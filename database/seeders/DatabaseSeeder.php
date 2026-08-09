<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Favorite;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    protected const CATEGORY_PATH_SEPARATOR = ' > ';

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $catalogData = $this->catalogSeedData();
        $manualCatalogMode = $this->usingManualCatalog($catalogData);
        $users = $this->seedDemoUsers();

        if ($manualCatalogMode) {
            $this->resetCatalogDemoState();
        }

        $categories = $this->seedCategories($catalogData['categories'] ?? [], $manualCatalogMode);

        // Crear productos desde datos manuales o con fallback aleatorio
        $products = $this->seedProducts($catalogData['products'] ?? [], $categories, $manualCatalogMode);

        if ($products->isEmpty()) {
            return;
        }

        // Regenerar ordenes demo con el catalogo actual.
        OrderItem::query()->delete();
        Order::query()->delete();

        // Crear 15 órdenes
        $orders = Order::factory(15)
            ->recycle($users)
            ->create();

        // Crear items para esas ordenes usando productos ya existentes
        foreach ($orders as $order) {
            $selectedProducts = $products->random(rand(1, min(4, $products->count())));
            $subtotal = 0;

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 3);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                ]);
                $subtotal += $product->price * $quantity;
            }
            $order->update([
                'total_price' => round($subtotal * 1.1, 2),
            ]);
        }
    }

    protected function seedDemoUsers(): Collection
    {
        User::query()->updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'email_verified_at' => now(),
                'is_admin' => true,
            ]
        );

        $targetUserCount = 11;
        $missingUsers = max(0, $targetUserCount - User::query()->count());

        if ($missingUsers > 0) {
            User::factory($missingUsers)->create();
        }

        return User::query()->get();
    }

    protected function resetCatalogDemoState(): void
    {
        Favorite::query()->delete();
        OrderItem::query()->delete();
        Order::query()->delete();
        Product::query()->delete();
        Category::query()->delete();
    }

    protected function catalogSeedData(): array
    {
        $catalogData = require database_path('seeders/data/catalog.php');
        $externalProducts = $this->loadExternalProducts();

        if ($externalProducts !== null) {
            $catalogData['products'] = $externalProducts;
        }

        return $catalogData;
    }

    protected function loadExternalProducts(): ?array
    {
        $jsonPath = database_path('seeders/data/products.json');
        $csvPath = database_path('seeders/data/products.csv');

        if (is_file($jsonPath)) {
            return $this->loadProductsFromJson($jsonPath);
        }

        if (is_file($csvPath)) {
            return $this->loadProductsFromCsv($csvPath);
        }

        return null;
    }

    protected function loadProductsFromJson(string $path): array
    {
        $jsonContents = file_get_contents($path);

        if ($jsonContents === false) {
            throw new \RuntimeException("No se pudo leer el archivo JSON [{$path}].");
        }

        $jsonContents = preg_replace('/^\xEF\xBB\xBF/', '', $jsonContents) ?? $jsonContents;
        $decoded = json_decode($jsonContents, true);

        if (! is_array($decoded)) {
            throw new \InvalidArgumentException("El archivo JSON [{$path}] no tiene un formato valido.");
        }

        $products = $decoded['products'] ?? $decoded;

        if (! is_array($products)) {
            throw new \InvalidArgumentException("El archivo JSON [{$path}] debe contener un array de productos o una clave `products`.");
        }

        return collect($products)
            ->map(fn ($product) => $this->normalizeImportedProductRow((array) $product))
            ->all();
    }

    protected function loadProductsFromCsv(string $path): array
    {
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new \RuntimeException("No se pudo abrir el archivo CSV [{$path}].");
        }

        $header = fgetcsv($handle);

        if (! is_array($header)) {
            fclose($handle);

            return [];
        }

        $header = array_map('trim', $header);
        $products = [];

        while (($row = fgetcsv($handle)) !== false) {
            if ($row === [null] || $row === []) {
                continue;
            }

            $assocRow = [];

            foreach ($header as $index => $column) {
                $assocRow[$column] = $row[$index] ?? null;
            }

            $products[] = $this->normalizeImportedProductRow($assocRow);
        }

        fclose($handle);

        return $products;
    }

    protected function normalizeImportedProductRow(array $row): array
    {
        $normalized = [
            'name' => $this->normalizeNullableString($row['name'] ?? null),
            'description' => $this->normalizeNullableString($row['description'] ?? null),
            'price' => $this->normalizeNullableFloat($row['price'] ?? null),
            'stock' => $this->normalizeNullableInt($row['stock'] ?? null),
            'image' => $this->normalizeNullableString($row['image'] ?? null),
            'active' => $this->normalizeNullableBool($row['active'] ?? null),
            'height_meters' => $this->normalizeNullableFloat($row['height_meters'] ?? null),
            'habitat' => $this->normalizeNullableString($row['habitat'] ?? null),
            'diet' => $this->normalizeNullableString($row['diet'] ?? null),
            'era' => $this->normalizeNullableString($row['era'] ?? null),
        ];

        $categories = $row['categories'] ?? $row['category'] ?? [];

        if (is_string($categories)) {
            $categories = str_contains($categories, '|')
                ? explode('|', $categories)
                : explode(',', $categories);
        }

        $normalized['categories'] = collect((array) $categories)
            ->map(function ($category) {
                if (is_array($category)) {
                    return collect($category)
                        ->map(fn ($segment) => trim((string) $segment))
                        ->filter()
                        ->values()
                        ->all();
                }

                return trim((string) $category);
            })
            ->filter(function ($category) {
                if (is_array($category)) {
                    return $category !== [];
                }

                return $category !== '';
            })
            ->values()
            ->all();

        return array_filter(
            $normalized,
            fn ($value, $key) => $key === 'active' || $key === 'categories' || $value !== null,
            ARRAY_FILTER_USE_BOTH
        );
    }

    protected function normalizeNullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $stringValue = trim((string) $value);

        return $stringValue === '' ? null : $stringValue;
    }

    protected function normalizeNullableInt(mixed $value): ?int
    {
        $stringValue = $this->normalizeNullableString($value);

        return $stringValue === null ? null : (int) $stringValue;
    }

    protected function normalizeNullableFloat(mixed $value): ?float
    {
        $stringValue = $this->normalizeNullableString($value);

        return $stringValue === null ? null : (float) $stringValue;
    }

    protected function normalizeNullableBool(mixed $value): ?bool
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_bool($value)) {
            return $value;
        }

        $normalized = strtolower(trim((string) $value));

        return match ($normalized) {
            '1', 'true', 'yes', 'si', 'sí' => true,
            '0', 'false', 'no' => false,
            default => null,
        };
    }

    protected function usingManualCatalog(array $catalogData): bool
    {
        return ($catalogData['categories'] ?? []) !== []
            || ($catalogData['products'] ?? []) !== [];
    }

    protected function seedCategories(array $categoriesData, bool $manualCatalogMode): Collection
    {
        if ($categoriesData === []) {
            if ($manualCatalogMode) {
                return collect();
            }

            return Category::factory(5)->create();
        }

        $seededCategories = collect();

        foreach ($categoriesData as $categoryData) {
            $this->seedCategoryNode($categoryData, null, $seededCategories);
        }

        return $seededCategories;
    }

    protected function seedProducts(array $productsData, Collection $categories, bool $manualCatalogMode): Collection
    {
        if ($productsData === []) {
            if ($manualCatalogMode) {
                return collect();
            }

            return Product::factory(30)->create();
        }

        $categoriesById = $categories->keyBy('id');
        $categoriesByName = $categories->groupBy('name');
        $categoriesByPath = $this->buildCategoryPathMap($categories, $categoriesById);

        return collect($productsData)
            ->map(function (array $productData) use ($categoriesById, $categoriesByName, $categoriesByPath) {
                $productName = $productData['name'] ?? 'sin-nombre';
                $categoryNames = $productData['categories'] ?? [];

                if ($categoryNames === [] && ! empty($productData['category'])) {
                    $categoryNames = [$productData['category']];
                }

                $categoryIds = collect($categoryNames)
                    ->map(function ($categoryReference) use ($categoriesByName, $categoriesByPath, $productName) {
                        return $this->resolveCategoryReference(
                            $categoryReference,
                            $categoriesByName,
                            $categoriesByPath,
                            $productName,
                        )->id;
                    })
                    ->unique()
                    ->values();

                if ($categoryIds->isEmpty()) {
                    throw new \InvalidArgumentException("El producto manual [{$productName}] debe definir al menos una categoria en `categories`.");
                }

                $expandedCategoryIds = $this->expandCategoryIdsWithAncestors($categoryIds, $categoriesById);

                $attributes = $productData;
                unset($attributes['category']);
                unset($attributes['categories']);

                $attributes['category_id'] = $categoryIds->first();
                $attributes['active'] = $attributes['active'] ?? true;

                $product = Product::query()->updateOrCreate(
                    ['name' => $attributes['name']],
                    $attributes
                );

                $product->categories()->sync($expandedCategoryIds->all());

                return $product;
            });
    }

    protected function seedCategoryNode(array $categoryData, ?Category $parentCategory, Collection $seededCategories): void
    {
        $categoryName = trim((string) ($categoryData['name'] ?? ''));

        if ($categoryName === '') {
            $parentContext = $parentCategory?->name ? " dentro de [{$parentCategory->name}]" : '';

            throw new \InvalidArgumentException("Cada categoria del catalogo debe definir `name`{$parentContext}.");
        }

        $children = $categoryData['children'] ?? [];

        if ($children !== [] && ! is_array($children)) {
            throw new \InvalidArgumentException("La categoria [{$categoryName}] debe definir `children` como array.");
        }

        $category = Category::query()->updateOrCreate(
            [
                'name' => $categoryName,
                'parent_id' => $parentCategory?->id,
            ]
        );

        $seededCategories->push($category);

        foreach ($children as $childCategoryData) {
            $this->seedCategoryNode($childCategoryData, $category, $seededCategories);
        }
    }

    protected function expandCategoryIdsWithAncestors(Collection $categoryIds, Collection $categoriesById): Collection
    {
        $expandedCategoryIds = collect();

        foreach ($categoryIds as $categoryId) {
            $currentCategory = $categoriesById->get($categoryId);

            while ($currentCategory) {
                $expandedCategoryIds->push($currentCategory->id);

                $currentCategory = $currentCategory->parent_id !== null
                    ? $categoriesById->get($currentCategory->parent_id)
                    : null;
            }
        }

        return $expandedCategoryIds
            ->unique()
            ->values();
    }

    protected function buildCategoryPathMap(Collection $categories, Collection $categoriesById): Collection
    {
        return $categories
            ->mapWithKeys(function (Category $category) use ($categoriesById) {
                return [
                    $this->categoryPathString($category, $categoriesById) => $category,
                ];
            });
    }

    protected function categoryPathString(Category $category, Collection $categoriesById): string
    {
        $segments = [];
        $currentCategory = $category;

        while ($currentCategory) {
            array_unshift($segments, $currentCategory->name);

            $currentCategory = $currentCategory->parent_id !== null
                ? $categoriesById->get($currentCategory->parent_id)
                : null;
        }

        return implode(self::CATEGORY_PATH_SEPARATOR, $segments);
    }

    protected function normalizeCategoryPath(array|string $categoryReference): string
    {
        $segments = is_array($categoryReference)
            ? $categoryReference
            : (preg_split('/\s*>\s*/', trim($categoryReference)) ?: []);

        return collect($segments)
            ->map(fn ($segment) => trim((string) $segment))
            ->filter()
            ->implode(self::CATEGORY_PATH_SEPARATOR);
    }

    protected function resolveCategoryReference(
        mixed $categoryReference,
        Collection $categoriesByName,
        Collection $categoriesByPath,
        string $productName,
    ): Category {
        if (is_array($categoryReference)) {
            $normalizedPath = $this->normalizeCategoryPath($categoryReference);

            return $this->resolveCategoryByPath($normalizedPath, $categoriesByPath, $productName);
        }

        if (! is_string($categoryReference) || trim($categoryReference) === '') {
            throw new \InvalidArgumentException("El producto manual [{$productName}] contiene una referencia de categoria invalida.");
        }

        $normalizedReference = trim($categoryReference);

        if (str_contains($normalizedReference, '>')) {
            return $this->resolveCategoryByPath(
                $this->normalizeCategoryPath($normalizedReference),
                $categoriesByPath,
                $productName,
            );
        }

        $matchingCategories = $categoriesByName->get($normalizedReference, collect());

        if ($matchingCategories->count() === 1) {
            return $matchingCategories->sole();
        }

        if ($matchingCategories->isEmpty()) {
            throw new \InvalidArgumentException("La categoria manual [{$normalizedReference}] no existe para el producto [{$productName}] en database/seeders/data/catalog.php.");
        }

        $availablePaths = $matchingCategories
            ->map(function (Category $category) use ($categoriesByPath) {
                return $categoriesByPath
                    ->search(fn (Category $mappedCategory) => $mappedCategory->is($category));
            })
            ->filter()
            ->values()
            ->implode(', ');

        throw new \InvalidArgumentException(
            "La categoria manual [{$normalizedReference}] es ambigua para el producto [{$productName}]. Usa la ruta completa con `>`; por ejemplo: {$availablePaths}."
        );
    }

    protected function resolveCategoryByPath(string $categoryPath, Collection $categoriesByPath, string $productName): Category
    {
        $category = $categoriesByPath->get($categoryPath);

        if (! $category) {
            throw new \InvalidArgumentException("La ruta de categoria [{$categoryPath}] no existe para el producto [{$productName}] en database/seeders/data/catalog.php.");
        }

        return $category;
    }
}