<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/*
 * AdminController
 * ----------------
 * Panel de administración. Todas las rutas de este controlador están protegidas
 * por los middlewares 'auth' + 'admin' (definidos en routes/web.php).
 *
 * Rutas:
 *   GET    /admin                        → dashboard()        → métricas generales
 *   GET    /admin/products               → products()         → listado de todos los productos
 *   GET    /admin/products/create        → createProduct()    → formulario de creación
 *   POST   /admin/products               → storeProduct()     → guarda nuevo producto
 *   GET    /admin/products/{product}/edit → editProduct()     → formulario de edición
 *   PATCH  /admin/products/{product}     → updateProduct()    → guarda cambios
 *   DELETE /admin/products/{product}     → destroyProduct()   → elimina producto
 *   PATCH  /admin/products/{product}/toggle → toggleProduct() → activa/desactiva
 */
class AdminController extends Controller
{
    // ─── Dashboard ──────────────────────────────────────────────────────────

    public function dashboard() {
        $orderDateColumn = Order::dateColumn();

        $stats = [
            'products'         => Product::count(),
            'products_active'  => Product::where('active', true)->count(),
            'products_value'   => Product::sum(\DB::raw('price * stock')),
            'categories'       => Category::count(),
            'users'            => User::count(),
            'orders'           => Order::count(),
            'orders_revenue'   => Order::sum('total_price'),
            'pending_orders'    => Order::where('status', 'pendiente')->count(),
        ];

        $recentOrders = Order::with('user')
            ->latest($orderDateColumn)
            ->limit(10)
            ->get();

        $lowStock = Product::where('active', true)
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->limit(10)
            ->get(['id', 'name', 'stock']);

        return view('admin.dashboard', compact('stats', 'recentOrders', 'lowStock'));
    }


    // ─── Products ────────────────────────────────────────────────────────────
    public function products(Request $request) {
        $products = Product::with('categories')
            ->filter($request->all())
            ->orderBy('name')
            ->paginate(12);

        return view('admin.products.index', compact('products'));
    }

    public function createProduct() {
        $categories = Category::orderBy('name')->get(['id', 'name']);
        return view('admin.products.create', compact('categories'));
    }

    private function productRules(?Product $product = null): array {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                $product ? Rule::unique('products', 'name')->ignore($product->id) : 'unique:products,name',
            ],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'string', 'max:500'],
            'active' => ['boolean'],
            'category_id' => ['required', 'exists:categories,id'],
            'habitat' => ['nullable', Rule::in(array_keys(Product::HABITAT_OPTIONS))],
            'diet' => ['nullable', Rule::in(array_keys(Product::DIET_OPTIONS))],
            'era' => ['nullable', Rule::in(array_keys(Product::ERA_OPTIONS))],
            'height_meters' => ['nullable', 'numeric', 'min:0'],
            'categories' => ['array'],
            'categories.*' => ['exists:categories,id'],
        ];
    }

    public function storeProduct(Request $request) {
        $validated = $request->validate($this->productRules());
        $validated['active'] = $request->boolean('active');
        $product = Product::create($validated);
        $product->categories()->sync($request->input('categories', []));
        return redirect()->route('admin.products')->with('success', "Producto \"{$product->name}\" creado correctamente.");
    }

    public function editProduct(Product $product) {
        $categories = Category::orderBy('name')->get(['id', 'name']);
        $selectedCategoryIds = $product->categories()->pluck('categories.id')->all();
        return view('admin.products.edit', compact('product', 'categories', 'selectedCategoryIds'));
    }

    public function updateProduct(Request $request, Product $product) {
        $validated = $request->validate($this->productRules($product));
        $validated['active'] = $request->boolean('active');
        $product->update($validated);
        $product->categories()->sync($request->input('categories', []));
        return redirect()->route('admin.products')->with('success', "Producto \"{$product->name}\" actualizado correctamente.");
    }

    public function destroyProduct(Product $product) {
        $name = $product->name;
        $product->delete();
        return redirect()->route('admin.products')->with('success', "Producto \"{$name}\" eliminado correctamente.");
    }

    public function toggleProduct(Product $product) {
        $product->update(['active' => ! $product->active]);
        $status = $product->active ? 'activado' : 'desactivado';
        return back()->with('success', "Producto \"{$product->name}\" {$status}.");
    }

    // ─── Users ────────────────────────────────────────────────────────────────

    public function users(Request $request) {
        $query = User::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('is_admin')) {
            $query->where('is_admin', (bool) $request->input('is_admin'));
        }

        if ($request->filled('is_active')) {
            $request->boolean('is_active') ? $query->whereNotNull('email_verified_at') : $query->whereNull('email_verified_at');
        }

        $users = $query->orderBy('id')->paginate(20)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function createUser() {
        return view('admin.users.create');
    }

    public function storeUser(Request $request) {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'is_admin' => ['boolean'],
        ]);

        $validated['is_admin'] = $request->boolean('is_admin');
        $validated['email_verified_at'] = now();
        $user = User::create($validated);
        return redirect()->route('admin.users')->with('success', "Usuario \"{$user->name}\" creado correctamente.");
    }

    public function editUser(User $user) {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user) {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_admin' => ['boolean'],
        ]);

        $validated['is_admin'] = $request->boolean('is_admin');

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);
        return redirect()->route('admin.users')->with('success', "Usuario \"{$user->name}\" actualizado correctamente.");
    }

    public function destroyUser(User $user) {
        $name = $user->name;
        $user->delete();
        return redirect()->route('admin.users')->with('success', "Usuario \"{$name}\" eliminado correctamente.");
    }

    // ─── Orders ───────────────────────────────────────────────────────────────

    public function orders(Request $request) {
        $orderDateColumn = Order::dateColumn();
        $query = Order::with('user');

        if ($search = $request->input('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $orders = $query->orderByDesc($orderDateColumn)->paginate(20)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function showOrder(Order $order) {
        $order->load('user', 'orderItems.product');
        return view('admin.orders.show', compact('order'));
    }

    public function updateOrderStatus(Request $request, Order $order) {
        $request->validate([
            'status' => ['required', 'in:pendiente,completado,cancelado'],
        ]);

        $order->update(['status' => $request->input('status')]);

        return back()->with('success', "Estado de la orden #{$order->id} actualizado a \"{$order->status}\".");
    }

    public function metrics() {
        $lowStockThreshold = 10;
        $orderDateColumn = Order::dateColumn();
        $now = now();
        $startOfMonth     = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth   = $now->copy()->subMonth()->endOfMonth();
        $startOfToday     = $now->copy()->startOfDay();

        // ─── Inventory ────────────────────────────────────────────────────
        $noStockCount      = Product::where('stock', 0)->count();
        $lowStockCount     = Product::where('stock', '>', 0)->where('stock', '<=', $lowStockThreshold)->count();
        $inventoryValue    = Product::sum(DB::raw('price * stock'));
        $addedThisMonth    = Product::where('created_at', '>=', $startOfMonth)->count();

        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.id', 'products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        $leastSoldProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.id', 'products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold')
            ->limit(10)
            ->get();

        $lowStockList = Product::where('stock', '>', 0)
            ->where('stock', '<=', $lowStockThreshold)
            ->orderBy('stock')
            ->get(['id', 'name', 'stock']);

        // ─── Orders ───────────────────────────────────────────────────────
        $salesToday = Order::where('status', 'completado')
            ->where($orderDateColumn, '>=', $startOfToday)
            ->sum('total_price');

        $salesThisMonth = Order::where('status', 'completado')
            ->where($orderDateColumn, '>=', $startOfMonth)
            ->sum('total_price');

        $salesLastMonth = Order::where('status', 'completado')
            ->whereBetween($orderDateColumn, [$startOfLastMonth, $endOfLastMonth])
            ->sum('total_price');

        $salesGrowth = $salesLastMonth > 0
            ? round((($salesThisMonth - $salesLastMonth) / $salesLastMonth) * 100, 1)
            : null;

        $completedOrders = Order::where('status', 'completado')->count();
        $cancelledOrders = Order::where('status', 'cancelado')->count();
        $pendingOrders   = Order::where('status', 'pendiente')->count();

        $avgTicket = $completedOrders > 0
            ? round(Order::where('status', 'completado')->avg('total_price'), 2)
            : 0;

        $stalePendingOrders = Order::where('status', 'pendiente')
            ->where($orderDateColumn, '<', $now->copy()->subHours(48))
            ->count();

        // ─── Customers ────────────────────────────────────────────────────
        $newUsersThisMonth = User::where('created_at', '>=', $startOfMonth)->count();
        $activeUsers       = User::has('orders')->count();
        $recurringUsers    = User::has('orders', '>=', 2)->count();
        $inactiveUsers     = User::where('is_admin', false)->doesntHave('orders')->count();

        $topBuyer = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.status', 'completado')
            ->select('users.id', 'users.name', DB::raw('SUM(orders.total_price) as total_spent'))
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_spent')
            ->first();

        $avgOrdersPerUser = $activeUsers > 0
            ? round(Order::count() / $activeUsers, 1)
            : 0;

        // ─── Charts ───────────────────────────────────────────────────────
        // Sales by month (last 12 months) – SQLite strftime
        $rawSalesByMonth = Order::where('status', 'completado')
            ->where($orderDateColumn, '>=', $now->copy()->subMonths(11)->startOfMonth())
            ->selectRaw("strftime('%Y-%m', {$orderDateColumn}) as month, SUM(total_price) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $rawProductsSoldByMonth = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completado')
            ->where("orders.{$orderDateColumn}", '>=', $now->copy()->subMonths(11)->startOfMonth())
            ->selectRaw("strftime('%Y-%m', orders.{$orderDateColumn}) as month, SUM(order_items.quantity) as total_units")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total_units', 'month');

        $rawSalesByDay = Order::where('status', 'completado')
            ->where($orderDateColumn, '>=', $now->copy()->subDays(29)->startOfDay())
            ->selectRaw("strftime('%Y-%m-%d', {$orderDateColumn}) as day, SUM(total_price) as total")
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

            $rawProductsSoldByDay = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.status', 'completado')
                ->where("orders.{$orderDateColumn}", '>=', $now->copy()->subDays(29)->startOfDay())
                ->selectRaw("strftime('%Y-%m-%d', orders.{$orderDateColumn}) as day, SUM(order_items.quantity) as total_units")
                ->groupBy('day')
                ->orderBy('day')
                ->pluck('total_units', 'day');

        $rawNewUsersByMonth = User::where('created_at', '>=', $now->copy()->subMonths(11)->startOfMonth())
            ->selectRaw("strftime('%Y-%m', created_at) as month, COUNT(*) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // Fill all 12 months / 30 days so charts have no gaps
        $monthLabels           = [];
        $salesByMonthFilled    = [];
        $productsSoldByMonthFilled = [];
        $newUsersByMonthFilled = [];
        for ($i = 11; $i >= 0; $i--) {
            $key            = $now->copy()->subMonths($i)->format('Y-m');
            $monthLabels[]           = $now->copy()->subMonths($i)->locale('es')->isoFormat('MMM YYYY');
            $salesByMonthFilled[]    = (float) ($rawSalesByMonth[$key]    ?? 0);
            $productsSoldByMonthFilled[] = (int) ($rawProductsSoldByMonth[$key] ?? 0);
            $newUsersByMonthFilled[] = (int)   ($rawNewUsersByMonth[$key] ?? 0);
        }

        $dayLabels              = [];
        $salesByDayFilled       = [];
        $productsSoldByDayFilled = [];
        for ($i = 29; $i >= 0; $i--) {
            $key          = $now->copy()->subDays($i)->format('Y-m-d');
            $dayLabels[]              = $now->copy()->subDays($i)->format('d/m');
            $salesByDayFilled[]       = (float) ($rawSalesByDay[$key] ?? 0);
            $productsSoldByDayFilled[] = (int) ($rawProductsSoldByDay[$key] ?? 0);
        }

        $salesByCategory = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completado')
            ->select('categories.name', DB::raw('SUM(order_items.quantity * order_items.unit_price) as total'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total')
            ->get();

        $orderStatusCounts = [
            'Pendiente'  => $pendingOrders,
            'Completado' => $completedOrders,
            'Cancelado'  => $cancelledOrders,
        ];

        return view('admin.metrics', compact(
            'lowStockThreshold',
            'noStockCount', 'lowStockCount', 'inventoryValue', 'addedThisMonth',
            'topProducts', 'leastSoldProducts', 'lowStockList',
            'salesToday', 'salesThisMonth', 'salesLastMonth', 'salesGrowth',
            'completedOrders', 'cancelledOrders', 'pendingOrders', 'avgTicket',
            'stalePendingOrders',
            'newUsersThisMonth', 'activeUsers', 'recurringUsers', 'topBuyer',
            'avgOrdersPerUser', 'inactiveUsers',
            'monthLabels', 'salesByMonthFilled', 'productsSoldByMonthFilled', 'newUsersByMonthFilled',
            'dayLabels', 'salesByDayFilled', 'productsSoldByDayFilled',
            'salesByCategory', 'orderStatusCounts',
        ));
    }
}
