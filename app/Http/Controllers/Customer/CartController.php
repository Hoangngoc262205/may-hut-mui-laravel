<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cart = $this->cart()->load('items.variant.product.images', 'items.variant.size', 'items.variant.color');

        return view('cart.index', compact('cart'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_variant_id' => ['required', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ], [
            'product_variant_id.required' => 'Vui lòng chọn phân loại sản phẩm.',
            'quantity.min' => 'Số lượng phải lớn hơn 0.',
        ]);

        $variant = ProductVariant::with('product')->active()->findOrFail($data['product_variant_id']);

        if ($variant->stock < $data['quantity']) {
            return back()->withInput()->with('error', 'Số lượng vượt quá tồn kho hiện có.');
        }

        $cart = $this->cart();
        $item = $cart->items()->where('product_variant_id', $variant->id)->first();
        $newQuantity = ($item?->quantity ?? 0) + (int) $data['quantity'];

        if ($newQuantity > $variant->stock) {
            return back()->withInput()->with('error', 'Giỏ hàng đã có sản phẩm này, tổng số lượng vượt quá tồn kho.');
        }

        $cart->items()->updateOrCreate(
            ['product_variant_id' => $variant->id],
            ['quantity' => $newQuantity, 'price' => $variant->price]
        );

        if ($request->boolean('buy_now')) {
            return redirect()->route('checkout.index')->with('success', 'Sản phẩm đã sẵn sàng để đặt hàng.');
        }

        return back()->with('success', 'Đã thêm sản phẩm vào giỏ hàng.');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        abort_unless($cartItem->cart->user_id === Auth::id(), 403);

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ], [
            'quantity.min' => 'Số lượng phải lớn hơn 0.',
        ]);

        $variant = $cartItem->variant;

        if ($data['quantity'] > $variant->stock) {
            return back()->with('error', 'Số lượng vượt quá tồn kho hiện có.');
        }

        $cartItem->update([
            'quantity' => $data['quantity'],
            'price' => $variant->price,
        ]);

        return back()->with('success', 'Đã cập nhật giỏ hàng.');
    }

    public function destroy(CartItem $cartItem)
    {
        abort_unless($cartItem->cart->user_id === Auth::id(), 403);
        $cartItem->delete();

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }

    private function cart(): Cart
    {
        return Cart::firstOrCreate(['user_id' => Auth::id()]);
    }
}
