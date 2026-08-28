@extends('layouts.app')

@section('title', 'Đơn hàng của tôi')

@section('content')
    <section class="page-head">
        <div class="container">
            <span class="eyebrow">Orders</span>
            <h1>Đơn hàng của tôi</h1>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Ngày đặt</th>
                            <th>Sản phẩm</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td><strong>{{ $order->order_code }}</strong></td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $order->items_count }}</td>
                                <td>{{ number_format($order->total, 0, ',', '.') }}đ</td>
                                <td><span class="status-badge status-{{ $order->status }}">{{ $order->statusLabel() }}</span></td>
                                <td><a href="{{ route('orders.show', $order) }}">Chi tiết</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="6">Bạn chưa có đơn hàng nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pagination-wrap">{{ $orders->links() }}</div>
        </div>
    </section>
@endsection
