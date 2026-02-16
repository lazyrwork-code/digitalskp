<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-grid-fill me-2"></i>
        <span>Digital SKP</span>
    </div>

    <nav class="nav flex-column">
       @php
            $dashboardRoute = match(auth()->user()->role) {
                'admin'   => 'admin.dashboard',
                'kepala'  => 'kepala.dashboard',
                default   => 'dashboard', // pegawai
            };
            $repositoryRoute = match(auth()->user()->role) {
                'kepala'  => 'kepala.riwayat',
                default   => 'skp.riwayat', // pegawai
            };

            $menus = [
                ['route'=>$dashboardRoute, 'icon'=>'bi-speedometer2','label'=>'Dashboard'],
                ['route'=>'skp.baru','icon'=>'bi-file-earmark-plus','label'=>'Ajukan SKP Baru'],
                ['route'=>$repositoryRoute,'icon'=>'bi-clock-history','label'=>'Riwayat SKP'],
            ];
        @endphp


        @foreach($menus as $menu)
            <a class="nav-link {{ request()->routeIs($menu['route']) ? 'active' : '' }}" href="{{ route($menu['route']) }}">
                <i class="bi {{ $menu['icon'] }}"></i>
                <span>{{ $menu['label'] }}</span>
            </a>
        @endforeach
    </nav>
</aside>
