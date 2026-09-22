<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecipeIngredientRequest;
use App\Http\Requests\StoreRecipeRequest;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\Recipe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RecipeController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->can('inventory.view'), 403);

        return Inertia::render('Recipes/Index', [
            'recipes' => Recipe::query()->with(['product', 'ingredients.item'])->latest()->get(),
            'products' => Product::query()->doesntHave('recipe')->where('is_active', true)->orderBy('name')->get(['id', 'name', 'sku', 'base_price']),
            'inventoryItems' => InventoryItem::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'code', 'unit', 'unit_cost']),
        ]);
    }

    public function store(StoreRecipeRequest $request): RedirectResponse
    {
        $recipe = Recipe::create($request->validated());
        $this->recalculate($recipe);

        return to_route('recipes.index')->with('success', 'Receta creada. Agrega los insumos para calcular el costo.');
    }

    public function storeIngredient(StoreRecipeIngredientRequest $request, Recipe $recipe): RedirectResponse
    {
        $recipe->ingredients()->create($request->validated());
        $this->recalculate($recipe);

        return to_route('recipes.index')->with('success', 'Ingrediente agregado y costo actualizado.');
    }

    private function recalculate(Recipe $recipe): void
    {
        $recipe->load(['ingredients.item', 'product']);
        $ingredientsCost = $recipe->ingredients->sum(fn ($ingredient) => (float) $ingredient->quantity * (float) $ingredient->item->unit_cost);
        $directCost = $ingredientsCost + (float) $recipe->packaging_cost + (float) $recipe->other_cost;
        $price = (float) $recipe->product->base_price;
        $grossProfit = $price - $directCost;
        $margin = $price > 0 ? ($grossProfit / $price) * 100 : 0;

        $recipe->product->update([
            'direct_cost' => $directCost,
            'estimated_cost' => $directCost,
            'gross_profit' => $grossProfit,
            'margin_percent' => $margin,
        ]);
    }
}
