@extends('layouts.app')

@section('title', $product->name)

@section('content')
<section class="section product-detail-section">
    <div class="container">

        {{-- Breadcrumb --}}
        <div class="product-breadcrumb">
            <a href="{{ url('/') }}">Trang chủ</a>
            <span>›</span>
            <a href="{{ route('products.index') }}">Sản phẩm</a>
            <span>›</span>
            <span>{{ $product->name }}</span>
        </div>

        {{-- PRODUCT DETAIL --}}
        <div class="product-detail-layout">

            {{-- =========================
                LEFT: PRODUCT GALLERY
            ========================== --}}
            <div class="product-gallery">

                <div class="gallery-thumbs">
                    @foreach($product->images as $image)
                        <button
                            type="button"
                            class="gallery-thumb {{ $loop->first ? 'active' : '' }}"
                            data-gallery-image="{{ asset($image->image) }}"
                        >
                            <img
                                src="{{ asset($image->image) }}"
                                alt="{{ $product->name }}"
                            >
                        </button>
                    @endforeach
                </div>

                <div class="gallery-main">
                    <img
                        id="mainProductImage"
                        src="{{ asset($defaultVariant?->image ?: $product->primary_image_path) }}"
                        alt="{{ $product->name }}"
                    >
                </div>

            </div>


            {{-- =========================
                CENTER: PRODUCT INFORMATION
            ========================== --}}
            <div class="product-summary">

                <div class="product-brand">
                    {{ $product->brand?->name ?? 'Đang cập nhật' }}
                </div>

                <h1>{{ $product->name }}</h1>

                <div class="summary-rating">
                    <span class="stars">
                        {{ str_repeat('★', (int) round($product->average_rating)) }}
                        {{ str_repeat('☆', 5 - (int) round($product->average_rating)) }}
                    </span>

                    <span>
                        {{ number_format($product->average_rating, 1) }}/5
                    </span>

                    <span>
                        {{ $approvedReviews->count() }} đánh giá
                    </span>
                </div>

                {{-- PRICE --}}
                <div class="detail-price" id="variantPrice">
                    {{ number_format(
                        (int) ($defaultVariant?->price ?? $product->min_price),
                        0,
                        ',',
                        '.'
                    ) }}đ
                </div>

                {{-- BASIC INFO --}}
                <div class="variant-meta">

                    <div>
                        <span>Thương hiệu:</span>
                        <strong>
                            {{ $product->brand?->name ?? 'Đang cập nhật' }}
                        </strong>
                    </div>

                    <div>
                        <span>Loại máy:</span>
                        <strong>
                            {{ $product->detail?->hood_type
                                ?? $product->category?->name
                                ?? 'Đang cập nhật' }}
                        </strong>
                    </div>

                    <div>
                        <span>Mã máy:</span>
                        <strong id="variantSku">
                            {{ $defaultVariant?->sku ?: 'Đang cập nhật' }}
                        </strong>
                    </div>

                    <div
                        id="variantStock"
                        class="stock {{ !($defaultVariant && $defaultVariant->stock > 0) ? 'out' : '' }}"
                    >
                        {{ $defaultVariant && $defaultVariant->stock > 0
                            ? '✓ Còn '.$defaultVariant->stock.' sản phẩm'
                            : 'Hết hàng' }}
                    </div>

                </div>


                {{-- BUY FORM --}}
                <form
                    method="POST"
                    action="{{ route('cart.store') }}"
                    class="buy-box"
                    data-loading-form
                >

                    @csrf

                    <input
                        type="hidden"
                        name="product_variant_id"
                        id="selectedVariantId"
                        value="{{ $defaultVariant?->id }}"
                    >

                    {{-- SIZE --}}
                    @if($sizes->count())
                        <div class="variant-group">
                            <label>Kích thước</label>

                            <div
                                class="option-row"
                                id="sizeOptions"
                            >
                                @foreach($sizes as $size)
                                    <button
                                        type="button"
                                        class="option-button"
                                        data-size-id="{{ $size->id }}"
                                    >
                                        {{ $size->name }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif


                    {{-- COLOR --}}
                    @if($colors->count())
                        <div class="variant-group">
                            <label>Màu sắc</label>

                            <div
                                class="option-row"
                                id="colorOptions"
                            >
                                @foreach($colors as $color)
                                    <button
                                        type="button"
                                        class="option-button color-option"
                                        data-color-id="{{ $color->id }}"
                                    >
                                        <span
                                            style="background: {{ $color->hex_code }}"
                                        ></span>

                                        {{ $color->name }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif


                    {{-- QUANTITY --}}
                    <div class="quantity-row">

                        <label for="quantity">
                            Số lượng
                        </label>

                        <div class="quantity-control">
                            <button
                                type="button"
                                onclick="changeQuantity(-1)"
                            >
                                −
                            </button>

                            <input
                                id="quantity"
                                type="number"
                                name="quantity"
                                value="1"
                                min="1"
                            >

                            <button
                                type="button"
                                onclick="changeQuantity(1)"
                            >
                                +
                            </button>
                        </div>

                    </div>


                    {{-- BUTTONS --}}
                    <div class="buy-actions">

                        <button
                            class="btn btn-add-cart"
                            id="addToCartButton"
                            type="submit"
                        >
                            <span>🛒</span>
                            Thêm vào giỏ hàng
                        </button>

                        <button
                            class="btn btn-buy-now"
                            id="buyNowButton"
                            type="submit"
                            name="buy_now"
                            value="1"
                        >
                            Mua ngay
                        </button>

                    </div>

                </form>


                {{-- WISHLIST --}}
                @auth
                    <form
                        method="POST"
                        action="{{ route('wishlist.toggle', $product) }}"
                    >
                        @csrf

                        <button
                            class="wishlist-button"
                            type="submit"
                        >
                            ♡ Thêm vào yêu thích
                        </button>
                    </form>
                @else
                    <a
                        class="wishlist-button"
                        href="{{ route('login') }}"
                    >
                        ♡ Đăng nhập để yêu thích
                    </a>
                @endauth

            </div>


            {{-- =========================
                RIGHT: COMMITMENT
            ========================== --}}
            <aside class="product-commitment">

                <h3>
                    Cam kết của chúng tôi
                </h3>

                <div class="commitment-item">
                    <div class="commitment-icon">✓</div>

                    <div>
                        <strong>
                            Hàng chính hãng 100%
                        </strong>

                        <p>
                            Sản phẩm chính hãng,
                            nguồn gốc rõ ràng.
                        </p>
                    </div>
                </div>


                <div class="commitment-item">
                    <div class="commitment-icon">🚚</div>

                    <div>
                        <strong>
                            Miễn phí vận chuyển
                        </strong>

                        <p>
                            Cho đơn hàng từ
                            3.000.000đ.
                        </p>
                    </div>
                </div>


                <div class="commitment-item">
                    <div class="commitment-icon">↻</div>

                    <div>
                        <strong>
                            Đổi trả dễ dàng
                        </strong>

                        <p>
                            Hỗ trợ đổi trả
                            trong 7 ngày.
                        </p>
                    </div>
                </div>


                <div class="commitment-item">
                    <div class="commitment-icon">☎</div>

                    <div>
                        <strong>
                            Hỗ trợ 24/7
                        </strong>

                        <p>
                            Hotline:
                            <strong>1900 1234</strong>
                        </p>
                    </div>
                </div>


                <div class="commitment-payment">

                    <strong>
                        Thanh toán an toàn
                    </strong>

                    <div class="payment-methods">
                        <span>COD</span>
                        <span>VISA</span>
                        <span>ATM</span>
                    </div>

                </div>

            </aside>

        </div>
    </div>
</section>


{{-- =========================
    DESCRIPTION + SPECIFICATION
========================== --}}
<section class="section section-muted">
    <div class="container detail-tabs">

        <article>
            <h2>Mô tả sản phẩm</h2>

            <p>
                {{ $product->description }}
            </p>
        </article>


        <article>

            <h2>Thông số kỹ thuật</h2>

            <dl class="spec-list">

                @foreach([
                    'Công suất hút' => $product->detail?->suction_capacity,
                    'Độ ồn' => $product->detail?->noise_level,
                    'Tốc độ' => $product->detail?->speed_levels,
                    'Điện áp' => $product->detail?->voltage,
                    'Chất liệu' => $product->detail?->material,
                    'Kiểu máy' => $product->detail?->hood_type,
                    'Kích thước' => $product->detail?->dimensions,
                    'Bảo hành' => $product->detail?->warranty,
                ] as $label => $value)

                    <div>
                        <dt>{{ $label }}</dt>

                        <dd>
                            {{ $value ?: 'Đang cập nhật' }}
                        </dd>
                    </div>

                @endforeach

            </dl>

        </article>

    </div>
</section>


{{-- =========================
    REVIEWS
========================== --}}
<section class="section">

    <div class="container reviews-layout">

        <div>

            <h2>Đánh giá khách hàng</h2>

            <div class="review-score">
                {{ number_format($product->average_rating, 1) }}
                <span>/5</span>
            </div>

            <p>
                {{ $approvedReviews->count() }}
                đánh giá đã hiển thị
            </p>


            @auth

                <form
                    method="POST"
                    action="{{ route('reviews.store', $product) }}"
                    class="review-form"
                >

                    @csrf

                    <label>Chọn số sao</label>

                    <select name="rating" required>
                        <option value="5">5 sao</option>
                        <option value="4">4 sao</option>
                        <option value="3">3 sao</option>
                        <option value="2">2 sao</option>
                        <option value="1">1 sao</option>
                    </select>


                    <label>Bình luận</label>

                    <textarea
                        name="comment"
                        rows="4"
                        placeholder="Chia sẻ trải nghiệm của bạn"
                    ></textarea>


                    <button
                        class="btn btn-dark"
                        type="submit"
                    >
                        Gửi đánh giá
                    </button>

                </form>

            @else

                <a
                    class="btn btn-outline"
                    href="{{ route('login') }}"
                >
                    Đăng nhập để đánh giá
                </a>

            @endauth

        </div>


        <div class="review-list">

            @forelse($approvedReviews as $review)

                <article class="review-card">

                    <div>

                        <strong>
                            {{ $review->user?->name }}
                        </strong>

                        <span class="stars">
                            {{ str_repeat('★', $review->rating) }}
                            {{ str_repeat('☆', 5 - $review->rating) }}
                        </span>

                    </div>

                    <p>
                        {{ $review->comment ?: 'Người dùng chưa để lại bình luận.' }}
                    </p>

                    <small>
                        {{ $review->created_at->format('d/m/Y') }}
                    </small>

                </article>

            @empty

                <div class="empty-state compact">
                    Chưa có đánh giá nào cho sản phẩm này.
                </div>

            @endforelse

        </div>

    </div>

</section>


{{-- =========================
    RELATED PRODUCTS
========================== --}}
@if($relatedProducts->count())

<section class="section section-muted">

    <div class="container">

        <div class="section-heading">

            <div>

                <span class="eyebrow">
                    Liên quan
                </span>

                <h2>
                    Sản phẩm cùng danh mục
                </h2>

            </div>

        </div>


        <div class="product-grid">

            @foreach($relatedProducts as $relatedProduct)

                <x-product-card
                    :product="$relatedProduct"
                />

            @endforeach

        </div>

    </div>

</section>

@endif
@endsection

@push('scripts')
    <script>
        window.productVariants = @json($variantData);
        window.defaultVariantId = @json($defaultVariant?->id);
    </script>
@endpush
