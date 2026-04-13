<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p style="font-size: 20px; margin-bottom: 15px;">
                        ¡Bienvenido <strong>{{ Auth::user()->name }}</strong>! 👋
                    </p>
                    <p style="margin-bottom: 20px;">
                        {{ __("You're logged in!") }}
                    </p>
                    
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" style="padding: 10px 20px; background-color: #FF6B6B; color: white; font-weight: bold; border: none; border-radius: 5px; cursor: pointer;">
                            🚪 Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
