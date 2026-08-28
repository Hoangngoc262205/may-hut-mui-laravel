@extends('layouts.app')

@section('title', 'Đăng ký')

@section('content')
<section class="auth-page">
    <form method="POST" action="{{ route('register.post') }}" class="auth-card" data-loading-form>
        @csrf

        <span class="eyebrow">New account</span>
        <h1>Tạo tài khoản</h1>

        <label for="register-name">Họ tên</label>
        <input id="register-name" name="name" value="{{ old('name') }}" placeholder="Nhập họ tên" required>

        <label for="register-email">Email</label>
        <input id="register-email" type="email" name="email" value="{{ old('email') }}" placeholder="Nhập email" required>

        <label for="register-phone">Số điện thoại</label>
        <input id="register-phone" name="phone" value="{{ old('phone') }}" placeholder="Nhập số điện thoại">

        <label for="register-password">Mật khẩu</label>
        <input id="register-password" type="password" name="password" placeholder="Nhập mật khẩu" required>

        <label for="register-password-confirm">Xác nhận mật khẩu</label>
        <input id="register-password-confirm" type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu" required>

        <button class="btn btn-dark full" type="submit">Tạo tài khoản</button>

        <p>Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a></p>
    </form>
</section>
@endsection
