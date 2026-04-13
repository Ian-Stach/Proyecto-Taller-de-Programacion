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
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $categories = \App\Models\Category::all();

        return view('orders.index', compact('orders', 'categories'));
    }

    /**
     * Mostrar detalle de una orden
     * GET /orders/{id}
     */
    public function show(Order $order)
    {
        // Verificar que la orden pertenece al usuario autenticado
        if ($order->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        $categories = \App\Models\Category::all();

        return view('orders.show', compact('order', 'categories'));
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

            // Calcular total
            $total = 0;
            foreach ($cart as $productId => $quantity) {
                $product = Product::findOrFail($productId);
                $total += $product->price * $quantity;
            }

            // Crear orden
            $order = Order::create([
                'user_id' => Auth::id(),
                'total_price' => $total,
                'status' => 'pending'
            ]);

            // Crear items de la orden
            foreach ($cart as $productId => $quantity) {
                $product = Product::findOrFail($productId);

                // Validar stock suficiente
                if ($product->stock < $quantity) {
                    throw new \Exception("Stock insuficiente para {$product->name}");
                }

                // Crear item de orden
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => $product->price
                ]);

                // Reducir stock
                $product->decrement('stock', $quantity);
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
