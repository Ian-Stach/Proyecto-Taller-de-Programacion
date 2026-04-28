<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

/*
 * ProfileController
 * ------------------
 * Controla el dashboard de cuenta del usuario autenticado (perfil, seguridad,
 * órdenes, favoritos y edición). Toda la lógica de las distintas secciones
 * se centraliza en una única vista: profile/user.blade.php, que renderiza
 * el panel activo según el parámetro ?panel= de la URL.
 *
 * Rutas (middleware 'auth' en routes/web.php):
 *   GET    /user              → show()    → dashboard (panel por defecto: 'overview')
 *   GET    /user/edit         → edit()    → redirige a /user?panel=edit
 *   PUT    /user              → update()  → actualiza nombre y email
 *   DELETE /user              → destroy() → elimina la cuenta
 */
class ProfileController extends Controller
{
    /**
     * Display the user's account dashboard.
     * GET /user?panel=overview|security|orders|favorites|edit
     *
     * Panel system: la vista tiene 5 paneles. El controlador solo carga datos
     * para el panel activo (lazy loading por panel), evitando queries innecesarias.
     * Por ejemplo, si panel='overview', $orders y $favorites son null y la vista
     * no los necesita.
     *
     * Panel 'orders':
     *   Carga órdenes con eager loading 'orderItems.product' para mostrar detalles.
     *   Búsqueda por $ordersSearch: si es numérico busca por id; siempre busca también
     *   por nombre de producto via whereHas. La función anónima agrupa ambas condiciones
     *   con OR para que cualquiera de las dos sea suficiente para incluir la orden.
     *   appends($request->query()) preserva todos los parámetros en los links de paginación.
     *
     * Panel 'favorites':
     *   Carga favoritos con 'product.categories' (eager loading anidado).
     *   whereHas('product') filtra favoritos huérfanos (cuyo producto fue eliminado).
     *   Búsqueda por $favoritesSearch: busca en el nombre del producto relacionado.
     *
     * Validación de panel: si ?panel= no está en la lista permitida, se normaliza
     * a 'overview', evitando que la vista intente renderizar un panel inexistente.
     */
    public function show(Request $request): View
    {
        $currentPanel = $request->string('panel')->toString();

        if (! in_array($currentPanel, ['overview', 'security', 'orders', 'favorites', 'edit'], true)) {
            $currentPanel = 'overview';
        }

        $ordersSearch = trim((string) $request->query('orders_search', ''));
        $favoritesSearch = trim((string) $request->query('favorites_search', ''));
        $orders = null;
        $favorites = null;

        if ($currentPanel === 'orders') {
            $ordersQuery = $request->user()
                ->orders()
                ->with(['orderItems.product'])
                ->orderByDesc('created_at');

            if ($ordersSearch !== '') {
                $ordersQuery->where(function ($query) use ($ordersSearch) {
                    // Si el término es numérico, también busca por ID de orden exacto
                    if (ctype_digit($ordersSearch)) {
                        $query->orWhere('id', (int) $ordersSearch);
                    }

                    $query->orWhereHas('orderItems.product', function ($productQuery) use ($ordersSearch) {
                        $productQuery->where('name', 'like', "%{$ordersSearch}%");
                    });
                });
            }

            $orders = $ordersQuery->paginate(8)->appends($request->query());
        }

        if ($currentPanel === 'favorites') {
            $favoritesQuery = $request->user()
                ->favorites()
                ->whereHas('product') // descarta favoritos cuyo producto fue eliminado de BD
                ->with('product.categories')
                ->latest();

            if ($favoritesSearch !== '') {
                $favoritesQuery->whereHas('product', function ($productQuery) use ($favoritesSearch) {
                    $productQuery->where('name', 'like', "%{$favoritesSearch}%");
                });
            }

            $favorites = $favoritesQuery->paginate(8)->appends($request->query());
        }

        return view('profile.user', [
            'currentPanel' => $currentPanel,
            'orders' => $orders,
            'favorites' => $favorites,
            'ordersSearch' => $ordersSearch,
            'favoritesSearch' => $favoritesSearch,
            'statusLabels' => [
                'completed' => 'Completado',
                'pending'   => 'Pendiente',
                'cancelled' => 'Cancelado',
            ],
            'statusClasses' => [
                'completed' => 'text-success',
                'pending'   => 'text-warning-emphasis',
                'cancelled' => 'text-danger',
            ],
        ]);
    }

    /**
     * Display the user's profile form.
     * GET /user/edit
     *
     * No renderiza una vista propia: redirige a /user?panel=edit preservando
     * cualquier query param existente con array_merge($request->query(), ...).
     * Esto mantiene la URL consistente con el sistema de paneles de la vista.
     */
    public function edit(Request $request): RedirectResponse
    {
        return Redirect::route('user', array_merge($request->query(), ['panel' => 'edit']));
    }

    /**
     * Update the user's profile information.
     * PUT /user
     *
     * ProfileUpdateRequest es un Form Request que centraliza las reglas de validación
     * (name requerido, email único excepto el propio, etc.) fuera del controlador.
     * fill() + save() en vez de update() para mayor claridad sobre qué campos se tocan.
     * La vista muestra el flash 'profile-updated' para confirmar el cambio.
     *
     * NOTA: si el usuario cambia su email, email_verified_at se resetea automáticamente
     * a null en el modelo User (ver el método fillable/casts del modelo).
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        $request->user()->save();

        return Redirect::back()->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     * DELETE /user
     *
     * validateWithBag('userDeletion', ...) valida la contraseña actual con la regla
     * 'current_password' de Laravel. Si falla, el error va al bag 'userDeletion' en vez
     * de al bag default, para que la vista pueda mostrarlo en el panel correcto.
     *
     * Secuencia de eliminación segura:
     *   1. Guarda el modelo $user antes de cerrar sesión (después de logout no hay Auth::user()).
     *   2. Auth::logout() cierra la sesión activa.
     *   3. $user->delete() elimina el registro de la BD (y en cascada según migraciones).
     *   4. invalidate() destruye la sesión del servidor.
     *   5. regenerateToken() emite un nuevo CSRF token para la respuesta final.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
