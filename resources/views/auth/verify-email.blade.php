@extends('layouts.app')

@section('title', 'Xác thực tài khoản email - Máy Hút Mùi Store')

@section('content')
<section class="auth-page verify-email-section">
    <div class="auth-container">
        <div class="auth-card verification-card-pro">
            {{-- HEADER VỚI ICON ĐỒNG HỒ / PHONG THƯ PHÁT SÁNG --}}
            <div class="verify-icon-wrapper">
                <div class="verify-icon-pulse"></div>
                <div class="verify-icon-box">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="16" x="2" y="4" rx="3"></rect>
                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                    </svg>
                </div>
            </div>

            <div class="auth-header">
                <span class="eyebrow-pill">BẢO MẬT TÀI KHOẢN</span>
                <h1>Xác thực địa chỉ Email</h1>
                <p class="verify-lead">
                    Chúng tôi vừa gửi liên kết kích hoạt tài khoản đến địa chỉ email:
                </p>
                <div class="email-badge-display">
                    <span class="email-icon">✉</span>
                    <strong class="user-email-text">{{ auth()->user()->email }}</strong>
                    <span class="status-pill-amber">Chờ xác thực</span>
                </div>
            </div>

            @if (session('success') || session('status') == 'verification-link-sent')
                <div class="verify-alert-success">
                    <span class="check-icon">✓</span>
                    <div>
                        <strong>Đã gửi lại liên kết!</strong>
                        <p>Một email xác thực mới đã được chuyển đến hộp thư của bạn. Vui lòng kiểm tra lại.</p>
                    </div>
                </div>
            @endif

            {{-- PHÍM TẮT MỞ HỘP THƯ NHANH --}}
            <div class="quick-mail-clients">
                <p class="quick-clients-title">Truy cập hộp thư của bạn nhanh chóng:</p>
                <div class="clients-buttons">
                    <a href="https://mail.google.com" target="_blank" rel="noopener noreferrer" class="mail-client-btn gmail-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z"/>
                        </svg>
                        <span>Mở Gmail</span>
                    </a>
                    <a href="https://outlook.live.com" target="_blank" rel="noopener noreferrer" class="mail-client-btn outlook-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                        </svg>
                        <span>Mở Outlook</span>
                    </a>
                </div>
            </div>

            {{-- HƯỚNG DẪN 3 BƯỚC --}}
            <div class="verify-instructions-box">
                <div class="instruction-item">
                    <span class="step-num">1</span>
                    <span>Kiểm tra hộp thư <strong>Hộp thư đến (Inbox)</strong> hoặc mục <strong>Spam / Thư rác</strong>.</span>
                </div>
                <div class="instruction-item">
                    <span class="step-num">2</span>
                    <span>Nhấp vào nút <strong>"Xác Thực Tài Khoản Ngay"</strong> trong email (hiệu lực trong 60 phút).</span>
                </div>
                <div class="instruction-item">
                    <span class="step-num">3</span>
                    <span>Sau khi xác thực, bạn có thể đặt hàng và theo dõi bảo hành máy hút mùi ngay lập tức.</span>
                </div>
            </div>

            {{-- GỬI LẠI EMAIL VÀ ĐĂNG XUẤT --}}
            <div class="verify-actions-area">
                <form method="POST" action="{{ route('verification.send') }}" id="resendForm">
                    @csrf
                    <button class="btn-resend-email" type="submit" id="resendBtn">
                        <span class="btn-icon">↻</span>
                        <span id="resendBtnText">Gửi lại email xác thực</span>
                    </button>
                </form>

                <div class="verify-footer-links">
                    <span class="text-muted">Không phải tài khoản của bạn?</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline-logout-form">
                        @csrf
                        <button class="link-logout" type="submit">Đăng xuất & Đổi tài khoản khác</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const resendForm = document.getElementById('resendForm');
        const resendBtn = document.getElementById('resendBtn');
        const resendText = document.getElementById('resendBtnText');

        if (resendForm && resendBtn) {
            resendForm.addEventListener('submit', function () {
                resendBtn.disabled = true;
                resendBtn.classList.add('disabled');
                let seconds = 60;
                resendText.textContent = `Vui lòng đợi (${seconds}s)...`;

                const interval = setInterval(() => {
                    seconds--;
                    if (seconds <= 0) {
                        clearInterval(interval);
                        resendBtn.disabled = false;
                        resendBtn.classList.remove('disabled');
                        resendText.textContent = 'Gửi lại email xác thực';
                    } else {
                        resendText.textContent = `Vui lòng đợi (${seconds}s)...`;
                    }
                }, 1000);
            });
        }
    });
</script>
@endpush
@endsection
