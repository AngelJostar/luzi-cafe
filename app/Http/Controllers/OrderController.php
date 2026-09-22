<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\AuditLog;
use App\Models\BranchInventory;
use App\Models\InventoryMovement;
use App\Models\OperationalNotification;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->can('orders.view'), 403);

        return Inertia::render('Orders/Index', [
            'orders' => Order::query()->with(['branch', 'items'])->latest('placed_at')->latest('id')->limit(100)->get(),
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $nextStatus = $request->string('status')->toString();
        $allowed = [
            'pending' => ['preparing', 'cancelled'],
            'preparing' => ['ready', 'cancelled'],
            'ready' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];
        if (! in_array($nextStatus, $allowed[$order->status] ?? [], true)) {
            throw ValidationException::withMessages(['status' => 'La transición de estado no es válida.']);
        }
        $previousStatus = $order->status;

        DB::transaction(function () use ($order, $nextStatus, $previousStatus, $request): void {
            if ($nextStatus === 'cancelled') {
                $this->restoreInventory($order, $request->user()->id);
            }
            $order->update(['status' => $nextStatus]);
            OperationalNotification::create([
                'branch_id' => $order->branch_id, 'type' => 'order_status',
                'title' => "Pedido {$order->folio}: {$nextStatus}",
                'body' => "El pedido cambió de {$previousStatus} a {$nextStatus}.",
                'subject_type' => Order::class, 'subject_id' => $order->id,
            ]);
            AuditLog::create([
                'user_id' => $request->user()->id, 'event' => 'order.status_changed',
                'subject_type' => Order::class, 'subject_id' => $order->id,
                'metadata' => ['folio' => $order->folio, 'from' => $previousStatus, 'to' => $nextStatus],
                'ip_address' => $request->ip(),
            ]);
        });

        return to_route('orders.index')->with('success', 'Estado del pedido actualizado.');
    }

    private function restoreInventory(Order $order, int $userId): void
    {
        if ($order->inventory_reversed_at) {
            return;
        }
        $order->load('items.ingredients.item');
        foreach ($order->items as $item) {
            foreach ($item->ingredients as $ingredient) {
                $inventory = BranchInventory::query()->lockForUpdate()->firstOrCreate(
                    ['branch_id' => $order->branch_id, 'inventory_item_id' => $ingredient->inventory_item_id],
                    ['quantity' => 0, 'reorder_point' => 0]
                );
                $before = (float) $inventory->quantity;
                $after = $before + (float) $ingredient->quantity_consumed;
                $inventory->update(['quantity' => $after]);
                InventoryMovement::create([
                    'branch_id' => $order->branch_id, 'inventory_item_id' => $ingredient->inventory_item_id, 'user_id' => $userId,
                    'type' => 'reversal', 'quantity_delta' => $ingredient->quantity_consumed, 'quantity_before' => $before, 'quantity_after' => $after,
                    'reason' => "Reversión por cancelación {$order->folio}", 'notes' => "Producto: {$item->product_name}",
                ]);
            }
        }
        $order->update(['inventory_reversed_at' => now()]);
    }
}
