<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ], [
            'rating.required' => 'Vui lòng chọn số sao đánh giá.',
            'rating.min' => 'Đánh giá phải từ 1 đến 5 sao.',
            'rating.max' => 'Đánh giá phải từ 1 đến 5 sao.',
        ]);

        $product->reviews()->updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null,
                'status' => 'approved',
            ]
        );

        return back()->with('success', 'Cảm ơn bạn đã đánh giá sản phẩm.');
    }
}
