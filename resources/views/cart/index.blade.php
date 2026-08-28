@extends('layouts.app')

@section('title', 'Giỏ hàng')

@section('content')
    <section class="page-head">
        <div class="container">
            <span class="eyebrow">Shopping cart</span>
            <h1>Giỏ hàng</h1>
        </div>
    </section>

    <section class="section">
        <div class="container">
            @if($cart->items->isEmpty())
                <div class="empty-state">
                    <h2>Giỏ hàng đang trống</h2>
                    <p>Hãy chọn một mẫu máy hút mùi phù hợp với căn bếp của bạn.</p>
                    <a class="btn btn-dark" href="{{ route('products.index') }}">Tiếp tục mua sắm</a>
                </div>
            @else
                <div class="cart-layout">
                    <div class="table-card">
                        <table>
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Đơn giá</th>
                                    <th>Số lượng</th>
                                    <th>Thành tiền</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cart->items as $item)
                                    <tr>
                                        <td>
                                            <div class="cart-product">
                                                <img src="{{ asset($item->variant?->image ?: $item->variant?->product?->primary_image_path) }}" alt="{{ $item->variant?->product?->name }}">
                                                <div>
                                                    <strong>{{ $item->variant?->product?->name }}</strong>
                                                    <span>{{ $item->variant?->variant_name }} · SKU {{ $item->variant?->sku }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ number_format($item->price, 0, ',', '.') }}đ</td>
                                        <td>
                                            <form method="POST" action="{{ route('cart.items.update', $item) }}" class="qty-form">
                                                @csrf
                                                @method('PATCH')
                                                <input type="number" name="quantity" min="1" max="{{ $item->variant?->stock }}" value="{{ $item->quantity }}">
                                                <button class="btn btn-soft" type="submit">Cập nhật</button>
                                            </form>
                                        </td>
                                        <td><strong>{{ number_format($item->subtotal, 0, ',', '.') }}đ</strong></td>
                                        <td>
                                            <form method="POST" action="{{ route('cart.items.destroy', $item) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="link-danger" type="submit">Xóa</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <aside class="summary-card">
                        <h2>Tổng đơn hàng</h2>
                        <div class="summary-line">
                            <span>Tạm tính</span>
                            <strong>{{ number_format($cart->total, 0, ',', '.') }}đ</strong>
                        </div>
                        <div class="summary-line total">
                            <span>Tổng tiền</span>
                            <strong>{{ number_format($cart->total, 0, ',', '.') }}đ</strong>
                        </div>
                        <a class="btn btn-dark full" href="{{ route('checkout.index') }}">Tiến hành đặt hàng</a>
                        <a class="btn btn-outline full" href="{{ route('products.index') }}">Tiếp tục mua sắm</a>
                    </aside>
                </div>
            @endif
        </div>
    </section>
@endsection
