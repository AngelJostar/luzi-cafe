<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateCatalogOrderRequest;
use App\Http\Requests\UpdateProductCategoriesRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->can('products.manage'), 403);

        return Inertia::render('Catalog/Index', [
            'categories' => Category::query()->orderBy('display_order')->get(['id', 'name', 'slug', 'display_order', 'is_active']),
            'branches' => Branch::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'products' => Product::query()->with(['category:id,name', 'categories:id,name', 'branches:id,name'])
                ->orderBy('display_order')
                ->get(['id', 'category_id', 'name', 'short_name', 'slug', 'sku', 'description', 'commercial_description', 'base_price', 'promo_price', 'estimated_cost', 'status', 'image_path', 'tags', 'estimated_prep_minutes', 'max_per_order', 'is_active', 'display_order']),
        ]);
    }

    public function storeCategory(StoreCategoryRequest $request): RedirectResponse
    {
        Category::query()->create([
            'name' => $request->string('name')->toString(),
            'slug' => $request->filled('slug') ? $request->string('slug')->toString() : Str::slug($request->string('name')->toString()),
            'display_order' => Category::query()->max('display_order') + 1,
        ]);

        return back();
    }

    public function storeProduct(StoreProductRequest $request): RedirectResponse
    {
        $product = Product::query()->create($this->productAttributes($request->validated(), Product::query()->max('display_order') + 1));

        $this->syncRelations($product, $request->validated());

        return back();
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($this->productAttributes($request->validated(), $product->display_order, $product));
        $this->syncRelations($product, $request->validated());

        return back();
    }

    public function duplicate(Request $request, Product $product): RedirectResponse
    {
        abort_unless($request->user()?->can('products.manage'), 403);

        $copy = $product->replicate(['sku', 'barcode', 'internal_code', 'slug']);
        $copy->name = $product->name.' copia';
        $copy->slug = Str::slug($copy->name).'-'.now()->format('Hisu');
        $copy->sku = $product->sku ? $product->sku.'-COPY-'.Str::upper(Str::random(4)) : null;
        $copy->display_order = Product::query()->max('display_order') + 1;
        $copy->save();
        $copy->categories()->sync($product->categories->mapWithKeys(fn (Category $category, int $index): array => [$category->id => ['display_order' => $index + 1]]));
        $copy->branches()->sync($product->branches->mapWithKeys(fn (Branch $branch): array => [$branch->id => ['price' => $branch->pivot->price, 'is_available' => $branch->pivot->is_available]]));

        return back();
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        abort_unless($request->user()?->can('products.manage'), 403);
        $product->delete();

        return back();
    }

    public function updateCategoryOrder(UpdateCatalogOrderRequest $request): RedirectResponse
    {
        $ids = $request->validated('ids');
        abort_unless(Category::query()->whereIn('id', $ids)->count() === count($ids), 422);

        foreach ($ids as $index => $id) {
            Category::query()->whereKey($id)->update(['display_order' => $index + 1]);
        }

        return back();
    }

    public function updateProductOrder(UpdateCatalogOrderRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $category = Category::query()->findOrFail($data['category_id']);
        $ids = $data['ids'];
        abort_unless($category->products()->whereIn('products.id', $ids)->count() === count($ids), 422);

        foreach ($ids as $index => $id) {
            $category->products()->updateExistingPivot($id, ['display_order' => $index + 1]);
        }

        return back();
    }

    public function updateProductCategories(UpdateProductCategoriesRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        $categoryIds = collect($data['category_ids'])->unique()->values();
        $product->update(['category_id' => $data['category_id']]);
        $product->categories()->sync($categoryIds->mapWithKeys(fn (int $categoryId, int $index): array => [$categoryId => ['display_order' => $index + 1]]));

        return back();
    }

    public function updateProductTags(Request $request, Product $product): RedirectResponse
    {
        abort_unless($request->user()?->can('products.manage'), 403);

        $tags = collect(explode(',', (string) $request->validate([
            'tags' => ['nullable', 'string', 'max:500'],
        ])['tags']))
            ->map(fn (string $tag) => Str::slug(trim($tag)))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $product->update(['tags' => $tags]);

        return back();
    }

    /** @param array<string, mixed> $data */
    private function productAttributes(array $data, int $displayOrder, ?Product $product = null): array
    {
        $baseSlug = Str::slug($data['name']);
        $slug = $baseSlug;
        $suffix = 2;

        while (Product::query()->where('slug', $slug)->when($product, fn ($query) => $query->where('id', '!=', $product->id))->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return [
            'category_id' => $data['category_id'], 'name' => $data['name'], 'short_name' => $data['short_name'] ?? null,
            'description' => $data['description'] ?? null, 'commercial_description' => $data['commercial_description'] ?? null,
            'slug' => $slug, 'sku' => ($data['sku'] ?? null) ?: null, 'barcode' => ($data['barcode'] ?? null) ?: null,
            'internal_code' => ($data['internal_code'] ?? null) ?: null, 'base_price' => $data['base_price'], 'promo_price' => $data['promo_price'] ?? null,
            'estimated_cost' => $data['estimated_cost'] ?? 0, 'status' => $data['status'], 'image_path' => $data['image_path'] ?? null,
            'tags' => collect(explode(',', $data['tags'] ?? ''))->map(fn (string $tag) => Str::slug(trim($tag)))->filter()->unique()->values()->all(),
            'estimated_prep_minutes' => $data['estimated_prep_minutes'] ?? 8, 'max_per_order' => $data['max_per_order'] ?? 12,
            'is_active' => $data['status'] === 'active', 'display_order' => $displayOrder,
        ];
    }

    /** @param array<string, mixed> $data */
    private function syncRelations(Product $product, array $data): void
    {
        $categoryIds = collect($data['category_ids'] ?? [])->push($product->category_id)->unique()->values();
        $product->categories()->sync($categoryIds->mapWithKeys(fn (int $categoryId, int $index): array => [$categoryId => ['display_order' => $index + 1]]));
        $product->branches()->sync(collect($data['branch_ids'] ?? [])->mapWithKeys(fn (int $branchId): array => [$branchId => ['is_available' => true]]));
    }
}
