<?php

namespace App\Http\Controllers;

use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $featuredCategory = Category::query()
            ->withCount([
                'products as active_products_count' => fn ($query) => $query->where('products.active', true),
            ])
            ->orderByDesc('active_products_count')
            ->orderBy('name')
            ->first();

        $featuredProducts = collect();

        if ($featuredCategory && $featuredCategory->active_products_count > 0) {
            $featuredProducts = $featuredCategory->products()
                ->where('products.active', true)
                ->orderBy('products.name')
                ->limit(16)
                ->get(['products.id', 'products.name', 'products.price', 'products.image', 'products.stock']);
        }

        return view('principal', [
            'featuredCategory' => $featuredCategory,
            'featuredProducts' => $featuredProducts,
        ]);
    }
}
