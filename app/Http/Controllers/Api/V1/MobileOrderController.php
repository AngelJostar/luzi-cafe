<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMobileOrderRequest;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\BranchInventory;
use App\Models\InventoryMovement;
use App\Models\OperationalNotification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MobileOrderController extends Controller
{
    public function store(StoreMobileOrderRequest $request): JsonResponse
    {
        $data = $request->validated();
        $branch = Branch::query()->where('is_active', true)->findOrFail($data['branch_id']);

        $order = DB::transaction(function () use ($data, $branch, $request): Order {
            $products = Product::query()
                ->with(['recipe.ingredients.item', 'branches'])
                ->whereIn('id', collect($data['items'])->pluck('product_id'))
                ->where('is_active', true)
                ->get()
                ->keyBy('id');

            $this->validateProducts($data['items'], $products, $branch);
            $folio = $this->nextFolio($branch);
            $subtotal = collect($data['items'])->sum(fn (array $item) => (float) $products[$item['product_id']]->base_price * $item['quantity']);
            $promotion = $this->promotionFor($data['promotion_code'] ?? null, $subtotal);
            $discount = $promotion ? $this->discountFor($promotion, $subtotal) : 0;
            $order = Order::create([
                'folio' => $folio, 'branch_id' => $branch->id, 'user_id' => $request->user()->id,
                'customer_name' => $request->user()->name, 'customer_email' => $request->user()->email,
                'channel' => 'mobile', 'fulfillment_type' => $data['fulfillment_type'], 'status' => 'pending',
                'payment_status' => 'pending', 'payment_method' => $data['payment_method'], 'promotion_code' => $promotion?->code,
                'subtotal' => $subtotal, 'discount_total' => $discount, 'total' => $subtotal - $discount, 'notes' => $data['notes'] ?? null, 'placed_at' => now(),
            ]);

            if ($promotion) {
                $promotion->increment('used_count');
            }

            foreach ($data['items'] as $line) {
                $product = $products[$line['product_id']];
                $quantity = (int) $line['quantity'];
                $unitPrice = (float) $product->base_price;
                $orderItem = $order->items()->create([
                    'product_id' => $product->id, 'product_name' => $product->name, 'product_sku' => $product->sku,
                    'category_name' => $product->category?->name, 'quantity' => $quantity, 'unit_price' => $unitPrice,
                    'total' => $unitPrice * $quantity, 'cost_snapshot' => $product->direct_cost,
                    'modifiers' => $line['modifiers'] ?? [], 'notes' => $line['notes'] ?? null,
                ]);
                $this->consumeRecipe($branch, $product, $quantity, $order, $orderItem, $request->user()->id);
            }

            OperationalNotification::create([
                'branch_id' => $branch->id, 'type' => 'new_order', 'title' => "Nuevo pedido {$order->folio}",
                'body' => 'Pedido móvil por $'.number_format((float) $order->total, 2).' pendiente de preparación.',
                'subject_type' => Order::class, 'subject_id' => $order->id,
            ]);
            AuditLog::create([
                'user_id' => $request->user()->id, 'event' => 'mobile_order.created',
                'subject_type' => Order::class, 'subject_id' => $order->id,
                'metadata' => ['folio' => $order->folio, 'branch_id' => $branch->id, 'total' => $order->total],
                'ip_address' => $request->ip(),
            ]);

            return $order->load('items');
        });

        return response()->json(['data' => $order], 201);
    }

    private function validateProducts(array $lines, $products, Branch $branch): void
    {
        foreach ($lines as $line) {
            $product = $products->get($line['product_id']);
            if (! $product) {
                throw ValidationException::withMessages(['items' => 'Uno de los productos ya no está disponible.']);
            }
            $availability = $product->branches->firstWhere('id', $branch->id);
            if ($product->branches->isNotEmpty() && (! $availability || ! $availability->pivot->is_available)) {
                throw ValidationException::withMessages(['items' => "{$product->name} no está disponible en esta sucursal."]);
            }
        }
    }

    private function consumeRecipe(Branch $branch, Product $product, int $quantity, Order $order, OrderItem $orderItem, int $userId): void
    {
        if (! $product->recipe?->is_active) {
            return;
        }
        foreach ($product->recipe->ingredients as $ingredient) {
            $consumed = (float) $ingredient->quantity * $quantity;
            $orderItem->ingredients()->create([
                'inventory_item_id' => $ingredient->inventory_item_id,
                'quantity_per_product' => $ingredient->quantity,
                'quantity_consumed' => $consumed,
            ]);
            $inventory = BranchInventory::query()->lockForUpdate()->firstOrCreate(
                ['branch_id' => $branch->id, 'inventory_item_id' => $ingredient->inventory_item_id],
                ['quantity' => 0, 'reorder_point' => 0]
            );
            $before = (float) $inventory->quantity;
            $delta = -$consumed;
            $after = $before + $delta;
            if ($after < 0) {
                throw ValidationException::withMessages(['items' => "Existencia insuficiente de {$ingredient->item->name} en {$branch->name}."]);
            }
            $inventory->update(['quantity' => $after]);
            InventoryMovement::create([
                'branch_id' => $branch->id, 'inventory_item_id' => $ingredient->inventory_item_id, 'user_id' => $userId,
                'type' => 'sale', 'quantity_delta' => $delta, 'quantity_before' => $before, 'quantity_after' => $after,
                'reason' => "Consumo por pedido {$order->folio}", 'notes' => "Producto: {$product->name}",
            ]);
        }
    }

    private function nextFolio(Branch $branch): string
    {
        $sequence = Order::query()->where('branch_id', $branch->id)->lockForUpdate()->count() + 1;

        return sprintf('%s-%s-%06d', $branch->code, now()->format('Y'), $sequence);
    }

    private function promotionFor(?string $code, float $subtotal): ?Promotion
    {
        if (! $code) {
            return null;
        }
        $promotion = Promotion::query()->where('code', strtoupper($code))->lockForUpdate()->first();
        if (! $promotion || ! $promotion->is_active || ($promotion->starts_at && $promotion->starts_at->isFuture()) || ($promotion->ends_at && $promotion->ends_at->isPast()) || ($promotion->usage_limit && $promotion->used_count >= $promotion->usage_limit)) {
            throw ValidationException::withMessages(['promotion_code' => 'La promoción no está disponible.']);
        }
        if ($subtotal < (float) $promotion->minimum_order_amount) {
            throw ValidationException::withMessages(['promotion_code' => 'No se alcanza el monto mínimo para esta promoción.']);
        }

        return $promotion;
    }

    private function discountFor(Promotion $promotion, float $subtotal): float
    {
        $discount = $promotion->type === 'percent' ? $subtotal * ((float) $promotion->amount / 100) : (float) $promotion->amount;
        if ($promotion->maximum_discount) {
            $discount = min($discount, (float) $promotion->maximum_discount);
        }

        return min($discount, $subtotal);
    }
}
