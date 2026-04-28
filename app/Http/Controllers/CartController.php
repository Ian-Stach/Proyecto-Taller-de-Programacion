<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

/*
 * CartController
 * ---------------
 * Gestiona el carrito de compras, que se almacena íntegramente en la sesión PHP
 * como un array asociativo: [ product_id => quantity ].
 *
 * No existe un modelo Cart ni tabla en BD; el carrito vive en la sesión del usuario.
 * Solo usuarios autenticados pueden usar el carrito (middleware 'auth' en routes/web.php).
 *
 * Rutas:
 *   GET    /cart              → index()   → muestra el carrito
 *   POST   /cart/{product}    → add()     → agrega o acumula un producto
 *   DELETE /cart/{product}    → remove()  → elimina un producto del carrito
 */
class CartController extends Controller
{
    /**
     * Ver carrito
     * GET /cart
     *
     * Si el carrito está vacío devuelve la vista con arrays vacíos directamente,
     * evitando hacer cualquier consulta a la BD.
     *
     * Si hay productos, los obtiene TODOS en una sola query usando whereIn()
     * con las claves del array de sesión (los product_ids), y luego los indexa
     * por id con keyBy('id') para acceso O(1) dentro del foreach.
     *
     * Construye $cartItems como array de arrays con:
     *   'product'  → el modelo Product
     *   'quantity' → cantidad en el carrito
     *   'subtotal' → price * quantity
     * y acumula $total para mostrar el resumen.
     *
     * NOTA: si un producto fue eliminado de la BD después de ser añadido al carrito,
     * el isset($products[$productId]) lo descarta silenciosamente.
     */
    public function index()
    {
        $cart = session()->get('cart') ?? [];
        
        if (empty($cart)) {
            return view('cart.show', ['cartItems' => [], 'total' => 0, 'tax' => 0, 'grandTotal' => 0]);
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

        $tax = round($total * 0.10, 2);
        $grandTotal = round($total + $tax, 2);

        return view('cart.show', compact('cartItems', 'total', 'tax', 'grandTotal'));
    }

    /**
     * Agregar producto al carrito
     * POST /cart/{product}
     *
     * La validación usa 'max:' . $product->stock como regla dinámica para que
     * la cantidad enviada en este request no supere el stock total del producto.
     *
     * Pero el carrito es acumulativo: el mismo producto puede estar ya en el carrito
     * de un add anterior. Por eso se calcula $newQuantity = $currentQuantity + $request->quantity
     * y se valida que esa suma tampoco supere el stock. Si supera, se devuelve un error
     * descriptivo indicando cuánto ya hay en el carrito.
     *
     * Devuelve JSON en lugar de una redirección para que el cliente pueda
     * mostrar el resultado sin recargar la página (manejado por public/js/cart.js).
     * La validación de Laravel también devuelve JSON automáticamente cuando
     * el request incluye el header Accept: application/json.
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
            return response()->json([
                'success' => false,
                'errors'  => [
                    'quantity' => ["La cantidad solicitada ({$newQuantity}) excede el stock disponible ({$product->stock}). "
                        . "Actualmente tienes {$currentQuantity} en el carrito."]
                ]
            ], 422);
        }

        // Agregar o actualizar cantidad
        $cart[$product->id] = $newQuantity;

        // Guardar en sesión
        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => "{$product->name} agregado al carrito!"
        ]);
    }

    /**
     * Contenido del sidebar del carrito
     * GET /cart/sidebar
     *
     * Devuelve el HTML del partial cart-sidebar con los datos actualizados
     * de la sesión. Usado por cart.js tras un add() exitoso para refrescar
     * el offcanvas sin recargar la página completa.
     *
     * Construye los mismos datos que el ViewComposer del AppServiceProvider,
     * pero en una respuesta independiente (no un layout completo).
     */
    public function sidebar()
    {
        $cart = session()->get('cart', []);
        $sidebarCartItems = [];
        $sidebarCartSubtotal = 0;

        if (!empty($cart)) {
            $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');

            foreach ($cart as $productId => $quantity) {
                $product = $products->get($productId);

                if (!$product) {
                    continue;
                }

                $subtotal = $product->price * $quantity;
                $sidebarCartItems[] = [
                    'product'  => $product,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                ];
                $sidebarCartSubtotal += $subtotal;
            }
        }

        return view('layouts.partials.cart-sidebar', compact('sidebarCartItems', 'sidebarCartSubtotal'));
    }

    /**
     * Remover producto del carrito
     * DELETE /cart/{product}
     *
     * Elimina la clave del producto del array de sesión.
     * Si el producto no estaba en el carrito (isset falla), no hace nada
     * y responde igualmente, evitando errores por doble clic o race conditions.
     *
     * Cuando la petición viene del sidebar AJAX (X-Requested-With: XMLHttpRequest
     * o Accept: application/json) devuelve JSON { success: true }.
     * De lo contrario redirige a la página del carrito (comportamiento clásico
     * para el form de cart/show.blade.php que no usa fetch).
     */
    public function remove(Request $request, Product $product)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            unset($cart[$product->id]);
            session()->put('cart', $cart);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('cart.show');
    }
}
