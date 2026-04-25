<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Listar órdenes del usuario autenticado
     * GET /orders
     */
    public function index()
    {
        $orders = Auth::user()->orders()
            ->with('orderItems')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Mostrar detalle de una orden
     * GET /orders/{order}
     */
    public function show(Order $order)
    {
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
     */
    public function store(Request $request)
    {
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
                'total_price' => $total,
                'status' => 'pending'
            ]);

            // Crear items y reducir stock
            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price']
                ]);

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
