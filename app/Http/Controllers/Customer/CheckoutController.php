<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()])
            ->load('items.variant.product.images', 'items.variant.size', 'items.variant.color');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng đang trống.');
        }

        return view('checkout.index', compact('cart'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'regex:/^(0|\+84)[0-9]{8,10}$/'],
            'address' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:500'],
        ], [
            'customer_name.required' => 'Vui lòng nhập họ tên.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại không hợp lệ.',
            'address.required' => 'Vui lòng nhập địa chỉ nhận hàng.',
        ]);

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()])
            ->load('items.variant.product', 'items.variant.size', 'items.variant.color');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng đang trống.');
        }

        $order = DB::transaction(function () use ($cart, $data) {
            $subtotal = 0;
            $checkedItems = [];

            foreach ($cart->items as $item) {
                $variant = ProductVariant::whereKey($item->product_variant_id)->lockForUpdate()->first();

                if (! $variant || $variant->status !== 'active' || $variant->stock < $item->quantity) {
                    throw ValidationException::withMessages([
                        'cart' => 'Một sản phẩm trong giỏ hàng không còn đủ tồn kho. Vui lòng kiểm tra lại giỏ hàng.',
                    ]);
                }

                $variant->load('product', 'size', 'color');
                $lineTotal = (int) $variant->price * (int) $item->quantity;
                $subtotal += $lineTotal;
                $checkedItems[] = [$item, $variant, $lineTotal];
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'order_code' => 'MHM'.now()->format('YmdHis').strtoupper(Str::random(4)),
                'customer_name' => $data['customer_name'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'note' => $data['note'] ?? null,
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'status' => 'pending',
            ]);

            foreach ($checkedItems as [$item, $variant, $lineTotal]) {
                $order->items()->create([
                    'product_variant_id' => $variant->id,
                    'product_name' => $variant->product->name,
                    'variant_name' => $variant->variant_name,
                    'quantity' => $item->quantity,
                    'price' => $variant->price,
                    'subtotal' => $lineTotal,
                ]);

                $variant->decrement('stock', $item->quantity);
            }

            $cart->items()->delete();

            return $order;
        });

        return redirect()->route('orders.show', $order)->with('success', 'Đặt hàng thành công. Mã đơn hàng: '.$order->order_code);
    }
}
