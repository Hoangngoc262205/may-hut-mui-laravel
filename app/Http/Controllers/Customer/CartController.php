<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService)
    {
    }

    /**
     * Hiển thị giỏ hàng của người dùng hiện tại kèm kiểm tra tồn kho.
     */
    public function index()
    {
        $cart = $this->cartService->getCart(Auth::id());

        return view('cart.index', compact('cart'));
    }

    /**
     * Thêm sản phẩm vào giỏ hàng thông qua AddToCartRequest & CartService.
     */
    public function store(AddToCartRequest $request)
    {
        $result = $this->cartService->addToCart(
            userId: Auth::id(),
            variantId: (int) $request->validated('product_variant_id'),
            quantity: (int) $request->validated('quantity')
        );

        if (! $result['success']) {
            return back()->withInput()->with('error', $result['message']);
        }

        if ($request->boolean('buy_now')) {
            return redirect()
                ->route('checkout.index')
                ->with('success', 'Sản phẩm đã được thêm và sẵn sàng để đặt hàng.');
        }

        return back()->with('success', $result['message']);
    }

    /**
     * Cập nhật số lượng của một mặt hàng trong giỏ.
     */
    public function update(Request $request, CartItem $cartItem)
    {
        abort_unless(
            $cartItem->cart && (int) $cartItem->cart->user_id === (int) Auth::id(),
            403
        );

        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ], [
            'quantity.required' => 'Vui lòng nhập số lượng.',
            'quantity.min' => 'Số lượng phải lớn hơn 0.',
        ]);

        $result = $this->cartService->updateQuantity(
            userId: Auth::id(),
            cartItemId: $cartItem->id,
            quantity: (int) $request->input('quantity')
        );

        if (! $result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
    }

    /**
     * Xóa một mặt hàng khỏi giỏ.
     */
    public function destroy(CartItem $cartItem)
    {
        abort_unless(
            $cartItem->cart && (int) $cartItem->cart->user_id === (int) Auth::id(),
            403
        );

        $this->cartService->removeItem(Auth::id(), $cartItem->id);

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }

    /**
     * Dọn sạch toàn bộ giỏ hàng.
     */
    public function clear()
    {
        $this->cartService->clearCart(Auth::id());

        return back()->with('success', 'Đã làm trống giỏ hàng.');
    }
}