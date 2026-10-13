@extends('layouts.admin')
@section('title', 'Chi tiết sản phẩm')
@section('page_title', 'Chi tiết sản phẩm')

@section('content')
<div class="admin-page">
    <div class="admin-form-head">
        <div>
            <a class="admin-back" href="{{ route('admin.products.index') }}">← Danh sách sản phẩm</a>
            <h2>{{ $product->name }}</h2>
            <p>{{ $product->brand?->name }} · {{ $product->category?->name }}</p>
        </div>
        <div class="head-actions">
            <a class="admin-secondary" href="{{ route('admin.products.edit', $product) }}">✏ Sửa thông tin</a>
            <a class="admin-primary" href="{{ route('admin.product-variants.create', ['product_id' => $product->id]) }}">+ Thêm phân loại</a>
            <a class="admin-outline" href="{{ route('products.show', $product->slug) }}" target="_blank">👁 Xem trên web</a>
        </div>
    </div>

    {{-- CẢNH BÁO NẾU HẾT HÀNG HOẶC CHƯA CÓ PHÂN LOẠI --}}
    @if($product->variants->isEmpty())
        <div class="admin-alert warning">
            <strong>⚠️ Sản phẩm chưa có phân loại & giá bán:</strong> Trên giao diện người dùng, sản phẩm này chưa thể mua được. 
            <a href="{{ route('admin.product-variants.create', ['product_id' => $product->id]) }}" class="alert-link">Bấm vào đây để tạo phân loại & nhập kho ngay →</a>
        </div>
    @elseif($product->variants->sum('stock') === 0)
        <div class="admin-alert danger">
            <strong>⛔ Sản phẩm hiện đã HẾT HÀNG (Tồn kho = 0):</strong> Trên giao diện người dùng đang hiển thị "Hết hàng". Vui lòng nhập thêm số lượng kho ở bảng phân loại bên dưới!
        </div>
    @endif

    <div class="product-admin-summary">
        <section class="admin-panel">
            <h3>Hình ảnh đại diện</h3>
            <div class="product-gallery-admin">
                <div class="product-main-image">
                    <img src="{{ asset($product->primary_image_path) }}" alt="{{ $product->name }}">
                </div>
                <div class="product-image-strip">
                    @foreach($product->images as $image)
                        <img src="{{ asset($image->image) }}" alt="Thumbnail">
                    @endforeach
                </div>
            </div>
        </section>

        <section class="admin-panel">
            <div class="detail-chips">
                <span>{{ $product->brand?->name }}</span>
                <span>{{ $product->category?->name }}</span>
                <span class="status status-{{ $product->status === 'active' ? 'completed' : 'cancelled' }}">
                    {{ $product->status === 'active' ? 'Đang bán' : 'Tạm ẩn' }}
                </span>
                <span class="status {{ $product->variants->sum('stock') > 0 ? 'status-completed' : 'status-cancelled' }}">
                    {{ $product->variants->sum('stock') > 0 ? 'Tổng kho: ' . $product->variants->sum('stock') . ' cái' : 'Hết hàng' }}
                </span>
            </div>

            <h3>{{ $product->name }}</h3>
            <div class="big-price">{{ number_format($product->min_price, 0, ',', '.') }}đ</div>
            <p class="muted-text">{{ $product->description }}</p>

            <dl class="admin-spec-list">
                <div><dt>Loại máy</dt><dd>{{ $product->detail?->hood_type ?: '—' }}</dd></div>
                <div><dt>Kích thước</dt><dd>{{ $product->detail?->dimensions ?: '—' }}</dd></div>
                <div><dt>Công suất hút</dt><dd>{{ $product->detail?->suction_capacity ?: '—' }}</dd></div>
                <div><dt>Độ ồn</dt><dd>{{ $product->detail?->noise_level ?: '—' }}</dd></div>
                <div><dt>Số cấp độ</dt><dd>{{ $product->detail?->speed_levels ?: '—' }}</dd></div>
                <div><dt>Chất liệu</dt><dd>{{ $product->detail?->material ?: '—' }}</dd></div>
                <div><dt>Điện áp</dt><dd>{{ $product->detail?->voltage ?: '—' }}</dd></div>
                <div><dt>Bảo hành</dt><dd>{{ $product->detail?->warranty ?: '—' }}</dd></div>
            </dl>
        </section>
    </div>

    {{-- BẢNG PHÂN LOẠI & NHẬP HÀNG NHANH --}}
    <section class="admin-panel">
        <div class="admin-panel-head">
            <div>
                <h3>Danh sách phân loại & Quản lý tồn kho</h3>
                <small>Nhập trực tiếp số lượng và bấm "Lưu kho" để bổ sung hàng ngay lập tức khi web hết hàng.</small>
            </div>
            <a class="admin-primary" href="{{ route('admin.product-variants.create', ['product_id' => $product->id]) }}">+ Thêm phân loại mới</a>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Kích thước</th>
                        <th>Màu sắc</th>
                        <th>Mã SKU</th>
                        <th>Giá bán</th>
                        <th style="min-width: 220px;">Tồn kho & Nhập hàng nhanh</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($product->variants as $variant)
                        <tr>
                            <td><strong>{{ $variant->size?->name }}</strong></td>
                            <td>
                                <span class="color-dot" style="background:{{ $variant->color?->hex_code ?: '#ddd' }}"></span>
                                {{ $variant->color?->name }}
                            </td>
                            <td><code>{{ $variant->sku }}</code></td>
                            <td><strong>{{ number_format($variant->price, 0, ',', '.') }}đ</strong></td>
                            <td>
                                <form method="POST" action="{{ route('admin.product-variants.quick-stock', $variant) }}" class="quick-stock-inline-form">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="stock" value="{{ $variant->stock }}" min="0" class="quick-stock-num">
                                    <button type="submit" class="btn-quick-stock {{ $variant->stock === 0 ? 'btn-restock-urgent' : '' }}" title="Cập nhật số lượng kho">
                                        {{ $variant->stock === 0 ? '⚡ Nhập hàng' : 'Cập nhật' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                <span class="status {{ $variant->stock > 0 ? 'status-completed' : 'status-cancelled' }}">
                                    {{ $variant->stock > 0 ? 'Còn hàng' : 'Hết hàng' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.product-variants.edit', $variant) }}" class="admin-link">Chỉnh sửa</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-cell">
                                Chưa có phân loại nào. <a href="{{ route('admin.product-variants.create', ['product_id' => $product->id]) }}">Tạo phân loại đầu tiên ngay →</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- QUẢN LÝ HÌNH ẢNH SẢN PHẨM --}}
    <section class="admin-panel">
        <div class="admin-panel-head">
            <div>
                <h3>Bộ sưu tập hình ảnh</h3>
                <small>Tải lên thêm ảnh hoặc đặt ảnh chính</small>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.products.images.store', $product) }}" enctype="multipart/form-data" class="upload-row">
            @csrf
            <input type="file" name="images[]" accept="image/*" multiple required>
            <button class="admin-secondary">Tải ảnh lên</button>
        </form>

        <div class="admin-image-grid">
            @foreach($product->images as $image)
                <div class="admin-image-card">
                    <img src="{{ asset($image->image) }}" alt="Image">
                    <div class="image-actions">
                        @if($image->is_primary)
                            <span class="status status-completed">Ảnh chính</span>
                        @else
                            <form method="POST" action="{{ route('admin.product-images.primary', $image) }}">
                                @csrf
                                @method('PATCH')
                                <button class="text-button">Đặt ảnh chính</button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('admin.product-images.destroy', $image) }}" onsubmit="return confirm('Xóa ảnh này?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-button danger">Xóa</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
