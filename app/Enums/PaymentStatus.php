<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Chưa thanh toán',
            self::PAID => 'Đã thanh toán',
            self::FAILED => 'Thất bại',
            self::REFUNDED => 'Đã hoàn tiền',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'badge-warning',
            self::PAID => 'badge-success',
            self::FAILED => 'badge-danger',
            self::REFUNDED => 'badge-secondary',
        };
    }
}
