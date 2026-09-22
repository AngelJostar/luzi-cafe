<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $branchId = $request->integer('branch_id');

        $products = Product::query()
            ->with(['category:id,name,slug', 'categories:id,name,slug'])
            ->where('is_active', true)
            ->when($branchId, fn ($query) => $query->whereHas('branches', fn ($branchQuery) => $branchQuery->where('branches.id', $branchId)->where('is_available', true)))
            ->orderBy('display_order')
            ->get();

        return response()->json([
            'categories' => Category::query()->where('is_active', true)->orderBy('display_order')->get(),
            'products' => $products,
        ]);
    }
}
