<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'suction_capacity',
        'noise_level',
        'speed_levels',
        'voltage',
        'material',
        'hood_type',
        'dimensions',
        'warranty',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
