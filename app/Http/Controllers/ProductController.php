<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Listar todos los productos
     * GET /products
     */
    public function index(Request $request)
    {
        $query = Product::where('active', true);

        // Filtrar por categoría si se proporciona
        if ($request->has('category')) {
            $category = Category::where('name', $request->category)->firstOrFail();
            $query->where('category_id', $category->id);
        }

        $products = $query->paginate(12);
        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Mostrar detalle de un producto
     * GET /products/{id}
     */
    public function show(Product $product)
    {
        // Verificar si es activo
        if (!$product->active) {
            abort(404);
        }

        $categories = Category::all();
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('active', true)
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts', 'categories'));
    }
}
