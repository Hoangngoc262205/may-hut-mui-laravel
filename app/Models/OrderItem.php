<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_variant_id',

        'product_name',
        'variant_name',

        'quantity',
        'price',
        'subtotal',
    ];

    protected $casts = [
        'order_id' => 'integer',
        'product_variant_id' => 'integer',

        'quantity' => 'integer',
        'price' => 'integer',
        'subtotal' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Order
    |--------------------------------------------------------------------------
    */

    public function order()
    {
        return $this->belongsTo(
            Order::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Variant
    |--------------------------------------------------------------------------
    |
    | nullable vì variant có thể bị xóa sau khi khách đã mua.
    | Dữ liệu product_name / variant_name / price / subtotal
    | vẫn giữ nguyên để đơn cũ không bị mất thông tin.
    |
    */

    public function variant()
    {
        return $this->belongsTo(
            ProductVariant::class,
            'product_variant_id'
        );
    }
}