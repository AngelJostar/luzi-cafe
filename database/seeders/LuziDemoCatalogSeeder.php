<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LuziDemoCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            ['code' => 'LZ-01', 'name' => 'Luzi Barra Centro', 'zone' => 'centro', 'address' => 'Barra principal Luzi', 'schedule' => 'Lun-Dom 7:00 a 21:00', 'estimated_prep_minutes' => 15],
            ['code' => 'LZ-02', 'name' => 'Luzi Norte Express', 'zone' => 'norte', 'address' => 'Ventana express zona norte', 'schedule' => 'Lun-Sab 8:00 a 20:00', 'estimated_prep_minutes' => 10],
            ['code' => 'LZ-03', 'name' => 'Luzi Poniente Oficinas', 'zone' => 'poniente', 'address' => 'Entrega en lobby corporativo', 'schedule' => 'Lun-Vie 8:00 a 18:00', 'estimated_prep_minutes' => 18],
            ['code' => 'LZ-04', 'name' => 'Luzi Sur Plaza', 'zone' => 'sur', 'address' => 'Módulo pickup plaza sur', 'schedule' => 'Lun-Dom 8:00 a 22:00', 'estimated_prep_minutes' => 14],
            ['code' => 'LZ-05', 'name' => 'Luzi Oriente Mercado', 'zone' => 'oriente', 'address' => 'Punto Luzi mercado oriente', 'schedule' => 'Lun-Dom 7:30 a 20:30', 'estimated_prep_minutes' => 16],
        ];

        $branchModels = collect($branches)->map(function (array $branch) {
            return Branch::query()->updateOrCreate(['code' => $branch['code']], [...$branch, 'is_active' => true, 'allow_scheduled_orders' => true]);
        });

        $categoryNames = ['Caliente', 'Frío', 'Postrecito', 'Frappes', 'Temporada', 'Combos', 'Clásicos'];
        $categories = collect($categoryNames)->mapWithKeys(function (string $name, int $index) {
            $category = Category::query()->updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'display_order' => $index + 1, 'is_active' => true],
            );

            return [$category->slug => $category];
        });

        // Productos, precios e imágenes tomados del catálogo demo de la app original Luzi.
        $products = [
            ['Café de olla', 'temporada', 39, 'cafe-olla.png', ['temporada', 'clasicos'], 'Canela, piloncillo y café suave para arrancar con sabor de casa.'],
            ['Latte', 'caliente', 42, 'latte-hot.png', ['caliente', 'clasicos'], 'Espresso con leche cremosa y final redondo.'],
            ['Cappuccino', 'caliente', 42, 'cappuccino-hot.png', ['caliente', 'clasicos'], 'Espresso intenso con leche vaporizada y espuma suave.'],
            ['Cold brew calle', 'frio', 56, 'cold-brew.png', ['frio', 'temporada'], 'Café frío intenso, bajo en acidez y listo para seguirle.'],
            ['Matcha lima', 'frio', 62, 'matcha-latte.png', ['frio', 'temporada'], 'Matcha cremoso con vibra fresca, natural y muy Luzi.'],
            ['Café frío listo', 'frio', 42, 'rtd-cafe-frio.png', ['frio', 'clasicos'], 'Botella fría para llevar, abrir y seguir con el día.'],
            ['Moka', 'caliente', 46, 'moka-hot.png', ['caliente', 'temporada'], 'Chocolate, espresso y leche cremosa.'],
            ['Frappe maíz dulce', 'frio', 68, 'vaso-frio.png', ['frio', 'temporada'], 'Frío, cremoso, dulce y alegre. Puro antojo mexicano.'],
            ['Frappe caramelo', 'frappes', 62, 'frappes-ref-caramelo.png', ['frappes'], 'Café, leche, caramelo y crema batida.'],
            ['Frappe cookies & cream', 'frappes', 64, 'frappes-ref-cookies.png', ['frappes'], 'Café, galleta y crema. El clásico que nunca falla.'],
            ['Frappe mocha', 'frappes', 62, 'frappes-ref-mocha.png', ['frappes'], 'Café, chocolate y leche.'],
            ['Frappe matcha', 'frappes', 66, 'frappes-ref-matcha.png', ['frappes'], 'Matcha premium, leche y un toque de vainilla.'],
            ['Frappe frutos rojos', 'frappes', 63, 'frappes-ref-frutos-rojos.png', ['frappes'], 'Mezcla de frutos rojos, yogurt y un toque de vainilla.'],
            ['Frappe horchata', 'frappes', 60, 'frappes-ref-horchata.png', ['frappes'], 'Horchata cremosa con café. Tradición que refresca.'],
            ['Frappe choco avellana', 'frappes', 65, 'frappes-ref-choco-avellana.png', ['frappes'], 'Chocolate, avellana y café. Suave, cremoso y adictivo.'],
            ['Pastel de chocolate', 'postrecito', 62, 'moka-hot.png', ['postrecito'], 'Húmedo, intenso y cubierto de chocolate.'],
            ['Cheesecake de fresa', 'postrecito', 66, 'caramelo-hot.png', ['postrecito'], 'Suave cheesecake con topping de fresa natural.'],
            ['Tiramisú', 'postrecito', 64, 'latte-hot.png', ['postrecito'], 'Clásico italiano con café, queso mascarpone y cacao.'],
            ['Flan casero', 'postrecito', 48, 'cafe-olla.png', ['postrecito'], 'Suave, cremoso y con caramelo hecho en casa.'],
            ['Mousse de chocolate', 'postrecito', 56, 'cold-brew.png', ['postrecito'], 'Ligero, aireado y lleno de chocolate.'],
            ['Pastel 3 leches', 'postrecito', 58, 'cappuccino-hot.png', ['postrecito'], 'Esponjoso, húmedo y con el toque dulce que te encanta.'],
            ['Brownie con helado', 'postrecito', 72, 'vaso-frio.png', ['postrecito'], 'Brownie tibio con helado y salsa de chocolate.'],
            ['Americano', 'caliente', 29, 'americano-hot.png', ['caliente', 'clasicos'], 'Espresso con agua caliente, limpio y balanceado.'],
            ['Caramelo Macchiato', 'caliente', 50, 'caramelo-hot.png', ['caliente', 'temporada'], 'Espresso con leche cremosa y caramelo.'],
            ['Café intenso', 'temporada', 52, 'cappuccino-hot.png', ['temporada'], 'Perfil potente para los que van al 100.'],
            ['Combo despierta', 'combos', 79, 'charola-luzi.png', ['combos'], 'Café mediano más pan dulce.'],
            ['Combo oficina', 'combos', 219, 'charola-luzi.png', ['combos'], 'Cuatro bebidas en charola para junta o equipo.'],
            ['Combo reunión', 'combos', 329, 'charola-luzi.png', ['combos'], 'Tres cafés más tres postres.'],
            ['Combo para compartir', 'combos', 299, 'charola-luzi.png', ['combos'], 'Café familiar más seis postrecitos.'],
            ['Combo completo', 'combos', 189, 'promo-home-luzi-pinguino.png', ['combos'], 'Café, sándwich y postrecito.'],
            ['Combo dulce pausa', 'combos', 109, 'charola-luzi.png', ['combos'], 'Café mediano más dos mini postres.'],
            ['Café molido clásico', 'clasicos', 129, 'bolsa-clasico.png', ['clasicos'], 'Café molido Luzi para seguirle también en casa.'],
            ['Café molido intenso', 'clasicos', 149, 'bolsa-intenso.png', ['clasicos'], 'Bolsa premium de perfil intenso para casa u oficina.'],
        ];

        foreach ($products as $index => [$name, $primaryCategory, $price, $image, $productCategories, $description]) {
            $slug = Str::slug($name);
            $product = Product::query()->updateOrCreate(
                ['sku' => sprintf('LUZI-%04d', $index + 1)],
                [
                    'category_id' => $categories[$primaryCategory]->id,
                    'name' => $name,
                    'short_name' => $name,
                    'slug' => $slug,
                    'internal_code' => sprintf('LUZI-%04d', $index + 1),
                    'description' => $description,
                    'commercial_description' => $description,
                    'base_price' => $price,
                    'estimated_cost' => round($price * 0.38, 2),
                    'status' => 'active',
                    'image_path' => '/images/demo/'.$image,
                    'tags' => $this->mobileTags($name, $primaryCategory),
                    'estimated_prep_minutes' => str_contains($name, 'Combo') ? 12 : 8,
                    'max_per_order' => 12,
                    'is_active' => true,
                    'display_order' => $index + 1,
                ],
            );

            $product->categories()->sync(collect($productCategories)->mapWithKeys(fn (string $category, int $order) => [
                $categories[$category]->id => ['display_order' => $order + 1],
            ])->all());

            $product->branches()->syncWithoutDetaching($branchModels->mapWithKeys(fn (Branch $branch) => [
                $branch->id => ['price' => $price, 'is_available' => true],
            ])->all());
        }
    }

    /** @return array<int, string> */
    private function mobileTags(string $name, string $category): array
    {
        $tags = match ($category) {
            'caliente' => ['cafe'],
            'frio', 'frappes' => ['frio'],
            default => [],
        };

        if (in_array($name, ['Americano', 'Latte', 'Cappuccino', 'CafÃ© de olla'], true)) {
            $tags[] = 'clasicos';
        }

        if (in_array($name, ['Moka', 'Caramelo Macchiato'], true)) {
            $tags[] = 'especiales';
        }

        if (str_contains($name, 'Matcha')) {
            $tags[] = 'sin-cafe';
        }

        return $tags;
    }
}
