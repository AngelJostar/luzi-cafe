<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileOrderHistoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::query()
            ->with(['branch:id,name,phone,address', 'items:id,order_id,product_name,product_sku,quantity,unit_price,total,modifiers,notes'])
            ->where('user_id', $request->user()->id)
            ->latest('placed_at')
            ->latest('id')
            ->paginate(20);

        return response()->json($orders);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        return response()->json([
            'data' => $order->load(['branch:id,name,phone,address,schedule', 'items.ingredients.item:id,name,unit']),
        ]);
    }
}
