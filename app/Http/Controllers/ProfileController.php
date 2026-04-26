<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's account dashboard.
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
                ->whereHas('product')
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
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): RedirectResponse
    {
        return Redirect::route('user', array_merge($request->query(), ['panel' => 'edit']));
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        $request->user()->save();

        return Redirect::back()->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
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
