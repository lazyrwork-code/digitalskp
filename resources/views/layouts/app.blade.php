<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Digital SKP | @yield('title', 'Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>
    <div class="app-wrapper">
        {{-- Sidebar --}}
        <x-sidebar />

        {{-- Main content --}}
        <div class="main">
            {{-- Topbar --}}
            <x-topbar />

            {{-- Page content --}}
            <div class="content p-4">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    
</body>
</html>
