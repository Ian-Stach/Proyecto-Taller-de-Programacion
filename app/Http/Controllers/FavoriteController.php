<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Agregar producto a favoritos
     * POST /favorites/{product_id}
     */
    public function store(Product $product)
    {
        // Verificar si ya está en favoritos
        $favorite = Favorite::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($favorite) {
            return back()->with('info', 'Este producto ya está en favoritos');
        }

        // Crear favorito
        Favorite::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id
        ]);

        return back()->with('success', "{$product->name} agregado a favoritos!");
    }

    /**
     * Remover producto de favoritos
     * DELETE /favorites/{product_id}
     */
    public function destroy(Product $product)
    {
        Favorite::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->delete();

        return back()->with('success', "{$product->name} removido de favoritos");
    }

    /**
     * Listar favoritos del usuario
     * GET /favorites (opcional)
     */
    public function index()
    {
        $favorites = Auth::user()->favorites()
            ->with('product')
            ->paginate(12);

        $categories = \App\Models\Category::all();

        return view('favorites.index', compact('favorites', 'categories'));
    }
}
