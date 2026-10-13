@extends('layouts.admin')
@section('title', $product->exists ? 'Sửa sản phẩm' : 'Thêm sản phẩm mới')
@section('page_title', $product->exists ? 'Sửa sản phẩm' : 'Thêm sản phẩm mới')

@section('content')
<div class="admin-page">
    <div class="admin-form-head">
        <div>
            <a class="admin-back" href="{{ route('admin.products.index') }}">← Quay lại danh sách sản phẩm</a>
            <h2>{{ $product->exists ? 'Cập nhật sản phẩm: ' . $product->name : 'Thêm máy hút mùi mới' }}</h2>
            <p>Điền đầy đủ thông tin, thông số kỹ thuật, hình ảnh và phân loại để bán trên website.</p>
        </div>
    </div>

    <form method="POST" 
          action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" 
          enctype="multipart/form-data" 
          class="admin-form">
        @csrf
        @if($product->exists)
            @method('PUT')
        @endif

        <div class="admin-two-col">
            {{-- CỘT 1: THÔNG TIN CƠ BẢN & PHÂN LOẠI BAN ĐẦU --}}
            <div class="form-column">
                <section class="admin-panel">
                    <h3>1. Thông tin chung</h3>
                    <div class="form-grid">
                        <div class="form-field full">
                            <label>Tên máy hút mùi <span class="required">*</span></label>
                            <input name="name" value="{{ old('name', $product->name) }}" placeholder="Ví dụ: Máy hút mùi Kocher KC-90 kính cong" required>
                            @error('name')<small class="error">{{ $message }}</small>@enderror
                        </div>

                        <div class="form-field">
                            <label>Danh mục <span class="required">*</span></label>
                            <select name="category_id" required>
                                <option value="">-- Chọn danh mục --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')<small class="error">{{ $message }}</small>@enderror
                        </div>

                        <div class="form-field">
                            <label>Thương hiệu <span class="required">*</span></label>
                            <select name="brand_id" required>
                                <option value="">-- Chọn thương hiệu --</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" @selected(old('brand_id', $product->brand_id) == $brand->id)>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('brand_id')<small class="error">{{ $message }}</small>@enderror
                        </div>

                        <div class="form-field full">
                            <label>Mô tả chi tiết</label>
                            <textarea name="description" rows="5" placeholder="Mô tả ưu điểm, tính năng nổi bật của máy hút mùi...">{{ old('description', $product->description) }}</textarea>
                        </div>

                        <div class="form-field">
                            <label>Trạng thái kinh doanh</label>
                            <select name="status">
                                <option value="active" @selected(old('status', $product->status ?: 'active') === 'active')>Đang bán (Hiển thị website)</option>
                                <option value="inactive" @selected(old('status', $product->status) === 'inactive')>Tạm ẩn</option>
                            </select>
                        </div>
                    </div>
                </section>

                {{-- HÌNH ẢNH SẢN PHẨM --}}
                <section class="admin-panel">
                    <h3>2. Hình ảnh sản phẩm</h3>
                    <p class="panel-subtitle">Tải lên hình ảnh máy hút mùi (JPG, PNG, WEBP, tối đa 4MB).</p>
                    <div class="form-grid">
                        <div class="form-field full">
                            <input type="file" name="images[]" accept="image/*" multiple class="file-input">
                            <small class="muted-text">💡 Có thể chọn nhiều ảnh cùng lúc. Ảnh đầu tiên sẽ làm ảnh đại diện chính.</small>
                        </div>
                    </div>
                </section>

                @if(!$product->exists)
                {{-- NẾU TẠO MỚI: CHO PHÉP NHẬP PHÂN LOẠI & TỒN KHO BAN ĐẦU --}}
                <section class="admin-panel highlight-panel">
                    <h3>3. Phân loại & Tồn kho ban đầu</h3>
                    <p class="panel-subtitle">Thiết lập ngay giá bán và số lượng nhập kho ban đầu để khách hàng có thể mua ngay.</p>
                    <div class="form-grid">
                        <div class="form-field">
                            <label>Kích thước</label>
                            <select name="size_id">
                                <option value="">-- Chọn kích thước --</option>
                                @foreach($sizes as $size)
                                    <option value="{{ $size->id }}" @selected(old('size_id') == $size->id)>{{ $size->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-field">
                            <label>Màu sắc</label>
                            <select name="color_id">
                                <option value="">-- Chọn màu sắc --</option>
                                @foreach($colors as $color)
                                    <option value="{{ $color->id }}" @selected(old('color_id') == $color->id)>{{ $color->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-field">
                            <label>Giá bán (VNĐ)</label>
                            <input type="number" name="initial_price" value="{{ old('initial_price') }}" placeholder="Ví dụ: 5500000" min="0" step="10000">
                        </div>

                        <div class="form-field">
                            <label>Số lượng nhập kho</label>
                            <input type="number" name="initial_stock" value="{{ old('initial_stock', 15) }}" min="0" placeholder="Số lượng máy">
                        </div>

                        <div class="form-field full">
                            <label>Mã SKU (tùy chọn)</label>
                            <input type="text" name="sku" value="{{ old('sku') }}" placeholder="Để trống hệ thống sẽ tự sinh mã SKU">
                        </div>
                    </div>
                </section>
                @endif
            </div>

            {{-- CỘT 2: THÔNG SỐ KỸ THUẬT MÁY HÚT MÙI --}}
            <div class="form-column">
                <section class="admin-panel">
                    <h3>{{ $product->exists ? '2. Thông số kỹ thuật' : '4. Thông số kỹ thuật chuyên sâu' }}</h3>
                    <p class="panel-subtitle">Thông số hiển thị tại trang chi tiết để khách hàng so sánh.</p>
                    <div class="form-grid">
                        <div class="form-field">
                            <label>Công suất hút</label>
                            <input name="suction_capacity" value="{{ old('suction_capacity', $product->detail?->suction_capacity) }}" placeholder="Ví dụ: 750 m³/h hoặc 1000 m³/h">
                        </div>

                        <div class="form-field">
                            <label>Độ ồn tối đa</label>
                            <input name="noise_level" value="{{ old('noise_level', $product->detail?->noise_level) }}" placeholder="Ví dụ: ≤ 52 dB (êm ái)">
                        </div>

                        <div class="form-field">
                            <label>Số cấp độ hút</label>
                            <input name="speed_levels" value="{{ old('speed_levels', $product->detail?->speed_levels) }}" placeholder="Ví dụ: 3 tốc độ + Booster">
                        </div>

                        <div class="form-field">
                            <label>Điện áp hoạt động</label>
                            <input name="voltage" value="{{ old('voltage', $product->detail?->voltage) }}" placeholder="220V / 50Hz">
                        </div>

                        <div class="form-field">
                            <label>Chất liệu thân máy</label>
                            <input name="material" value="{{ old('material', $product->detail?->material) }}" placeholder="Kính cường lực + Inox 304">
                        </div>

                        <div class="form-field">
                            <label>Kiểu dáng / Loại máy</label>
                            <input name="hood_type" value="{{ old('hood_type', $product->detail?->hood_type) }}" placeholder="Kính cong / Âm tủ / Chữ T">
                        </div>

                        <div class="form-field">
                            <label>Kích thước tổng thể</label>
                            <input name="dimensions" value="{{ old('dimensions', $product->detail?->dimensions) }}" placeholder="700 x 500 x 480 mm">
                        </div>

                        <div class="form-field">
                            <label>Thời hạn bảo hành</label>
                            <input name="warranty" value="{{ old('warranty', $product->detail?->warranty) }}" placeholder="24 tháng chính hãng">
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="admin-primary">
                {{ $product->exists ? '💾 Lưu thay đổi sản phẩm' : '🚀 Tạo sản phẩm & Nhập kho ngay' }}
            </button>
            <a href="{{ route('admin.products.index') }}" class="admin-secondary">Hủy bỏ</a>
        </div>
    </form>
</div>
@endsection
