<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->can('users.manage'), 403);

        $customers = User::role('cliente')->with('preferredBranch:id,name')->orderByDesc('id')->get(['id', 'name', 'email', 'phone', 'preferred_branch_id', 'loyalty_points', 'is_blocked', 'created_at']);
        $orders = Order::query()->with(['branch:id,name', 'items:id,order_id,product_name'])->whereIn('user_id', $customers->pluck('id'))->latest('placed_at')->get();

        $rows = $customers->map(function (User $user) use ($orders): array {
            $customerOrders = $orders->where('user_id', $user->id);
            $validOrders = $customerOrders->reject(fn (Order $order): bool => in_array($order->status, ['cancelled', 'refunded'], true));
            $lastOrder = $customerOrders->first();
            $lastMonth = now()->subMonth();
            $lastTwelveMonths = now()->subMonths(12);
            $total = (float) $validOrders->sum('total');
            $monthlyOrders = $validOrders->filter(fn (Order $order): bool => $order->placed_at?->gte($lastTwelveMonths) ?? false);
            $preferredBranch = $user->preferredBranch?->name ?? $validOrders->groupBy('branch_id')->sortByDesc(fn (Collection $group): int => $group->count())->first()?->first()?->branch?->name;
            $products = $validOrders->flatMap(fn (Order $order) => $order->items)->countBy('product_name')->sortDesc()->take(3)->keys()->implode(', ');

            return [
                'id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'phone' => $user->phone ?? $lastOrder?->customer_phone,
                'created_at' => $user->created_at, 'orders_count' => $validOrders->count(), 'total_spent' => $total,
                'last_month_spent' => (float) $validOrders->filter(fn (Order $order): bool => $order->placed_at?->gte($lastMonth) ?? false)->sum('total'),
                'monthly_average' => $monthlyOrders->isEmpty() ? 0 : (float) $monthlyOrders->sum('total') / 12,
                'average_ticket' => $validOrders->isEmpty() ? 0 : $total / $validOrders->count(),
                'last_payment' => $lastOrder?->payment_method, 'payment_methods' => $validOrders->pluck('payment_method')->filter()->unique()->take(3)->values(),
                'last_orders' => $validOrders->take(3)->pluck('folio')->values(), 'preferred_branch' => $preferredBranch,
                'frequent_products' => $products, 'loyalty_points' => $user->loyalty_points, 'is_blocked' => $user->is_blocked,
            ];
        });

        return Inertia::render('Customers/Index', [
            'customers' => $rows,
            'metrics' => [
                'total' => $rows->count(),
                'month' => $rows->filter(fn (array $row): bool => $row['last_month_spent'] > 0)->count(),
                'today' => $orders->where('placed_at', '>=', now()->startOfDay())->pluck('user_id')->unique()->count(),
            ],
        ]);
    }
}
