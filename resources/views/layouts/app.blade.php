<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Máy Hút Mùi Store')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script type="module" src="{{ asset('build/assets/app-DNhQZVZW.js') }}"></script>
    @stack('styles')
</head>
<body>
    @include('components.header')
    <main>
        @include('components.alert')
        @yield('content')
    </main>
    @include('components.footer')
    <div id="toast" class="toast {{ session('success') || session('error') ? 'show' : '' }}">
        <span>{{ session('success') ?: session('error') }}</span>
    </div>
    @stack('scripts')
</body>
</html>
