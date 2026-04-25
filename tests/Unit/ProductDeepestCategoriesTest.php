<?php

use App\Models\Category;
use App\Models\Product;
use Tests\TestCase;

uses(TestCase::class);

function makeCategory(int $id, string $name, ?int $parentId): Category
{
    $category = new Category();
    $category->forceFill([
        'id' => $id,
        'name' => $name,
        'parent_id' => $parentId,
    ]);

    return $category;
}

it('returns only the deepest categories in the assigned branch', function () {
    $saurischia = makeCategory(1, 'Saurischia', null);
    $sauropodomorpha = makeCategory(2, 'Sauropodomorpha', 1);
    $anchisauria = makeCategory(3, 'Anchisauria', 2);

    $product = new Product();
    $product->setRelation('categories', collect([
        $saurischia,
        $sauropodomorpha,
        $anchisauria,
    ]));

    expect($product->deepestCategories()->pluck('name')->all())
        ->toBe(['Anchisauria']);
});

it('keeps separate deepest categories from different branches', function () {
    $saurischia = makeCategory(1, 'Saurischia', null);
    $theropoda = makeCategory(2, 'Theropoda', 1);
    $tetanurae = makeCategory(3, 'Tetanurae', 2);
    $sauropodomorpha = makeCategory(4, 'Sauropodomorpha', 1);
    $anchisauria = makeCategory(5, 'Anchisauria', 4);

    $product = new Product();
    $product->setRelation('categories', collect([
        $saurischia,
        $theropoda,
        $tetanurae,
        $sauropodomorpha,
        $anchisauria,
    ]));

    expect($product->deepestCategories()->pluck('name')->all())
        ->toBe(['Tetanurae', 'Anchisauria']);
});