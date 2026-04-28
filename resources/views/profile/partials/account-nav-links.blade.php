<ul class="nav flex-column gap-2">
    <li class="nav-item">
        <a class="nav-link sidebar-account-link {{ $currentPanel === 'overview' ? 'is-current' : '' }}"
           href="{{ route('user', ['panel' => 'overview']) }}"
           @if ($currentPanel === 'overview') aria-current="page" @endif
        ><span class="me-2">🏠</span> Información general</a>
    </li>
    <li class="nav-item">
        <a class="nav-link sidebar-account-link {{ $currentPanel === 'security' ? 'is-current' : '' }}"
           href="{{ route('user', ['panel' => 'security']) }}"
           @if ($currentPanel === 'security') aria-current="page" @endif
        ><span class="me-2">🔒</span> Seguridad</a>
    </li>
    <li class="nav-item">
        <a class="nav-link sidebar-account-link {{ $currentPanel === 'orders' ? 'is-current' : '' }}"
           href="{{ route('user', ['panel' => 'orders']) }}"
           @if ($currentPanel === 'orders') aria-current="page" @endif
        ><span class="me-2">🧾</span> Pedidos</a>
    </li>
    <li class="nav-item">
        <a class="nav-link sidebar-account-link {{ $currentPanel === 'favorites' ? 'is-current' : '' }}"
           href="{{ route('user', ['panel' => 'favorites']) }}"
           @if ($currentPanel === 'favorites') aria-current="page" @endif
        ><span class="me-2">⭐</span> Favoritos</a>
    </li>
    <li class="nav-item">
        <a class="nav-link sidebar-account-link {{ $currentPanel === 'edit' ? 'is-current' : '' }}"
           href="{{ route('user', ['panel' => 'edit']) }}"
           @if ($currentPanel === 'edit') aria-current="page" @endif
        ><span class="me-2">✏️</span> Editar perfil</a>
    </li>
</ul>
