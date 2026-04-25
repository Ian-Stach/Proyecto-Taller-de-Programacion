<?php

use App\Models\Category;
use App\Models\Product;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

uses(RefreshDatabase::class);

it('seeds category trees and expands ancestor category assignments', function () {
    $seeder = new class extends DatabaseSeeder
    {
        public function seedCategoriesForTest(array $categoriesData): Collection
        {
            return $this->seedCategories($categoriesData, true);
        }

        public function seedProductsForTest(array $productsData, Collection $categories): Collection
        {
            return $this->seedProducts($productsData, $categories, true);
        }
    };

    $categories = $seeder->seedCategoriesForTest([
        [
            'name' => 'Saurischia',
            'children' => [
                [
                    'name' => 'Sauropodomorpha',
                    'children' => [
                        ['name' => 'Anchisauria'],
                    ],
                ],
            ],
        ],
    ]);

    $products = $seeder->seedProductsForTest([
        [
            'categories' => ['Anchisauria'],
            'name' => 'Aardonyx',
            'description' => 'Sauropodomorfo basal.',
            'price' => 149.90,
            'stock' => 12,
            'height_meters' => 2.00,
            'habitat' => 'terrestre',
            'diet' => 'herbivoro',
            'era' => 'jurasico',
        ],
    ], $categories);

    $saurischia = Category::query()->where('name', 'Saurischia')->firstOrFail();
    $sauropodomorpha = Category::query()->where('name', 'Sauropodomorpha')->firstOrFail();
    $anchisauria = Category::query()->where('name', 'Anchisauria')->firstOrFail();

    expect($saurischia->parent_id)->toBeNull();
    expect($sauropodomorpha->parent_id)->toBe($saurischia->id);
    expect($anchisauria->parent_id)->toBe($sauropodomorpha->id);

    $product = $products->sole()->load('categories');

    expect($product->category_id)->toBe($anchisauria->id);
    expect($product->categories->pluck('name')->all())
        ->toBe(['Anchisauria', 'Sauropodomorpha', 'Saurischia']);
    expect($product->deepestCategories()->pluck('name')->all())
        ->toBe(['Anchisauria']);
});

it('allows duplicate category names in different branches when products use full paths', function () {
    $seeder = new class extends DatabaseSeeder
    {
        public function seedCategoriesForTest(array $categoriesData): Collection
        {
            return $this->seedCategories($categoriesData, true);
        }

        public function seedProductsForTest(array $productsData, Collection $categories): Collection
        {
            return $this->seedProducts($productsData, $categories, true);
        }
    };

    $categories = $seeder->seedCategoriesForTest([
        [
            'name' => 'Saurischia',
            'children' => [
                [
                    'name' => 'Theropoda',
                    'children' => [
                        ['name' => 'Abelisauridae'],
                    ],
                ],
            ],
        ],
        [
            'name' => 'Ornithischia',
            'children' => [
                [
                    'name' => 'Thyreophora',
                    'children' => [
                        ['name' => 'Abelisauridae'],
                    ],
                ],
            ],
        ],
    ]);

    $products = $seeder->seedProductsForTest([
        [
            'categories' => ['Saurischia > Theropoda > Abelisauridae'],
            'name' => 'Majungasaurus',
            'description' => 'Abelisáurido del Cretácico.',
            'price' => 210.00,
            'stock' => 5,
            'height_meters' => 6.50,
            'habitat' => 'terrestre',
            'diet' => 'carnivoro',
            'era' => 'cretacico',
        ],
    ], $categories);

    expect(Category::query()->where('name', 'Abelisauridae')->count())->toBe(2);

    $product = $products->sole()->load('categories');

    expect($product->categories->pluck('name')->all())
        ->toBe(['Abelisauridae', 'Theropoda', 'Saurischia']);
    expect($product->deepestCategories()->pluck('name')->all())
        ->toBe(['Abelisauridae']);
});

it('rejects ambiguous category names without a full path', function () {
    $seeder = new class extends DatabaseSeeder
    {
        public function seedCategoriesForTest(array $categoriesData): Collection
        {
            return $this->seedCategories($categoriesData, true);
        }

        public function seedProductsForTest(array $productsData, Collection $categories): Collection
        {
            return $this->seedProducts($productsData, $categories, true);
        }
    };

    $categories = $seeder->seedCategoriesForTest([
        [
            'name' => 'Saurischia',
            'children' => [
                [
                    'name' => 'Theropoda',
                    'children' => [
                        ['name' => 'Abelisauridae'],
                    ],
                ],
            ],
        ],
        [
            'name' => 'Ornithischia',
            'children' => [
                [
                    'name' => 'Thyreophora',
                    'children' => [
                        ['name' => 'Abelisauridae'],
                    ],
                ],
            ],
        ],
    ]);

    expect(fn () => $seeder->seedProductsForTest([
        [
            'categories' => ['Abelisauridae'],
            'name' => 'Producto ambiguo',
            'description' => 'Debe fallar por ambigüedad.',
            'price' => 100.00,
            'stock' => 1,
            'height_meters' => 1.00,
            'habitat' => 'terrestre',
            'diet' => 'carnivoro',
            'era' => 'cretacico',
        ],
    ], $categories))->toThrow(InvalidArgumentException::class, 'es ambigua');
});