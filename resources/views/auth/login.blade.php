@extends('layouts.app')

@section('title', 'Đăng nhập')

@section('content')
<section class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-badge">⌂</div>
                <span class="eyebrow">WELCOME BACK</span>
                <h1>Đăng nhập</h1>
                <p>Đăng nhập để mua sắm và quản lý đơn hàng.</p>
            </div>

            <form method="POST" action="{{ route('login.post') }}" class="auth-form" data-loading-form>
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Nhập email của bạn" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
                </div>
                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" value="1">
                        <span>Ghi nhớ đăng nhập</span>
                    </label>
                </div>
                <button type="submit" class="auth-submit">Đăng nhập</button>
            </form>

            <div class="auth-register">
                Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a>
            </div>
        </div>
    </div>
</section>
@endsection
