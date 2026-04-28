<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/*
 * FavoriteController
 * -------------------
 * Gestiona la lista de favoritos de cada usuario autenticado.
 * Los favoritos se guardan en la tabla 'favorites' (user_id, product_id).
 *
 * Rutas (middleware 'auth' en routes/web.php):
 *   GET    /favorites             → index()   → lista de favoritos del usuario
 *   POST   /favorites/{product}   → store()   → agrega a favoritos
 *   DELETE /favorites/{product}   → destroy() → elimina de favoritos
 *
 * store() y destroy() soportan dos modos de respuesta:
 *   • AJAX / expectsJson() → devuelve JSON con el nuevo estado y las URLs de ambas acciones.
 *     El JS en la tarjeta de producto usa esas URLs para actualizar el form del corazón
 *     sin recargar la página (toggle visual del ícono).
 *   • Petición normal (form tradicional) → redirige con flash success/info.
 */
class FavoriteController extends Controller
{
    /**
     * Agregar producto a favoritos
     * POST /favorites/{product}
     *
     * Antes de crear, verifica si ya existe el favorito para el usuario actual.
     * Si ya existe:
     *   - AJAX → devuelve JSON con favorited:true y mensaje informativo (no es un error,
     *     el estado final es el deseado).
     *   - Normal → redirige con flash 'info'.
     *
     * Si no existe, crea el registro Favorite con user_id y product_id.
     * Auth::id() es más eficiente que Auth::user()->id porque no carga el modelo
     * completo del usuario si no estaba ya en memoria.
     *
     * La respuesta JSON incluye ambas URLs (add y remove) para que el JS pueda
     * actualizar el action del formulario del corazón al nuevo estado opuesto.
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
     *
     * Usa delete() directamente en el query builder (sin cargar el modelo primero)
     * para eliminar en una sola query. Si el registro no existía, delete() no
     * falla — simplemente no elimina nada, lo que es el estado correcto.
     *
     * La respuesta JSON devuelve favorited:false para que el JS actualice el ícono
     * del corazón al estado "no favorito".
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
     *
     * Carga la relación 'product.categories' con eager loading para evitar N+1
     * al renderizar la grilla (cada tarjeta muestra categorías del producto).
     * Pagina a 12 por página, igual que el catálogo de productos.
     */
    public function index()
    {
        $favorites = Auth::user()->favorites()
            ->with('product.categories')
            ->paginate(12);

        return view('favorites.index', compact('favorites'));
    }
}
