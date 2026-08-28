@props(['product', 'badge' => null])

@php
    $rating = (int) round($product->average_rating);
    $isWishlisted = false;

    if (auth()->check() && auth()->user()->wishlist) {
        $isWishlisted = auth()->user()->wishlist->items()->where('product_id', $product->id)->exists();
    }
@endphp

<article class="product-card">
    <div class="product-media">
        <a href="{{ route('products.show', $product->slug) }}">
            <img src="{{ asset($product->primary_image_path) }}" alt="{{ $product->name }}">
        </a>
        @if($badge)
            <span class="badge badge-dark">{{ $badge }}</span>
        @endif
        @auth
            <form method="POST" action="{{ route('wishlist.toggle', $product) }}" class="wishlist-form">
                @csrf
                <button type="submit" @class(['heart-button', 'active' => $isWishlisted]) aria-label="Yêu thích">♡</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="heart-button" aria-label="Đăng nhập để yêu thích">♡</a>
        @endauth
    </div>

    <div class="product-info">
        <span class="product-brand">{{ $product->brand?->name }}</span>
        <h3><a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a></h3>
        @php($demoVariant = $product->variants->sortBy('price')->first())
        <div class="product-meta">
            <span>Mã: {{ $demoVariant?->sku ?? 'Đang cập nhật' }}</span>
            <span>{{ $product->category?->name }}</span>
        </div>
        <div class="rating-row">
            <span class="stars">{{ str_repeat('★', $rating) }}{{ str_repeat('☆', 5 - $rating) }}</span>
            <small>{{ number_format($product->average_rating, 1) }}</small>
        </div>
        <div class="price-row">
            <strong>{{ number_format($product->min_price, 0, ',', '.') }}đ</strong>
            <span @class(['stock', 'out' => ! $product->in_stock])>{{ $product->in_stock ? 'Còn hàng' : 'Hết hàng' }}</span>
        </div>
        <a href="{{ route('products.show', $product->slug) }}" class="btn btn-soft full">Xem chi tiết</a>
    </div>
</article>
