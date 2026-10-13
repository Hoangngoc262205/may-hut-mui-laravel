<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::query()
            ->where(
                'user_id',
                Auth::id()
            )
            ->withCount('items')
            ->latest()
            ->paginate(10);

        return view(
            'orders.index',
            compact('orders')
        );
    }

    public function show(Order $order)
    {
        /*
        |--------------------------------------------------------------------------
        | Chỉ chủ đơn hàng mới được xem
        |--------------------------------------------------------------------------
        */

        abort_unless(
            (int) $order->user_id ===
                (int) Auth::id(),
            403
        );

        /*
        |--------------------------------------------------------------------------
        | Load dữ liệu đơn hàng
        |--------------------------------------------------------------------------
        */

        $order->load([
            'items.variant.product.images',
            'items.variant.size',
            'items.variant.color',
        ]);

        return view(
            'orders.show',
            compact('order')
        );
    }
}