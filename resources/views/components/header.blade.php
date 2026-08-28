@php
    $cartCount = 0;
    $wishlistCount = 0;
    if (auth()->check()) {
        $cart = auth()->user()->cart;
        $wishlist = auth()->user()->wishlist;
        $cartCount = $cart ? $cart->items()->sum('quantity') : 0;
        $wishlistCount = $wishlist ? $wishlist->items()->count() : 0;
    }
@endphp
<header class="site-header">
    <div class="top-strip">
        <div class="container top-strip-inner">
            <span>🚚 Miễn phí vận chuyển cho đơn từ 3.000.000đ</span>
            <span>☎ Hotline: <strong>1900 1234</strong></span>
            <span>Hỗ trợ 24/7</span>
        </div>
    </div>
    <div class="container header-main">
        <a href="{{ route('home') }}" class="brand-logo">
            <span class="logo-mark">⌂</span>
            <span><strong>MÁY HÚT MÙI</strong><small>STORE</small></span>
        </a>
        <form class="header-search" action="{{ route('products.index') }}" method="GET">
            <select name="category" aria-label="Danh mục">
                <option value="">Tất cả danh mục</option>
                @foreach($globalCategories ?? [] as $category)<option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>@endforeach
            </select>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Bạn cần tìm máy hút mùi gì?">
            <button type="submit" aria-label="Tìm kiếm">⌕</button>
        </form>
        <div class="header-actions">
            <a href="{{ auth()->check() ? route('wishlist.index') : route('login') }}" class="head-action"><span>♡</span><small>Yêu thích</small>@auth @if(($wishlistCount ?? 0) > 0)<em>{{ $wishlistCount }}</em>@endif @endauth</a>
            <a href="{{ auth()->check() ? route('cart.index') : route('login') }}" class="head-action"><span>🛒</span><small>Giỏ hàng</small>@auth @if(($cartCount ?? 0) > 0)<em>{{ $cartCount }}</em>@endif @endauth</a>
            @auth
                <div class="account-menu">
                    <a href="{{ route('orders.index') }}" class="head-action"><span>♙</span><small>Tài khoản</small></a>
                    <div class="account-panel"><a href="{{ route('orders.index') }}">Đơn hàng</a>@if(auth()->user()->isAdmin())<a href="{{ route('admin.dashboard') }}">Quản trị</a>@endif<form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Đăng xuất</button></form></div>
                </div>
            @else
                <a href="{{ route('login') }}" class="head-action"><span>♙</span><small>Đăng nhập</small></a>
            @endauth
        </div>
    </div>
    <nav class="main-nav"><div class="container nav-inner">
        <a class="category-menu" href="{{ route('products.index') }}">☰ &nbsp; DANH MỤC SẢN PHẨM</a>
        <a href="{{ route('home') }}">TRANG CHỦ</a>
        <a href="{{ route('products.index') }}">SẢN PHẨM</a>
        <a href="{{ route('products.index', ['sort'=>'best_selling']) }}">KHUYẾN MÃI</a>
        <a href="{{ route('products.index', ['sort'=>'latest']) }}">TIN TỨC</a>
        <a href="{{ route('products.index') }}">HƯỚNG DẪN</a>
        <a href="{{ route('products.index') }}">LIÊN HỆ</a>
    </div></nav>
</header>
