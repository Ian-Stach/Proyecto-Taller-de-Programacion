<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/*
 * OrderController
 * ----------------
 * Gestiona las órdenes de compra del usuario autenticado.
 * El flujo de checkout completo ocurre en store(): lee el carrito de sesión,
 * crea la Order, crea los OrderItems y descuenta el stock en una transacción DB.
 *
 * Rutas (middleware 'auth' en routes/web.php):
 *   GET  /orders        → index()  → historial de órdenes paginado
 *   GET  /orders/{order}→ show()   → detalle de una orden
 *   POST /checkout      → store()  → procesa el carrito y crea la orden
 */
class OrderController extends Controller
{
    /**
     * Listar órdenes del usuario autenticado
     * GET /orders
     *
     * Carga 'orderItems' con eager loading para poder mostrar el conteo de ítems
     * en la vista del listado sin queries adicionales.
     * Pagina a 10 órdenes por página en orden descendente de fecha.
     */
    public function index() {
        $orderDateColumn = Order::dateColumn();

        $orders = Auth::user()->orders()
            ->with('orderItems')
            ->orderByDesc($orderDateColumn)
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Mostrar detalle de una orden
     * GET /orders/{order}
     *
     * Verificación de propiedad manual (authorization): compara $order->user_id
     * con Auth::id() y aborta con 403 si no coinciden. Esto previene que un usuario
     * acceda a órdenes de otro usuario adivinando el ID en la URL.
     * Se usa abort(403) en vez de una Policy de Laravel por simplicidad.
     *
     * Carga 'orderItems.product' con eager loading para que la vista pueda mostrar
     * el nombre, imagen y precio de cada producto sin N queries adicionales.
     */
    public function show(Order $order) {
        // Verificar que la orden pertenece al usuario autenticado
        if ($order->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $order->load('orderItems.product');
        return view('orders.show', compact('order'));
    }

    /**
     * Crear una orden desde el carrito
     * POST /checkout
     *
     * Flujo completo del checkout dentro de una transacción DB:
     *
     *   1. Validación previa: el carrito no puede estar vacío.
     *
     *   2. Dentro de DB::beginTransaction():
     *      a. Obtiene todos los productos del carrito en UNA sola query (whereIn + keyBy)
     *         para evitar N queries dentro del foreach.
     *      b. Itera el carrito validando:
     *         • Que el producto existe en BD (podría haber sido eliminado).
     *         • Que el stock es suficiente para la cantidad pedida.
     *      c. Calcula subtotal acumulando price * quantity por ítem.
     *      d. Aplica impuesto del 10% (tax = subtotal * 0.1) y calcula total.
     *         round(..., 2) evita problemas de punto flotante en los centavos.
     *      e. Crea el registro Order con status 'pending'.
     *      f. Por cada ítem crea un OrderItem y decrementa el stock del producto
     *         con decrement(), que ejecuta un UPDATE atómico en BD.
     *      g. Hace commit y limpia el carrito de la sesión.
     *
     *   3. Si cualquier Exception se lanza (producto no encontrado, stock insuficiente,
     *      error de BD), DB::rollBack() deshace toda la transacción y se redirige
     *      con el mensaje de error. Ningún stock queda decrementado a medias.
     *
     * La transacción garantiza consistencia: o todo ocurre o nada.
     */
    public function store(Request $request) {
        $cart = session()->get('cart') ?? [];

        if (empty($cart) || !is_array($cart)) {
            return redirect()->route('cart.show')
                ->withErrors(['error' => 'El carrito está vacío']);
        }

        try {
            DB::beginTransaction();

            // Obtener todos los productos de una vez (evitar N queries)
            $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');
            
            $subtotal = 0;
            $orderItems = [];

            // Validar stock y calcular total
            foreach ($cart as $productId => $quantity) {
                $product = $products[$productId] ?? null;
                
                if (!$product) {
                    throw new \Exception("Producto {$productId} no encontrado");
                }

                if ($product->stock < $quantity) {
                    throw new \Exception("Stock insuficiente para {$product->name}");
                }

                $subtotal += $product->price * $quantity;
                $orderItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => $product->price
                ];
            }

            $tax = round($subtotal * 0.1, 2);
            $total = round($subtotal + $tax, 2);

            // Crear orden
            $order = Order::create([
                'user_id' => Auth::id(),
                'date' => now(),
                'total_price' => $total,
                'status' => 'pendiente'
            ]);

            // Crear items y reducir stock
            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price']
                ]);

                // decrement() ejecuta UPDATE products SET stock = stock - N directamente,
                // evitando race conditions que ocurrirían si se hiciera en PHP.
                $item['product']->decrement('stock', $item['quantity']);
            }

            DB::commit();

            // Limpiar carrito
            session()->forget('cart');

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Orden creada exitosamente!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
