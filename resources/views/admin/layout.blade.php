<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin – @yield('title', 'Panel de administración')</title>

        <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/estilos.css') }}?v={{ filemtime(public_path('css/estilos.css')) }}">

    </head>
    <body>
        <div class="d-flex min-vh-100">

            {{-- Sidebar --}}
            <nav class="admin-sidebar d-flex flex-column">
                <div class="admin-brand">Admin Panel</div>

                <ul class="nav flex-column mt-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                           href="{{ route('admin.dashboard') }}"
                        >Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}"
                           href="{{ route('admin.products') }}"
                        >Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}"
                           href="{{ route('admin.users') }}"
                        >Usuarios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}"
                           href="{{ route('admin.orders') }}"
                        >Órdenes</a>
                    </li>
                    <LI class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.metrics*') ? 'active' : '' }}"
                            href="{{ route('admin.metrics') }}"
                        >Métricas</a>
                    </LI>
                </ul>

                <div class="mt-auto p-3 border-top border-secondary">
                    <small class="d-block text-muted mb-2">{{ auth()->user()->name }}</small>
                    <a class="nav-link ps-0" href="{{ route('home') }}">← Volver al sitio</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-sm btn-outline-danger mt-1 w-100" type="submit">Cerrar sesión</button>
                    </form>
                </div>
            </nav>

            {{-- Contenido principal --}}
            <main class="admin-main">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show"
                         role="alert"
                    >
                        {{ session('success') }}
                        <button class="btn-close"
                                type="button"
                                data-bs-dismiss="alert"
                        ></button>
                    </div>
                @endif

                @yield('content')
            </main>

        </div>

        <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        @stack('scripts')
    </body>
</html>
