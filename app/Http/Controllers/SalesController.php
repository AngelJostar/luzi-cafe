<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SalesController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->can('orders.view'), 403);

        $items = OrderItem::query()->with(['order.branch'])->latest('id')->limit(500)->get();
        $validItems = $items->filter(fn (OrderItem $item) => ! in_array($item->order->status, ['cancelled', 'refunded'], true));
        $sales = (float) $validItems->sum('total');
        $directCost = $validItems->sum(fn (OrderItem $item) => (float) $item->cost_snapshot * $item->quantity);
        $profit = $sales - $directCost;
        $orders = $validItems->pluck('order')->unique('id');
        $topBranch = $validItems->groupBy(fn (OrderItem $item) => $item->order->branch->name)->map->sum('total')->sortDesc()->keys()->first();
        $topProduct = $validItems->groupBy('product_name')->map->sum('quantity')->sortDesc()->keys()->first();

        return Inertia::render('Sales/Index', [
            'branches' => Branch::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'rows' => $items,
            'summary' => [
                'sales' => $sales,
                'items' => $validItems->sum('quantity'),
                'orders' => $orders->count(),
                'average_ticket' => $orders->count() ? $sales / $orders->count() : 0,
                'direct_cost' => $directCost,
                'profit' => $profit,
                'margin' => $sales > 0 ? ($profit / $sales) * 100 : 0,
                'net_profit' => $profit,
                'top_branch' => $topBranch ?: '—',
                'top_product' => $topProduct ?: '—',
            ],
        ]);
    }
}
