@extends('layouts.admin')
@section('title', 'Quản lý sản phẩm')
@section('page_title', 'Quản lý sản phẩm')

@section('content')
<div class="admin-page">
    <div class="admin-toolbar">
        <div>
            <h2>Quản lý sản phẩm</h2>
            <p>Tổng số: <strong>{{ $products->total() }}</strong> máy hút mùi trên hệ thống</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="admin-primary">+ Thêm sản phẩm mới</a>
    </div>

    <form class="admin-filter" method="GET">
        <input name="q" value="{{ request('q') }}" placeholder="Tìm theo tên máy hút mùi..."/>
        <button class="admin-secondary">Tìm kiếm</button>
        @if(request('q'))
            <a class="admin-link" href="{{ route('admin.products.index') }}">Xóa lọc</a>
        @endif
    </form>

    <section class="admin-panel">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Thương hiệu</th>
                        <th>Danh mục</th>
                        <th>Giá từ</th>
                        <th>Tồn kho</th>
                        <th>Trạng thái</th>
                        <th style="width: 170px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        @php
                            $totalStock = $product->variants->sum('stock');
                            $hasVariants = $product->variants->isNotEmpty();
                        @endphp
                        <tr>
                            <td>
                                <img class="admin-thumb" src="{{ asset($product->primary_image_path) }}" alt="{{ $product->name }}">
                            </td>
                            <td>
                                <strong>{{ $product->name }}</strong>
                                <small class="table-sub">{{ $product->slug }}</small>
                            </td>
                            <td>{{ $product->brand?->name ?? '—' }}</td>
                            <td>{{ $product->category?->name ?? '—' }}</td>
                            <td>
                                <strong>{{ number_format($product->min_price, 0, ',', '.') }}đ</strong>
                            </td>
                            <td>
                                @if(!$hasVariants)
                                    <span class="stock-badge warning">Chưa có phân loại</span>
                                    <div><a href="{{ route('admin.products.show', $product) }}" class="quick-stock-link">+ Thêm phân loại</a></div>
                                @elseif($totalStock > 0)
                                    <span class="stock-badge in-stock">Còn {{ $totalStock }} cái</span>
                                @else
                                    <span class="stock-badge out-of-stock">⛔ Hết hàng</span>
                                    <div><a href="{{ route('admin.products.show', $product) }}" class="quick-stock-link text-danger">+ Nhập thêm hàng</a></div>
                                @endif
                            </td>
                            <td>
                                <span class="status {{ $product->status === 'active' ? 'status-completed' : 'status-cancelled' }}">
                                    {{ $product->status === 'active' ? 'Đang bán' : 'Tạm ẩn' }}
                                </span>
                            </td>
                            <td class="actions">
                                <a href="{{ route('admin.products.show', $product) }}" class="btn-action view" title="Xem chi tiết & nhập kho">Kho & Xem</a>
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn-action edit" title="Chỉnh sửa">Sửa</a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-action delete">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-cell">Chưa có sản phẩm nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            {{ $products->links() }}
        </div>
    </section>
</div>
@endsection
