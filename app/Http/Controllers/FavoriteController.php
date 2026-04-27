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
     * POST /favorites/{product}
     */
    public function store(Request $request, Product $product)
    {
        // Verificar si ya está en favoritos
        $favorite = Favorite::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($favorite) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'favorited' => true,
                    'product_id' => $product->id,
                    'message' => 'Este producto ya está en favoritos',
                    'urls' => [
                        'add' => route('favorites.add', $product),
                        'remove' => route('favorites.remove', $product),
                    ],
                ]);
            }

            return back()->with('info', 'Este producto ya está en favoritos');
        }

        // Crear favorito
        Favorite::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'favorited' => true,
                'product_id' => $product->id,
                'message' => "{$product->name} agregado a favoritos!",
                'urls' => [
                    'add' => route('favorites.add', $product),
                    'remove' => route('favorites.remove', $product),
                ],
            ]);
        }

        return back()->with('success', "{$product->name} agregado a favoritos!");
    }

    /**
     * Remover producto de favoritos
     * DELETE /favorites/{product}
     */
    public function destroy(Request $request, Product $product)
    {
        Favorite::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'favorited' => false,
                'product_id' => $product->id,
                'message' => "{$product->name} removido de favoritos",
                'urls' => [
                    'add' => route('favorites.add', $product),
                    'remove' => route('favorites.remove', $product),
                ],
            ]);
        }

        return back()->with('success', "{$product->name} removido de favoritos");
    }

    /**
     * Listar favoritos del usuario
     * GET /favorites
     */
    public function index()
    {
        $favorites = Auth::user()->favorites()
            ->with('product.categories')
            ->paginate(12);

        return view('favorites.index', compact('favorites'));
    }
}
