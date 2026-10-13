@extends('layouts.app')

@section('title', 'Đơn hàng '.$order->order_code)

@section('content')
    <section class="page-head">
        <div class="container">
            <span class="eyebrow">Order detail</span>
            <h1>Đơn hàng {{ $order->order_code }}</h1>
            <p><span class="status-badge status-{{ $order->status }}">{{ $order->statusLabel() }}</span></p>
        </div>
    </section>

    <section class="section">
        <div class="container order-detail-grid">
            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <div class="cart-product">
                                        <img src="{{ asset($item->variant?->image_path ?? 'images/products/hood-default.svg') }}" alt="{{ $item->product_name }}">
                                        <div>
                                            <strong>{{ $item->product_name }}</strong>
                                            <span>{{ $item->variant_name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ number_format($item->price, 0, ',', '.') }}đ</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->subtotal, 0, ',', '.') }}đ</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <aside class="summary-card">
                <h2>Thông tin giao hàng</h2>
                <p><strong>{{ $order->customer_name }}</strong></p>
                <p>{{ $order->phone }}</p>
                @if($order->email)<p>{{ $order->email }}</p>@endif
                <p>{{ $order->fullShippingAddress() }}</p>
                @if($order->note)
                    <p>Ghi chú: {{ $order->note }}</p>
                @endif
                <div class="summary-line">
                    <span>Tạm tính</span>
                    <strong>{{ number_format($order->subtotal, 0, ',', '.') }}đ</strong>
                </div>
                <div class="summary-line">
                    <span>Phí vận chuyển</span>
                    <strong>{{ number_format($order->shipping_fee, 0, ',', '.') }}đ</strong>
                </div>
                <div class="summary-line total">
                    <span>Tổng thanh toán</span>
                    <strong>{{ number_format($order->total, 0, ',', '.') }}đ</strong>
                </div>
            </aside>
        </div>
    </section>
@endsection
