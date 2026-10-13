<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_code',

        'customer_name',
        'phone',
        'email',

        'address',

        'province_id',
        'province_name',

        'district_id',
        'district_name',

        'ward_code',
        'ward_name',

        'note',

        'subtotal',
        'shipping_fee',
        'total',

        'status',

        'ghn_order_code',
        'ghn_fee',
        'ghn_status',
    ];

    protected $casts = [
        'user_id' => 'integer',

        'province_id' => 'string',
        'district_id' => 'string',
        'ward_code' => 'string',

        'subtotal' => 'integer',
        'shipping_fee' => 'integer',
        'total' => 'integer',

        'ghn_fee' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | User
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Order items
    |--------------------------------------------------------------------------
    */

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Payment transactions - LAB 06
    |--------------------------------------------------------------------------
    |
    | Một Order có thể có nhiều lần thanh toán.
    |
    | Ví dụ:
    | - MoMo lần 1: failed
    | - MoMo lần 2: failed
    | - MoMo lần 3: paid
    |
    */

    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Latest payment transaction
    |--------------------------------------------------------------------------
    */

    public function latestPaymentTransaction()
    {
        return $this->hasOne(PaymentTransaction::class)
            ->latestOfMany();
    }

    /*
    |--------------------------------------------------------------------------
    | Status label
    |--------------------------------------------------------------------------
    */

    public function statusLabel(): string
    {
        return [
            'pending' =>
                'Chờ xác nhận',

            'confirmed' =>
                'Đã xác nhận',

            'processing' =>
                'Đang xử lý',

            'paid' =>
                'Đã thanh toán',

            'shipping' =>
                'Đang giao',

            'completed' =>
                'Hoàn thành',

            'cancelled' =>
                'Đã hủy',
        ][$this->status]
            ?? $this->status;
    }

    /*
    |--------------------------------------------------------------------------
    | Shipping address
    |--------------------------------------------------------------------------
    */

    public function fullShippingAddress(): string
    {
        return collect([
            $this->address,
            $this->ward_name,
            $this->district_name,
            $this->province_name,
        ])
            ->filter(
                fn ($value) =>
                    filled($value)
            )
            ->join(', ');
    }

    /*
    |--------------------------------------------------------------------------
    | GHN status
    |--------------------------------------------------------------------------
    */

    public function ghnStatusLabel(): string
    {
        return [
            'created' =>
                'Đã tạo vận đơn',

            'ready_to_pick' =>
                'Chờ lấy hàng',

            'picking' =>
                'Đang lấy hàng',

            'delivering' =>
                'Đang giao',

            'delivered' =>
                'Đã giao',

            'cancelled' =>
                'Đã hủy vận đơn',
        ][$this->ghn_status]
            ?? (
                $this->ghn_status
                    ?: 'Chưa tạo vận đơn'
            );
    }
}