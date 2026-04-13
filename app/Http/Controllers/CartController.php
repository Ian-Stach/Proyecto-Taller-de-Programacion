<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Ver carrito
     * GET /cart
     */
    public function index(Request $request)
    {
        $cart = session()->get('cart') ?? [];
        $cartItems = [];
        $total = 0;

        foreach ($cart as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product->price * $quantity
                ];
                $total += $product->price * $quantity;
            }
        }

        $categories = \App\Models\Category::all();

        return view('cart.show', compact('cartItems', 'total', 'categories'));
    }

    /**
     * Agregar producto al carrito
     * POST /cart
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);

        // Validar stock
        if ($product->stock < $request->quantity) {
            return back()->withErrors(['stock' => 'Stock insuficiente']);
        }

        // Obtener carrito de la sesión
        $cart = session()->get('cart', []);

        // Agregar o actualizar cantidad
        if (isset($cart[$product->id])) {
            $cart[$product->id] += $request->quantity;
        } else {
            $cart[$product->id] = $request->quantity;
        }

        // Guardar en sesión
        session()->put('cart', $cart);

        return redirect()->route('cart.show')
            ->with('success', "{$product->name} agregado al carrito!");
    }

    /**
     * Remover producto del carrito
     * DELETE /cart/{product_id}
     */
    public function remove(Request $request, $productId)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);

            return redirect()->route('cart.show')
                ->with('success', 'Producto removido del carrito');
        }

        return redirect()->route('cart.show')
            ->withErrors(['error' => 'Producto no encontrado']);
    }
}
