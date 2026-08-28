@extends('layouts.admin')
@section('title','Tổng quan')
@section('page_title','Tổng quan')
@section('content')
<div class="admin-page">
    <div class="admin-welcome"><div><span class="eyebrow">QUẢN TRỊ CỬA HÀNG</span><h2>Xin chào, Admin 👋</h2><p>Theo dõi nhanh hoạt động của website máy hút mùi.</p></div><a class="admin-primary" href="{{ route('admin.products.create') }}">+ Thêm sản phẩm</a></div>
    <div class="admin-stats">
        <div class="admin-stat"><span class="admin-stat-icon">▣</span><div><small>Tổng sản phẩm</small><strong>{{ $totalProducts }}</strong></div></div>
        <div class="admin-stat"><span class="admin-stat-icon">▤</span><div><small>Đơn hàng</small><strong>{{ $totalOrders }}</strong></div></div>
        <div class="admin-stat"><span class="admin-stat-icon">♙</span><div><small>Khách hàng</small><strong>{{ $totalUsers }}</strong></div></div>
        <div class="admin-stat"><span class="admin-stat-icon">₫</span><div><small>Doanh thu hoàn thành</small><strong>{{ number_format($totalRevenue,0,',','.') }}đ</strong></div></div>
    </div>
    <div class="admin-two-col">
        <section class="admin-panel"><div class="admin-panel-head"><div><h3>Đơn hàng gần đây</h3><small>8 đơn mới nhất</small></div><a href="{{ route('admin.orders.index') }}">Xem tất cả →</a></div><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Mã đơn</th><th>Khách hàng</th><th>Ngày đặt</th><th>Trạng thái</th><th>Tổng</th></tr></thead><tbody>@forelse($recentOrders as $order)<tr><td><strong>{{ $order->order_code }}</strong></td><td>{{ $order->user?->name ?? $order->customer_name }}</td><td>{{ optional($order->created_at)->format('d/m/Y') }}</td><td><span class="status status-{{ $order->status }}">{{ $order->statusLabel() }}</span></td><td><strong>{{ number_format($order->total,0,',','.') }}đ</strong></td></tr>@empty<tr><td colspan="5" class="empty-cell">Chưa có đơn hàng.</td></tr>@endforelse</tbody></table></div></section>
        <section class="admin-panel"><div class="admin-panel-head"><div><h3>Sắp hết hàng</h3><small>Variant còn từ 5 sản phẩm trở xuống</small></div><a href="{{ route('admin.product-variants.index') }}">Quản lý →</a></div><div class="admin-stock-list">@forelse($lowStockVariants as $variant)<a href="{{ route('admin.product-variants.edit',$variant) }}" class="admin-stock-item"><div><strong>{{ $variant->product?->name }}</strong><small>{{ $variant->variant_name }}</small></div><b class="low-stock">{{ $variant->stock }}</b></a>@empty<div class="empty-block">Không có sản phẩm sắp hết hàng.</div>@endforelse</div></section>
    </div>
</div>
@endsection
