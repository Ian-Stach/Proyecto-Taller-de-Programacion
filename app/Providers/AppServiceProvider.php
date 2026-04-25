<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Compartir categorías con todas las vistas
        View::composer('layouts.Jurassic_Store', function ($view) {
            $navCategories = Category::orderBy('name')->get(['id', 'name']);

            $cart = session()->get('cart', []);
            $sidebarCartItems = [];
            $sidebarCartSubtotal = 0;

            if (!empty($cart)) {
                $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');

                foreach ($cart as $productId => $quantity) {
                    $product = $products->get($productId);

                    if (!$product) {
                        continue; // Omitir productos no encontrados
                    }

                    $subtotal = $product->price * $quantity;
                    $sidebarCartItems[] = [
                        'product' => $product,
                        'quantity' => $quantity,
                        'subtotal' => $subtotal,
                    ];
                    $sidebarCartSubtotal += $subtotal;
                }
            }
            
            $view->with([
                'navCategories' => $navCategories,
                'sidebarCartItems' => $sidebarCartItems,
                'sidebarCartSubtotal' => $sidebarCartSubtotal,
            ]);
        });
    }
}
