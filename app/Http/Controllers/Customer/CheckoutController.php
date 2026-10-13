<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Models\Cart;
use App\Services\CheckoutService;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function __construct(private readonly CheckoutService $checkoutService)
    {
    }

    /**
     * Hiển thị màn hình thanh toán đơn hàng.
     */
    public function index()
    {
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()])
            ->load([
                'items.variant.product.images',
                'items.variant.size',
                'items.variant.color',
            ]);

        if ($cart->items->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Giỏ hàng đang trống.');
        }

        return view('checkout.index', compact('cart'));
    }

    /**
     * Xử lý tạo đơn hàng từ CheckoutRequest & CheckoutService.
     */
    public function store(CheckoutRequest $request)
    {
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()])
            ->load([
                'items.variant.product',
                'items.variant.size',
                'items.variant.color',
            ]);

        if ($cart->items->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Giỏ hàng đang trống.');
        }

        $order = $this->checkoutService->placeOrder($cart, $request->validated());

        // Nếu chọn MoMo: chuyển sang khởi tạo liên kết thanh toán MoMo
        if ($request->input('payment_method') === 'momo') {
            return redirect()
                ->route('orders.momo.start', $order)
                ->with('success', 'Đơn hàng đã được tạo. Đang chuyển sang thanh toán MoMo.');
        }

        // Nếu chọn COD: hoàn tất đơn hàng
        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Đặt hàng thành công. Mã đơn hàng: ' . $order->order_code);
    }
}