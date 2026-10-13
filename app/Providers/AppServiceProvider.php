<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Cấu hình email xác thực tài khoản chuyên nghiệp theo nhận diện thương hiệu
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('【Máy Hút Mùi Store】Xác thực địa chỉ email tài khoản của bạn')
                ->greeting('Xin chào ' . $notifiable->name . '!')
                ->line('Cảm ơn bạn đã đăng ký tài khoản tại hệ thống Máy Hút Mùi Store - Chuyên thiết bị bếp cao cấp.')
                ->line('Vui lòng nhấn vào nút bên dưới để hoàn tất việc xác thực địa chỉ email và kích hoạt tài khoản:')
                ->action('Xác Thực Tài Khoản Ngay', $url)
                ->line('Liên kết xác thực này sẽ có hiệu lực trong vòng 60 phút vì lý do bảo mật.')
                ->line('Nếu bạn không thực hiện đăng ký tài khoản này, vui lòng bỏ qua email hoặc liên hệ tổng đài 1900 1234.')
                ->salutation("Trân trọng,\nĐội ngũ Máy Hút Mùi Store");
        });
    }
}
