<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case PROCESSING = 'processing';
    case SHIPPING = 'shipping';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Chờ xử lý',
            self::CONFIRMED => 'Đã xác nhận',
            self::PROCESSING => 'Đang chuẩn bị hàng',
            self::SHIPPING => 'Đang giao hàng',
            self::DELIVERED => 'Đã giao thành công',
            self::CANCELLED => 'Đã hủy',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'badge-warning',
            self::CONFIRMED => 'badge-info',
            self::PROCESSING => 'badge-primary',
            self::SHIPPING => 'badge-indigo',
            self::DELIVERED => 'badge-success',
            self::CANCELLED => 'badge-danger',
        };
    }
}
