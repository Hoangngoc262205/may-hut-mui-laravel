@extends('layouts.app')

@section('title', 'Đăng ký')

@section('content')
<section class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-badge">+</div>
                <span class="eyebrow">NEW ACCOUNT</span>
                <h1>Tạo tài khoản</h1>
                <p>Đăng ký để bắt đầu mua sắm tại Máy Hút Mùi Store.</p>
            </div>

            <form method="POST" action="{{ route('register.post') }}" class="auth-form" data-loading-form>
                @csrf
                <div class="form-group"><label for="name">Họ tên</label><input id="name" name="name" value="{{ old('name') }}" placeholder="Nhập họ tên" required></div>
                <div class="form-group"><label for="register-email">Email</label><input id="register-email" type="email" name="email" value="{{ old('email') }}" placeholder="Nhập email" required></div>
                <div class="form-group"><label for="phone">Số điện thoại</label><input id="phone" name="phone" value="{{ old('phone') }}" placeholder="Nhập số điện thoại"></div>
                <div class="form-group"><label for="register-password">Mật khẩu</label><input id="register-password" type="password" name="password" placeholder="Tối thiểu 6 ký tự" required></div>
                <div class="form-group"><label for="password-confirmation">Xác nhận mật khẩu</label><input id="password-confirmation" type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu" required></div>
                <button class="auth-submit" type="submit">Tạo tài khoản</button>
            </form>

            <div class="auth-register">Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a></div>
        </div>
    </div>
</section>
@endsection
