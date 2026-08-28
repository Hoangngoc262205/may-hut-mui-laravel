@extends('layouts.app')

@section('title', 'Sản phẩm máy hút mùi')

@section('content')
    <section class="page-head">
        <div class="container">
            <span class="eyebrow">Catalog</span>
            <h1>Sản phẩm máy hút mùi</h1>
            <p>Tìm kiếm, lọc theo danh mục, thương hiệu, kích thước, màu sắc, giá và tồn kho.</p>
        </div>
    </section>

    <section class="section">
        <div class="container catalog-layout">
            <aside class="filter-panel">
                <form method="GET" action="{{ route('products.index') }}">
                    <div class="filter-head">
                        <h2>Bộ lọc</h2>
                        <a href="{{ route('products.index') }}">Xóa bộ lọc</a>
                    </div>

                    <label>Tìm kiếm</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Bosch, âm tủ...">

                    <label>Danh mục</label>
                    <select name="category">
                        <option value="">Tất cả</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>

                    <label>Thương hiệu</label>
                    <select name="brand">
                        <option value="">Tất cả</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" @selected(request('brand') == $brand->id)>{{ $brand->name }}</option>
                        @endforeach
                    </select>

                    <label>Kích thước</label>
                    <select name="size">
                        <option value="">Tất cả</option>
                        @foreach($sizes as $size)
                            <option value="{{ $size->id }}" @selected(request('size') == $size->id)>{{ $size->name }}</option>
                        @endforeach
                    </select>

                    <label>Màu sắc</label>
                    <select name="color">
                        <option value="">Tất cả</option>
                        @foreach($colors as $color)
                            <option value="{{ $color->id }}" @selected(request('color') == $color->id)>{{ $color->name }}</option>
                        @endforeach
                    </select>

                    <div class="two-cols">
                        <div>
                            <label>Giá từ</label>
                            <input type="number" name="min_price" value="{{ request('min_price') }}" min="0">
                        </div>
                        <div>
                            <label>Đến</label>
                            <input type="number" name="max_price" value="{{ request('max_price') }}" min="0">
                        </div>
                    </div>

                    <label>Trạng thái</label>
                    <select name="stock">
                        <option value="">Tất cả</option>
                        <option value="in_stock" @selected(request('stock') === 'in_stock')>Còn hàng</option>
                    </select>

                    <label>Sắp xếp</label>
                    <select name="sort">
                        <option value="latest" @selected(request('sort') === 'latest')>Mới nhất</option>
                        <option value="price_asc" @selected(request('sort') === 'price_asc')>Giá thấp đến cao</option>
                        <option value="price_desc" @selected(request('sort') === 'price_desc')>Giá cao đến thấp</option>
                        <option value="best_selling" @selected(request('sort') === 'best_selling')>Bán chạy</option>
                    </select>

                    <button class="btn btn-dark full" type="submit">Áp dụng lọc</button>
                </form>
            </aside>

            <div class="catalog-content">
                <div class="catalog-toolbar">
                    <p>Tìm thấy <strong>{{ $products->total() }}</strong> sản phẩm</p>
                    <a class="btn btn-outline mobile-filter" href="#filters">Bộ lọc</a>
                </div>

                @if($products->count())
                    <div class="product-grid">
                        @foreach($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>
                    <div class="pagination-wrap">{{ $products->links() }}</div>
                @else
                    <div class="empty-state">
                        <h2>Không tìm thấy sản phẩm phù hợp</h2>
                        <p>Hãy thử bỏ bớt bộ lọc hoặc tìm kiếm bằng từ khóa ngắn hơn.</p>
                        <a class="btn btn-dark" href="{{ route('products.index') }}">Xóa bộ lọc</a>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
