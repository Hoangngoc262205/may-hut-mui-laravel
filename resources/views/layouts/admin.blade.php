<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin') - Máy Hút Mùi Store</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="admin-body">

    <aside class="admin-sidebar">

        {{-- Logo --}}
        <a class="admin-brand" href="{{ route('admin.dashboard') }}">
            <span class="logo-mark">M</span>

            <span>
                Máy Hút Mùi<br>
                <small>STORE ADMIN</small>
            </span>
        </a>

        {{-- Menu --}}
        <nav>

            {{-- Tổng quan --}}
            <a href="{{ route('admin.dashboard') }}"
               @class(['active' => request()->routeIs('admin.dashboard')])>
                ▦ Tổng quan
            </a>

            {{-- Sản phẩm --}}
            <a href="{{ route('admin.products.index') }}"
               @class(['active' => request()->routeIs('admin.products.*')])>
                ▣ Sản phẩm
            </a>

            {{-- Danh mục --}}
            <a href="{{ route('admin.categories.index') }}"
               @class(['active' => request()->routeIs('admin.categories.*')])>
                ◫ Danh mục
            </a>

            {{-- Thương hiệu --}}
            <a href="{{ route('admin.brands.index') }}"
               @class(['active' => request()->routeIs('admin.brands.*')])>
                ⌁ Thương hiệu
            </a>

            {{-- Kích thước --}}
            <a href="{{ route('admin.sizes.index') }}"
               @class(['active' => request()->routeIs('admin.sizes.*')])>
                ↔ Kích thước
            </a>

            {{-- Màu sắc --}}
            <a href="{{ route('admin.colors.index') }}"
               @class(['active' => request()->routeIs('admin.colors.*')])>
                ● Màu sắc
            </a>

            {{-- Phân loại --}}
            <a href="{{ route('admin.product-variants.index') }}"
               @class(['active' => request()->routeIs('admin.product-variants.*')])>
                ◈ Phân loại
            </a>

            {{-- Đơn hàng --}}
            <a href="{{ route('admin.orders.index') }}"
               @class(['active' => request()->routeIs('admin.orders.*')])>
                ▤ Đơn hàng
            </a>

            {{-- Tài chính - LAB 09 --}}
            <a href="{{ route('admin.finance.index') }}"
               @class(['active' => request()->routeIs('admin.finance.*')])>
                ◉ Tài chính
            </a>

            {{-- Người dùng --}}
            <a href="{{ route('admin.users.index') }}"
               @class(['active' => request()->routeIs('admin.users.*')])>
                ♙ Người dùng
            </a>

            {{-- Đánh giá --}}
            <a href="{{ route('admin.reviews.index') }}"
               @class(['active' => request()->routeIs('admin.reviews.*')])>
                ★ Đánh giá
            </a>

        </nav>
    </aside>


    <div class="admin-shell">

        {{-- Header --}}
        <header class="admin-topbar">

            <div>
                <span class="muted">
                    Quản trị hệ thống
                </span>

                <h1>
                    @yield('page_title', 'Tổng quan')
                </h1>
            </div>

            <div class="admin-actions">

                {{-- Xem website --}}
                <a class="btn btn-outline"
                   href="{{ route('home') }}">
                    Xem website
                </a>

                {{-- Đăng xuất --}}
                <form method="POST"
                      action="{{ route('logout') }}">
                    @csrf

                    <button class="btn btn-dark"
                            type="submit">
                        Đăng xuất
                    </button>
                </form>

            </div>

        </header>


        {{-- Nội dung --}}
        <main class="admin-main">

            @include('components.alert')

            @yield('content')

        </main>

    </div>

</body>
</html>