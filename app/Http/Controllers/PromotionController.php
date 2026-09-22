<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePromotionRequest;
use App\Models\Promotion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PromotionController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->can('products.manage'), 403);

        return Inertia::render('Promotions/Index', [
            'promotions' => Promotion::query()->latest()->get(),
        ]);
    }

    public function store(StorePromotionRequest $request): RedirectResponse
    {
        Promotion::create($request->validated());

        return to_route('promotions.index')->with('success', 'Promoción creada correctamente.');
    }
}
