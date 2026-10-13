<?php

namespace App\Services;

use App\Models\Order;
use RuntimeException;

class GHNOrderService
{
    public function __construct(
        private readonly GHNService $ghn
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Tạo vận đơn GHN từ Order
    |--------------------------------------------------------------------------
    */

    public function createForOrder(
        Order $order,
        bool $isPaid = false
    ): array {
        /*
        |--------------------------------------------------------------------------
        | Không tạo trùng vận đơn
        |--------------------------------------------------------------------------
        */

        if (
            filled($order->ghn_order_code)
        ) {
            throw new RuntimeException(
                'Đơn hàng đã có mã vận đơn GHN.'
            );
        }

        $order->loadMissing([
            'items',
        ]);

        if (
            $order->items->isEmpty()
        ) {
            throw new RuntimeException(
                'Đơn hàng không có sản phẩm.'
            );
        }

        if (
            ! in_array(
                $order->status,
                [
                    'confirmed',
                    'processing',
                ],
                true
            )
        ) {
            throw new RuntimeException(
                'Chỉ có thể tạo vận đơn GHN khi đơn đã được xác nhận hoặc đang xử lý.'
            );
        }

        $fromDistrictId =
            (int) config(
                'services.ghn.from_district_id',
                0
            );

        $fromWardCode =
            trim(
                (string) config(
                    'services.ghn.from_ward_code',
                    ''
                )
            );

        if ($fromDistrictId <= 0) {
            throw new RuntimeException(
                'Chưa cấu hình GHN_FROM_DISTRICT_ID.'
            );
        }

        if ($fromWardCode === '') {
            throw new RuntimeException(
                'Chưa cấu hình GHN_FROM_WARD_CODE.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Tính tổng trọng lượng
        |--------------------------------------------------------------------------
        |
        | Project hiện tại chưa có weight riêng cho từng variant,
        | nên sử dụng trọng lượng package cấu hình làm trọng lượng tối thiểu.
        |
        */

        $configuredWeight =
            max(
                1,
                (int) config(
                    'services.ghn.package.weight',
                    5000
                )
            );

        $totalQuantity =
            max(
                1,
                (int) $order->items->sum(
                    'quantity'
                )
            );

        $items = [];

        foreach (
            $order->items as $item
        ) {
            $items[] = [
                'name' =>
                    trim(
                        $item->product_name.
                        ' '.
                        $item->variant_name
                    ),

                'quantity' =>
                    (int) $item->quantity,

                'price' =>
                    (int) $item->price,

                'weight' =>
                    max(
                        1,
                        (int) ceil(
                            $configuredWeight /
                            $totalQuantity
                        )
                    ),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | COD
        |--------------------------------------------------------------------------
        */

        $codAmount =
            $isPaid
                ? 0
                : (int) $order->total;

        /*
        |--------------------------------------------------------------------------
        | Payload GHN
        |--------------------------------------------------------------------------
        */

        $payload = [
            'payment_type_id' =>
                (int) config(
                    'services.ghn.payment_type_id',
                    2
                ),

            'note' =>
                $order->note
                    ?: 'Don hang '.$order->order_code,

            'required_note' =>
                config(
                    'services.ghn.required_note',
                    'CHOXEMHANGKHONGTHU'
                ),

            /*
            |--------------------------------------------------------------------------
            | Người gửi
            |--------------------------------------------------------------------------
            */

            'from_name' =>
                config(
                    'services.ghn.from_name'
                ),

            'from_phone' =>
                config(
                    'services.ghn.from_phone'
                ),

            'from_address' =>
                config(
                    'services.ghn.from_address'
                ),

            'from_ward_code' =>
                $fromWardCode,

            'from_district_id' =>
                $fromDistrictId,

            /*
            |--------------------------------------------------------------------------
            | Người nhận
            |--------------------------------------------------------------------------
            */

            'to_name' =>
                $order->customer_name,

            'to_phone' =>
                $order->phone,

            'to_address' =>
                $order->address,

            'to_ward_code' =>
                (string) $order->ward_code,

            'to_district_id' =>
                (int) $order->district_id,

            /*
            |--------------------------------------------------------------------------
            | Tiền COD
            |--------------------------------------------------------------------------
            */

            'cod_amount' =>
                max(
                    0,
                    $codAmount
                ),

            /*
            |--------------------------------------------------------------------------
            | Nội dung
            |--------------------------------------------------------------------------
            */

            'content' =>
                'Don hang '.
                $order->order_code,

            /*
            |--------------------------------------------------------------------------
            | Kích thước kiện
            |--------------------------------------------------------------------------
            */

            'weight' =>
                $configuredWeight,

            'length' =>
                (int) config(
                    'services.ghn.package.length',
                    60
                ),

            'width' =>
                (int) config(
                    'services.ghn.package.width',
                    45
                ),

            'height' =>
                (int) config(
                    'services.ghn.package.height',
                    20
                ),

            'service_type_id' =>
                (int) config(
                    'services.ghn.service_type_id',
                    2
                ),

            'items' =>
                $items,
        ];

        return $this->ghn
            ->createOrder($payload);
    }

    /*
    |--------------------------------------------------------------------------
    | Hủy vận đơn
    |--------------------------------------------------------------------------
    */

    public function cancel(
        Order $order
    ): array {
        if (
            ! filled(
                $order->ghn_order_code
            )
        ) {
            throw new RuntimeException(
                'Đơn hàng chưa có mã vận đơn GHN.'
            );
        }

        return $this->ghn
            ->cancelOrder(
                (string)
                $order->ghn_order_code
            );
    }
}