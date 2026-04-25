<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight text-gray-800">
            Panel de Control
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="p-6 text-gray-900">
                    <p class="mb-4 text-xl">
                        ¡Bienvenido <strong>{{ Auth::user()->name }}</strong>! 👋
                    </p>
                    <p class="mb-5">
                        Has iniciado sesión correctamente
                    </p>
                    
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="rounded-md bg-rose-400 px-5 py-2.5 font-bold text-white hover:bg-rose-500">
                            🚪 Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
