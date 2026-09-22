<?php

namespace App\Http\Controllers;

use App\Models\BranchInventory;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->can('reports.view'), 403);
        [$from, $to] = $this->dates($request);
        $orders = $this->orders($from, $to)->get();
        $validOrders = $orders->whereNotIn('status', ['cancelled', 'refunded']);
        $lowStock = BranchInventory::query()->whereColumn('quantity', '<=', 'reorder_point')->count();
        $outOfStock = Product::query()->whereIn('status', ['sold_out', 'inactive'])->count();
        $profitability = Product::query()->orderBy('display_order')->get(['id', 'name', 'base_price', 'direct_cost', 'estimated_cost'])->map(function (Product $product): array {
            $price = (float) $product->base_price;
            $cost = (float) ($product->direct_cost > 0 ? $product->direct_cost : $product->estimated_cost);

            return ['id' => $product->id, 'name' => $product->name, 'price' => $price, 'cost' => $cost, 'margin' => $price > 0 ? (($price - $cost) / $price) * 100 : 0];
        });

        return Inertia::render('Reports/Index', [
            'filters' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
            'summary' => [
                'sales' => $validOrders->sum('total'),
                'orders' => $validOrders->count(),
                'average_ticket' => $validOrders->count() ? $validOrders->sum('total') / $validOrders->count() : 0,
                'low_stock' => $lowStock,
            ],
            'byBranch' => $orders->groupBy('branch.name')->map(fn ($branchOrders, $name) => ['name' => $name, 'orders' => $branchOrders->count(), 'sales' => $branchOrders->sum('total')])->values(),
            'byStatus' => $orders->groupBy('status')->map(fn ($statusOrders, $status) => ['status' => $status, 'count' => $statusOrders->count()])->values(),
            'reportRows' => [
                ['report' => 'Ventas', 'value' => (float) $validOrders->sum('total'), 'currency' => true],
                ['report' => 'Pedidos', 'value' => $validOrders->count(), 'currency' => false],
                ['report' => 'Inventario bajo', 'value' => $lowStock, 'currency' => false],
                ['report' => 'Productos agotados', 'value' => $outOfStock, 'currency' => false],
                ['report' => 'Diferencias de caja', 'value' => 0, 'currency' => false],
            ],
            'profitability' => $profitability,
        ]);
    }

    private function orders($from, $to)
    {
        return Order::query()->with('branch')->whereBetween('placed_at', [$from->startOfDay(), $to->endOfDay()]);
    }

    private function dates(Request $request): array
    {
        $from = now()->parse($request->input('from', now()->startOfMonth()->toDateString()));
        $to = now()->parse($request->input('to', now()->toDateString()));

        return [$from, $to];
    }
}
