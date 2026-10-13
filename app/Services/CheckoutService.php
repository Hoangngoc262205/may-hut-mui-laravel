<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentGateway;
use App\Enums\PaymentStatus;
use App\Models\Cart;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public function __construct(private readonly GHNService $ghnService)
    {
    }

    /**
     * Xử lý đặt hàng hoàn chỉnh trong Database Transaction an toàn tuyệt đối.
     */
    public function placeOrder(Cart $cart, array $data): Order
    {
        if ($cart->items->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => 'Giỏ hàng đang trống, không thể đặt hàng.',
            ]);
        }

        return DB::transaction(function () use ($cart, $data) {
            $subtotal = 0;
            $checkedItems = [];

            // 1. Khóa và kiểm tra tồn kho từng biến thể
            foreach ($cart->items as $item) {
                $variant = ProductVariant::whereKey($item->product_variant_id)
                    ->lockForUpdate()
                    ->with(['product', 'size', 'color'])
                    ->first();

                if (
                    ! $variant ||
                    $variant->status !== 'active' ||
                    $variant->stock < $item->quantity
                ) {
                    throw ValidationException::withMessages([
                        'cart' => 'Một sản phẩm trong giỏ hàng không còn đủ tồn kho. Vui lòng kiểm tra lại giỏ hàng.',
                    ]);
                }

                $lineTotal = (int) $variant->price * (int) $item->quantity;
                $subtotal += $lineTotal;

                $checkedItems[] = [$item, $variant, $lineTotal];
            }

            // 2. Tính phí vận chuyển GHN
            $shippingFee = 0;
            try {
                $shippingFee = $this->ghnService->calculateFee([
                    'district_id' => (int) $data['district_id'],
                    'ward_code' => (string) $data['ward_code'],
                ]);
            } catch (\Throwable $e) {
                // Fallback nếu API sandbox GHN không phản hồi
                $shippingFee = 35000;
            }

            // 3. Khởi tạo Đơn hàng
            $order = Order::create([
                'user_id' => $cart->user_id,
                'order_code' => 'ORD-' . strtoupper(Str::random(8)),
                'customer_name' => $data['customer_name'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'address' => $data['address'],
                'province_id' => $data['province_id'],
                'province_name' => $data['province_name'],
                'district_id' => $data['district_id'],
                'district_name' => $data['district_name'],
                'ward_code' => $data['ward_code'],
                'ward_name' => $data['ward_name'],
                'note' => $data['note'] ?? null,
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'total' => $subtotal + $shippingFee,
                'status' => OrderStatus::PENDING->value,
            ]);

            // 4. Lưu danh sách OrderItems và trừ kho
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

            // 5. Tạo Payment Transaction
            if ($data['payment_method'] === PaymentGateway::MOMO->value) {
                PaymentTransaction::create([
                    'order_id' => $order->id,
                    'gateway' => PaymentGateway::MOMO->value,
                    'gateway_order_id' => null,
                    'transaction_id' => null,
                    'amount' => $order->total,
                    'status' => PaymentStatus::PENDING->value,
                    'result_code' => null,
                    'message' => null,
                ]);
            } else {
                PaymentTransaction::create([
                    'order_id' => $order->id,
                    'gateway' => PaymentGateway::COD->value,
                    'gateway_order_id' => 'COD-' . $order->order_code,
                    'transaction_id' => null,
                    'amount' => $order->total,
                    'status' => PaymentStatus::PENDING->value,
                    'result_code' => null,
                    'message' => 'Thanh toán khi nhận hàng.',
                ]);
            }

            // 6. Xóa giỏ hàng sau khi đặt thành công
            $cart->items()->delete();

            return $order;
        });
    }
}
