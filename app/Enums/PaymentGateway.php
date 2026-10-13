<?php

namespace App\Enums;

enum PaymentGateway: string
{
    case COD = 'cod';
    case MOMO = 'momo';

    public function label(): string
    {
        return match ($this) {
            self::COD => 'Thanh toán khi nhận hàng (COD)',
            self::MOMO => 'Ví điện tử MoMo (ATM / QR Code)',
        };
    }
}
