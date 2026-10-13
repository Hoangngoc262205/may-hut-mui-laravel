@extends('layouts.app')
@section('title', 'Máy Hút Mùi Store - Thiết bị bếp hiện đại')
@section('content')
<section class="hero">
    <div class="container hero-grid">
        <div class="hero-copy">
            <div class="hero-eyebrow-badge">
                <span class="badge-dot"></span>
                <span>MÁY HÚT MÙI STORE · CHÍNH HÃNG</span>
            </div>
            <h1>KHÔNG KHÓI – KHÔNG MÙI<br><span class="hero-highlight">KHÔNG LO BÁM BẨN</span></h1>
            <p>Giải pháp hút khói khử mùi chuẩn châu Âu, vận hành êm ái, mang lại không gian bếp trong lành và sang trọng cho gia đình bạn.</p>
            <div class="hero-perks">
                <span>✓ Động cơ Inverter tiết kiệm điện</span>
                <span>✓ Công suất hút mạnh mẽ tới 1200 m³/h</span>
                <span>✓ Lưới lọc Inox dễ tháo rửa</span>
            </div>
            <div class="hero-actions">
                <a class="btn btn-accent hero-btn" href="{{ route('products.index') }}">KHÁM PHÁ BỘ SƯU TẬP →</a>
                <a class="btn btn-outline-white" href="{{ route('products.index', ['sort' => 'best_selling']) }}">Sản phẩm bán chạy</a>
            </div>
        </div>
        <div class="hero-visual">
            <img src="{{ asset('images/products/hero-kitchen.jpg') }}" alt="Bếp hiện đại và máy hút mùi cao cấp">
        </div>
    </div>
</section>
<section class="service-bar"><div class="container service-grid"><div><b>🚚</b><span><strong>Miễn phí vận chuyển</strong><small>Cho đơn từ 3.000.000đ</small></span></div><div><b>♜</b><span><strong>Bảo hành chính hãng</strong><small>Từ 24 - 36 tháng</small></span></div><div><b>↻</b><span><strong>Đổi trả dễ dàng</strong><small>Trong 7 ngày</small></span></div><div><b>☎</b><span><strong>Hỗ trợ 24/7</strong><small>1900 1234</small></span></div></div></section>
<section class="section"><div class="container"><div class="section-heading"><div><span class="eyebrow">BÁN CHẠY NHẤT</span><h2>SẢN PHẨM NỔI BẬT</h2></div><a href="{{ route('products.index') }}">Xem tất cả →</a></div><div class="product-grid featured-grid">@foreach($featuredProducts as $product)<x-product-card :product="$product" badge="MỚI"/>@endforeach</div></div></section>
<section class="section section-muted"><div class="container"><div class="section-heading"><div><span class="eyebrow">KHÁM PHÁ</span><h2>DANH MỤC SẢN PHẨM</h2></div></div><div class="category-grid">@foreach($categories as $category)<a class="category-card category-card-lg" href="{{ route('products.index',['category'=>$category->id]) }}"><div class="category-art">⌁</div><span>{{ $category->name }}</span><small>{{ $category->products_count }} sản phẩm</small></a>@endforeach</div></div></section>
<section class="section"><div class="container"><div class="section-heading"><div><span class="eyebrow">ĐỐI TÁC</span><h2>THƯƠNG HIỆU NỔI BẬT</h2></div></div><div class="brand-strip">@foreach($brands as $brand)<a href="{{ route('products.index',['brand'=>$brand->id]) }}">{{ $brand->name }}</a>@endforeach</div></div></section>
<section class="section section-muted"><div class="container"><div class="section-heading"><div><span class="eyebrow">ĐƯỢC CHỌN NHIỀU</span><h2>SẢN PHẨM BÁN CHẠY</h2></div><a href="{{ route('products.index',['sort'=>'best_selling']) }}">Xem bán chạy →</a></div><div class="product-grid">@foreach($bestSellingProducts as $product)<x-product-card :product="$product" badge="BÁN CHẠY"/>@endforeach</div></div></section>
<section class="about-band"><div class="container about-grid"><div><span class="eyebrow">CAM KẾT</span><h2>Tư vấn đúng công suất, đúng kích thước, đúng ngân sách.</h2></div><p>Website demo Laravel MVC tập trung vào trải nghiệm mua máy hút mùi: xem sản phẩm, chọn màu sắc/kích thước, theo dõi mã máy, thêm giỏ hàng và đặt hàng.</p></div></section>
@endsection
