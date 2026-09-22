<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        abort_unless($request->user()?->can('branches.view'), 403);

        return Inertia::render('Dashboard', [
            'branches' => Branch::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'metrics' => [
                'branches' => Branch::query()->count(),
                'activeBranches' => Branch::query()->where('is_active', true)->count(),
                'categories' => Category::query()->count(),
                'products' => Product::query()->count(),
                'activeProducts' => Product::query()->where('is_active', true)->count(),
            ],
        ]);
    }
}
