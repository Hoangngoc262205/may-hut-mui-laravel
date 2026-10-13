@extends('layouts.app')

@section('title', 'Thanh toán')

@section('content')
    <section class="page-head">
        <div class="container">
            <span class="eyebrow">Checkout</span>
            <h1>Thông tin đặt hàng</h1>
            <p>Chọn địa chỉ giao hàng để hệ thống tính phí GHN trước khi đặt đơn.</p>
        </div>
    </section>

    <section class="section">
        <div class="container checkout-layout">
            <form method="POST"
                  action="{{ route('checkout.store') }}"
                  class="checkout-form-card"
                  data-checkout-form>
                @csrf

                {{-- 1. THÔNG TIN NGƯỜI NHẬN --}}
                <div class="checkout-card-title">
                    <span class="checkout-title-icon">1</span>
                    <div>
                        <h2>Thông tin người nhận</h2>
                        <p>Dữ liệu này sẽ được lưu vào đơn hàng.</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="checkout-form-group">
                        <label>Họ tên</label>
                        <input
                            name="customer_name"
                            value="{{ old('customer_name', auth()->user()->name) }}"
                            required
                        >
                        @error('customer_name')
                            <small class="checkout-field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="checkout-form-group">
                        <label>Số điện thoại</label>
                        <input
                            name="phone"
                            value="{{ old('phone', auth()->user()->phone) }}"
                            required
                        >
                        @error('phone')
                            <small class="checkout-field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="checkout-form-group full">
                        <label>Email</label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email', auth()->user()->email) }}"
                        >
                        @error('email')
                            <small class="checkout-field-error">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                {{-- 2. ĐỊA CHỈ --}}
                <div class="checkout-card-title">
                    <span class="checkout-title-icon">2</span>
                    <div>
                        <h2>Địa chỉ giao hàng</h2>
                        <p>Tỉnh, huyện và xã được tải từ GHN.</p>
                    </div>
                </div>

                <div class="checkout-location-grid">
                    <div class="checkout-form-group">
                        <label>Tỉnh/Thành phố</label>
                        <select
                            id="province_select"
                            name="province_id"
                            required
                            data-old="{{ old('province_id') }}"
                        >
                            <option value="">Đang tải tỉnh/thành...</option>
                        </select>

                        <input
                            type="hidden"
                            name="province_name"
                            id="province_name"
                            value="{{ old('province_name') }}"
                        >

                        @error('province_id')
                            <small class="checkout-field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="checkout-form-group">
                        <label>Quận/Huyện</label>
                        <select
                            id="district_select"
                            name="district_id"
                            required
                            data-old="{{ old('district_id') }}"
                            disabled
                        >
                            <option value="">Chọn tỉnh trước</option>
                        </select>

                        <input
                            type="hidden"
                            name="district_name"
                            id="district_name"
                            value="{{ old('district_name') }}"
                        >

                        @error('district_id')
                            <small class="checkout-field-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="checkout-form-group">
                        <label>Phường/Xã</label>
                        <select
                            id="ward_select"
                            name="ward_code"
                            required
                            data-old="{{ old('ward_code') }}"
                            disabled
                        >
                            <option value="">Chọn huyện trước</option>
                        </select>

                        <input
                            type="hidden"
                            name="ward_name"
                            id="ward_name"
                            value="{{ old('ward_name') }}"
                        >

                        @error('ward_code')
                            <small class="checkout-field-error">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="checkout-form-group">
                    <label>Địa chỉ cụ thể</label>
                    <input
                        name="address"
                        value="{{ old('address', auth()->user()->address) }}"
                        placeholder="Số nhà, tên đường..."
                        required
                    >

                    @error('address')
                        <small class="checkout-field-error">{{ $message }}</small>
                    @enderror
                </div>

                <div class="checkout-form-group">
                    <label>Ghi chú</label>
                    <textarea
                        name="note"
                        rows="4"
                        placeholder="Thời gian giao, lưu ý lắp đặt..."
                    >{{ old('note') }}</textarea>

                    @error('note')
                        <small class="checkout-field-error">{{ $message }}</small>
                    @enderror

                    @error('shipping_fee')
                        <small class="checkout-field-error">{{ $message }}</small>
                    @enderror
                </div>

                {{-- 3. PHƯƠNG THỨC THANH TOÁN --}}
                <div class="checkout-card-title">
                    <span class="checkout-title-icon">3</span>
                    <div>
                        <h2>Phương thức thanh toán</h2>
                        <p>Chọn phương thức thanh toán cho đơn hàng.</p>
                    </div>
                </div>

                <div class="checkout-payment-options">

                    <label class="checkout-payment-option">
                        <input
                            type="radio"
                            name="payment_method"
                            value="cod"
                            {{ old('payment_method', 'cod') === 'cod' ? 'checked' : '' }}
                        >

                        <span class="checkout-payment-content">
                            <strong>Thanh toán khi nhận hàng (COD)</strong>
                            <small>Thanh toán trực tiếp cho nhân viên giao hàng khi nhận sản phẩm.</small>
                        </span>
                    </label>

                    <label class="checkout-payment-option">
                        <input
                            type="radio"
                            name="payment_method"
                            value="momo"
                            {{ old('payment_method') === 'momo' ? 'checked' : '' }}
                        >

                        <span class="checkout-payment-content">
                            <strong>Thanh toán qua MoMo</strong>
                            <small>Chuyển sang cổng thanh toán MoMo để thực hiện thanh toán.</small>
                        </span>
                    </label>

                </div>

                @error('payment_method')
                    <small class="checkout-field-error">{{ $message }}</small>
                @enderror

                {{-- SECURITY --}}
                <div class="checkout-security">
                    <span>!</span>
                    <p>
                        <strong>Backend tự tính lại.</strong>
                        Giá sản phẩm, tồn kho, phí vận chuyển và tổng tiền đều được kiểm tra lại khi đặt hàng.
                    </p>
                </div>

                <button
                    class="checkout-submit"
                    type="submit"
                    id="placeOrderButton"
                    disabled
                >
                    Đặt hàng
                </button>
            </form>

            {{-- ORDER SUMMARY --}}
            <aside class="checkout-summary">
                <div class="checkout-summary-header">
                    <h2>Đơn hàng của bạn</h2>
                    <span>{{ $cart->items->sum('quantity') }} sản phẩm</span>
                </div>

                <div class="checkout-products">
                    @foreach($cart->items as $item)
                        <div class="checkout-product">
                            <div class="checkout-product-image">
                                <img
                                    src="{{ asset($item->variant?->image_path ?? 'images/products/hood-default.svg') }}"
                                    alt="{{ $item->variant?->product?->name }}"
                                >

                                <span class="checkout-quantity">
                                    {{ $item->quantity }}
                                </span>
                            </div>

                            <div class="checkout-product-info">
                                <h3>{{ $item->variant?->product?->name }}</h3>

                                <p>
                                    {{ $item->variant?->variant_name }}
                                    · SKU {{ $item->variant?->sku }}
                                </p>

                                <span>
                                    {{ number_format($item->price, 0, ',', '.') }}đ
                                </span>
                            </div>

                            <b class="checkout-product-price">
                                {{ number_format($item->subtotal, 0, ',', '.') }}đ
                            </b>
                        </div>
                    @endforeach
                </div>

                <div class="checkout-summary-divider"></div>

                <div class="checkout-summary-line">
                    <span>Tạm tính</span>

                    <strong
                        id="checkoutSubtotal"
                        data-value="{{ $cart->total }}"
                    >
                        {{ number_format($cart->total, 0, ',', '.') }}đ
                    </strong>
                </div>

                <div class="checkout-summary-line">
                    <span>Phí vận chuyển</span>

                    <strong id="shippingFeeText">
                        Chọn địa chỉ
                    </strong>
                </div>

                <div
                    id="shippingMessage"
                    class="checkout-shipping-message"
                ></div>

                <div class="checkout-summary-divider"></div>

                <div class="checkout-total">
                    <div>
                        <span>Tổng thanh toán</span>
                        <small>
                            Đã gồm phí vận chuyển GHN khi tính được
                        </small>
                    </div>

                    <strong id="checkoutTotal">
                        {{ number_format($cart->total, 0, ',', '.') }}đ
                    </strong>
                </div>

                <div class="checkout-note">
                    Nếu GHN chưa được cấu hình hoặc API lỗi, trang sẽ báo rõ
                    và không cho đặt đơn với tổng tiền sai.
                </div>
            </aside>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        window.checkoutRoutes = {
            provinces: @json(route('locations.provinces')),
            districts: @json(url('/locations/districts')),
            wards: @json(url('/locations/wards')),
            fee: @json(route('locations.calculate-fee')),
        };
    </script>
@endpush