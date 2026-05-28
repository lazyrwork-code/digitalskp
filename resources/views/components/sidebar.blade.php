<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-grid-fill me-2"></i>
        <span>SIGMA RM</span>
    </div>

    <nav class="nav flex-column">
        @php
            $role = auth()->user()->role;

            $dashboardRoute = match($role) {
                'admin'  => 'admin.dashboard',
                'kepala' => 'kepala.dashboard',
                default  => 'dashboard',
            };
            $repositoryRoute = match($role) {
                'admin'  => 'admin.riwayat',
                'kepala' => 'kepala.riwayat',
                default  => 'skp.riwayat',
            };

            $menus = [
                ['route' => $dashboardRoute,   'icon' => 'bi-speedometer2',       'label' => 'Dashboard'],
                ['route' => 'skp.baru',        'icon' => 'bi-file-earmark-plus',  'label' => 'Ajukan SKP Baru'],
                ['route' => $repositoryRoute,  'icon' => 'bi-clock-history',      'label' => 'Riwayat SKP'],
            ];

            if ($role === 'kepala') {
                $menus[] = [
                    'route' => 'kepala.user.index',
                    'icon'  => 'bi-people',
                    'label' => 'Kelola User',
                ];
            }
        @endphp

        @foreach($menus as $menu)
            <a class="nav-link {{ request()->routeIs($menu['route']) ? 'active' : '' }}"
               href="{{ route($menu['route']) }}">
                <i class="bi {{ $menu['icon'] }}"></i>
                <span>{{ $menu['label'] }}</span>
            </a>
        @endforeach
    </nav>
</aside>