@extends('layouts.app')

@section('title', 'Đăng nhập')

@section('content')
<section class="auth-page">
    <form method="POST" action="{{ route('login.post') }}" class="auth-card" data-loading-form>
        @csrf

        <span class="eyebrow">Welcome back</span>
        <h1>Đăng nhập</h1>

        <label for="login-email">Email</label>
        <input id="login-email" type="email" name="email" value="{{ old('email') }}" placeholder="Nhập email của bạn" required autofocus>

        <label for="login-password">Mật khẩu</label>
        <input id="login-password" type="password" name="password" placeholder="Nhập mật khẩu" required>

        <label class="check-row" for="remember-login">
            <input id="remember-login" type="checkbox" name="remember" value="1">
            <span>Ghi nhớ đăng nhập</span>
        </label>

        <button class="btn btn-dark full" type="submit">Đăng nhập</button>

        <p>Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a></p>
    </form>
</section>
@endsection
