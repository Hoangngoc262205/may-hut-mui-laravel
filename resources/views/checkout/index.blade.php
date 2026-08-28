@extends('layouts.app')

@section('title', 'Thanh toán')

@section('content')
    <section class="page-head">
        <div class="container">
            <span class="eyebrow">Checkout</span>
            <h1>Thông tin đặt hàng</h1>
            <p>Website demo không tích hợp thanh toán online thật.</p>
        </div>
    </section>

    <section class="section">
        <div class="container checkout-layout">
            <form method="POST" action="{{ route('checkout.store') }}" class="form-card" data-loading-form>
                @csrf
                <h2>Thông tin nhận hàng</h2>
                <label>Họ tên</label>
                <input name="customer_name" value="{{ old('customer_name', auth()->user()->name) }}" required>
                <label>Số điện thoại</label>
                <input name="phone" value="{{ old('phone', auth()->user()->phone) }}" required>
                <label>Địa chỉ</label>
                <input name="address" value="{{ old('address', auth()->user()->address) }}" required>
                <label>Ghi chú</label>
                <textarea name="note" rows="4" placeholder="Thời gian giao, lưu ý lắp đặt...">{{ old('note') }}</textarea>
                <button class="btn btn-dark" type="submit">Đặt hàng</button>
            </form>

            <aside class="summary-card">
                <h2>Đơn hàng của bạn</h2>
                @foreach($cart->items as $item)
                    <div class="checkout-item">
                        <img src="{{ asset($item->variant?->image ?: $item->variant?->product?->primary_image_path) }}" alt="{{ $item->variant?->product?->name }}">
                        <div>
                            <strong>{{ $item->variant?->product?->name }}</strong>
                            <span>{{ $item->variant?->variant_name }} × {{ $item->quantity }}</span>
                        </div>
                        <b>{{ number_format($item->subtotal, 0, ',', '.') }}đ</b>
                    </div>
                @endforeach
                <div class="summary-line total">
                    <span>Tổng tiền</span>
                    <strong>{{ number_format($cart->total, 0, ',', '.') }}đ</strong>
                </div>
            </aside>
        </div>
    </section>
@endsection
