<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Builder;

class CartService
{
    /**
     * Lấy hoặc tạo giỏ hàng cho người dùng kèm quan hệ chi tiết.
     */
    public function getCart(int $userId): Cart
    {
        $cart = Cart::firstOrCreate(['user_id' => $userId]);

        $cart->load([
            'items.variant.product.images',
            'items.variant.size',
            'items.variant.color',
        ]);

        foreach ($cart->items as $item) {
            $item->cart_issue = $this->detectItemIssue($item);
        }

        return $cart;
    }

    /**
     * Kiểm tra tình trạng sản phẩm trong giỏ (hết hàng, ngừng bán, thay đổi giá).
     */
    public function detectItemIssue(CartItem $item): ?string
    {
        $variant = $item->variant;

        if (! $variant) {
            return 'Phân loại sản phẩm này không còn tồn tại trên hệ thống.';
        }

        if ($variant->status !== 'active') {
            return 'Phân loại này hiện đang tạm ngừng kinh doanh.';
        }

        if (! $variant->product || $variant->product->status !== 'active') {
            return 'Sản phẩm này hiện đang tạm ngừng kinh doanh.';
        }

        if ((int) $variant->stock <= 0) {
            return 'Sản phẩm hiện đã hết hàng.';
        }

        if ((int) $item->quantity > (int) $variant->stock) {
            return 'Số lượng yêu cầu (' . $item->quantity . ') vượt quá tồn kho hiện có (' . $variant->stock . ').';
        }

        return null;
    }

    /**
     * Thêm sản phẩm vào giỏ hàng với đầy đủ kiểm tra tồn kho & trạng thái.
     */
    public function addToCart(int $userId, int $variantId, int $quantity): array
    {
        $variant = ProductVariant::query()
            ->with('product')
            ->whereKey($variantId)
            ->where('status', 'active')
            ->whereHas('product', function (Builder $query) {
                $query->where('status', 'active');
            })
            ->first();

        if (! $variant) {
            return [
                'success' => false,
                'message' => 'Sản phẩm hoặc phân loại này hiện không còn kinh doanh.',
            ];
        }

        if ((int) $variant->stock <= 0) {
            return [
                'success' => false,
                'message' => 'Phân loại sản phẩm này hiện đã hết hàng.',
            ];
        }

        $cart = Cart::firstOrCreate(['user_id' => $userId]);

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_variant_id', $variant->id)
            ->first();

        $currentQuantity = $cartItem ? (int) $cartItem->quantity : 0;
        $newQuantity = $currentQuantity + $quantity;

        if ($newQuantity > (int) $variant->stock) {
            return [
                'success' => false,
                'message' => "Kho chỉ còn {$variant->stock} sản phẩm. Bạn đã có {$currentQuantity} trong giỏ.",
            ];
        }

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $newQuantity,
                'price' => $variant->price,
            ]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_variant_id' => $variant->id,
                'quantity' => $quantity,
                'price' => $variant->price,
            ]);
        }

        return [
            'success' => true,
            'message' => 'Đã thêm sản phẩm vào giỏ hàng thành công.',
        ];
    }

    /**
     * Cập nhật số lượng của một mặt hàng trong giỏ.
     */
    public function updateQuantity(int $userId, int $cartItemId, int $quantity): array
    {
        $cart = Cart::firstOrCreate(['user_id' => $userId]);

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('id', $cartItemId)
            ->with('variant')
            ->first();

        if (! $cartItem) {
            return ['success' => false, 'message' => 'Mặt hàng không tồn tại trong giỏ.'];
        }

        $variant = $cartItem->variant;

        if (! $variant || $variant->status !== 'active') {
            return ['success' => false, 'message' => 'Phân loại này hiện ngừng bán.'];
        }

        if ($quantity > (int) $variant->stock) {
            return [
                'success' => false,
                'message' => "Số lượng vượt quá số lượng kho còn lại ({$variant->stock}).",
            ];
        }

        $cartItem->update([
            'quantity' => $quantity,
            'price' => $variant->price,
        ]);

        return ['success' => true, 'message' => 'Đã cập nhật số lượng thành công.'];
    }

    /**
     * Xóa 1 mặt hàng khỏi giỏ.
     */
    public function removeItem(int $userId, int $cartItemId): bool
    {
        $cart = Cart::firstOrCreate(['user_id' => $userId]);

        return (bool) CartItem::where('cart_id', $cart->id)
            ->where('id', $cartItemId)
            ->delete();
    }

    /**
     * Dọn sạch toàn bộ giỏ hàng.
     */
    public function clearCart(int $userId): bool
    {
        $cart = Cart::firstOrCreate(['user_id' => $userId]);

        return (bool) CartItem::where('cart_id', $cart->id)->delete();
    }
}
