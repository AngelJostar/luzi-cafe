<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdjustInventoryRequest;
use App\Http\Requests\StoreInventoryItemRequest;
use App\Http\Requests\StorePurchaseOrderRequest;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdatePurchaseOrderStatusRequest;
use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\BranchInventory;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class InventoryController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->can('inventory.view'), 403);

        return Inertia::render('Inventory/Index', [
            'branches' => Branch::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'items' => InventoryItem::query()->with('branchInventories.branch')->orderBy('name')->get(),
            'movements' => InventoryMovement::query()->with(['branch', 'item', 'user'])->latest()->limit(20)->get(),
            'suppliers' => Supplier::query()->orderBy('name')->get(),
            'purchaseOrders' => PurchaseOrder::query()->with(['supplier', 'branch', 'lines.item'])->latest()->get(),
        ]);
    }

    public function storeItem(StoreInventoryItemRequest $request): RedirectResponse
    {
        InventoryItem::create($request->validated());

        return to_route('inventory.index')->with('success', 'Insumo creado correctamente.');
    }

    public function adjust(AdjustInventoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $delta = $data['direction'] === 'in' ? (float) $data['quantity'] : -(float) $data['quantity'];

        DB::transaction(function () use ($data, $delta, $request): void {
            $inventory = BranchInventory::query()->firstOrCreate(
                ['branch_id' => $data['branch_id'], 'inventory_item_id' => $data['inventory_item_id']],
                ['quantity' => 0, 'reorder_point' => 0]
            );
            $before = (float) $inventory->quantity;
            $after = $before + $delta;

            if ($after < 0) {
                throw ValidationException::withMessages(['quantity' => 'La salida no puede dejar la existencia en negativo.']);
            }

            $inventory->update(['quantity' => $after]);
            InventoryMovement::create([
                'branch_id' => $data['branch_id'], 'inventory_item_id' => $data['inventory_item_id'],
                'user_id' => $request->user()->id, 'type' => $data['direction'], 'quantity_delta' => $delta,
                'quantity_before' => $before, 'quantity_after' => $after, 'reason' => $data['reason'], 'notes' => $data['notes'] ?? null,
            ]);
            AuditLog::create([
                'user_id' => $request->user()->id, 'event' => 'inventory.adjusted',
                'subject_type' => BranchInventory::class, 'subject_id' => $inventory->id,
                'metadata' => ['branch_id' => $data['branch_id'], 'inventory_item_id' => $data['inventory_item_id'], 'delta' => $delta, 'reason' => $data['reason']],
                'ip_address' => $request->ip(),
            ]);
        });

        return to_route('inventory.index')->with('success', 'Movimiento de inventario registrado.');
    }

    public function storeSupplier(StoreSupplierRequest $request): RedirectResponse
    {
        Supplier::create($request->validated());

        return to_route('inventory.index')->with('success', 'Proveedor creado correctamente.');
    }

    public function storePurchaseOrder(StorePurchaseOrderRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $total = round((float) $data['quantity'] * (float) $data['unit_price'], 2);

        DB::transaction(function () use ($data, $total, $request): void {
            $order = PurchaseOrder::create([
                'folio' => 'OC-'.now()->format('Ymd-His').'-'.random_int(10, 99),
                'supplier_id' => $data['supplier_id'], 'branch_id' => $data['branch_id'], 'requested_by' => $request->user()->id,
                'subtotal' => $total, 'total' => $total, 'expected_at' => $data['expected_at'] ?? null, 'notes' => $data['notes'] ?? null,
            ]);
            $order->lines()->create(['inventory_item_id' => $data['inventory_item_id'], 'quantity' => $data['quantity'], 'unit_price' => $data['unit_price'], 'total' => $total]);
        });

        return to_route('inventory.index')->with('success', 'Orden de compra creada.');
    }

    public function updatePurchaseOrderStatus(UpdatePurchaseOrderStatusRequest $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $status = $request->validated('status');
        abort_if(in_array($purchaseOrder->status, ['received', 'rejected', 'cancelled'], true), 422, 'La orden ya está cerrada.');

        DB::transaction(function () use ($purchaseOrder, $status, $request): void {
            if ($status === 'received') {
                foreach ($purchaseOrder->lines as $line) {
                    $inventory = BranchInventory::query()->lockForUpdate()->firstOrCreate(['branch_id' => $purchaseOrder->branch_id, 'inventory_item_id' => $line->inventory_item_id], ['quantity' => 0, 'reorder_point' => 0]);
                    $before = (float) $inventory->quantity;
                    $after = $before + (float) $line->quantity;
                    $inventory->update(['quantity' => $after]);
                    InventoryMovement::create(['branch_id' => $purchaseOrder->branch_id, 'inventory_item_id' => $line->inventory_item_id, 'user_id' => $request->user()->id, 'type' => 'in', 'quantity_delta' => $line->quantity, 'quantity_before' => $before, 'quantity_after' => $after, 'reason' => 'Recepción '.$purchaseOrder->folio]);
                }
                $purchaseOrder->update(['status' => 'received', 'received_at' => now(), 'payment_status' => $purchaseOrder->payment_status === 'pending' ? 'paid' : $purchaseOrder->payment_status]);

                return;
            }
            $purchaseOrder->update(['status' => $status, 'payment_status' => $status === 'paid' ? 'paid' : $purchaseOrder->payment_status]);
        });

        return to_route('inventory.index')->with('success', 'Estado de la orden actualizado.');
    }
}
