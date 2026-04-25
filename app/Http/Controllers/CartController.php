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
    public function index()
    {
        $cart = session()->get('cart') ?? [];
        
        if (empty($cart)) {
            return view('cart.show', ['cartItems' => [], 'total' => 0]);
        }
        
        // Una sola query: obtener todos los productos
        $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');
        $cartItems = [];
        $total = 0;

        foreach ($cart as $productId => $quantity) {
            if (isset($products[$productId])) {
                $product = $products[$productId];
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $product->price * $quantity
                ];
                $total += $product->price * $quantity;
            }
        }

        return view('cart.show', compact('cartItems', 'total'));
    }

    /**
     * Agregar producto al carrito
     * POST /cart/{product}
     */
    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $product->stock
        ]);

        // Obtener carrito de la sesión
        $cart = session()->get('cart', []);

        // Calcular nueva cantidad total
        $currentQuantity = $cart[$product->id] ?? 0;
        $newQuantity = $currentQuantity + $request->quantity;

        // Validar que no exceda el stock disponible
        if ($newQuantity > $product->stock) {
            return back()->withErrors([
                'quantity' => "La cantidad solicitada ({$newQuantity}) excede el stock disponible ({$product->stock}). "
                    . "Actualmente tienes {$currentQuantity} en el carrito."
            ]);
        }

        // Agregar o actualizar cantidad
        $cart[$product->id] = $newQuantity;

        // Guardar en sesión
        session()->put('cart', $cart);

        return redirect()->route('cart.show')
            ->with('success', "{$product->name} agregado al carrito!");
    }

    /**
     * Remover producto del carrito
     * DELETE /cart/{product}
     */
    public function remove(Product $product)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            unset($cart[$product->id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.show');
    }
}
