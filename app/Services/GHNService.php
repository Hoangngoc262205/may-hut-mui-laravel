<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GHNService
{
    public function provinces(): array
    {
        return $this->request()
            ->get('/master-data/province')
            ->json('data') ?? [];
    }

    public function districts(int|string $provinceId): array
    {
        return $this->request()
            ->get('/master-data/district', [
                'province_id' => (int) $provinceId,
            ])
            ->json('data') ?? [];
    }

    public function wards(int|string $districtId): array
    {
        return $this->request()
            ->get('/master-data/ward', [
                'district_id' => (int) $districtId,
            ])
            ->json('data') ?? [];
    }

    public function calculateFee(array $payload): int
    {
        $response = $this->request()
            ->post('/v2/shipping-order/fee', $this->feePayload($payload));

        if (! $response->successful()) {
            Log::warning('GHN calculate fee failed', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            throw new RuntimeException(
                $response->json('message')
                    ?: 'Không thể tính phí vận chuyển GHN.'
            );
        }

        if ((int) $response->json('code', 0) !== 200) {
            throw new RuntimeException(
                $response->json('message')
                    ?: 'GHN không thể tính phí vận chuyển.'
            );
        }

        return (int) ($response->json('data.total') ?? 0);
    }

    public function createOrder(array $payload): array
    {
        $response = $this->request()
            ->post('/v2/shipping-order/create', $payload);

        if (! $response->successful()) {
            throw new RuntimeException(
                $response->json('message')
                    ?: 'Không thể tạo đơn GHN.'
            );
        }

        if ((int) $response->json('code', 0) !== 200) {
            throw new RuntimeException(
                $response->json('message')
                    ?: 'GHN không thể tạo đơn.'
            );
        }

        return $response->json('data') ?? [];
    }

    public function cancelOrder(string $orderCode): array
    {
        $response = $this->request()
            ->post('/v2/switch-status/cancel', [
                'order_codes' => [$orderCode],
            ]);

        if (! $response->successful()) {
            throw new RuntimeException(
                $response->json('message')
                    ?: 'Không thể hủy đơn GHN.'
            );
        }

        if ((int) $response->json('code', 0) !== 200) {
            throw new RuntimeException(
                $response->json('message')
                    ?: 'GHN không thể hủy đơn.'
            );
        }

        return $response->json('data') ?? [];
    }

    private function request(): PendingRequest
    {
        $token = config('services.ghn.token');
        $baseUrl = rtrim((string) config('services.ghn.base_url'), '/');

        if (! $token) {
            throw new RuntimeException(
                'Thiếu GHN_TOKEN trong file .env.'
            );
        }

        if (! $baseUrl) {
            throw new RuntimeException(
                'Thiếu GHN_BASE_URL trong file .env.'
            );
        }

        if (! config('services.ghn.shop_id')) {
            throw new RuntimeException(
                'Thiếu GHN_SHOP_ID trong file .env.'
            );
        }

        if (! config('services.ghn.from_district_id')) {
            throw new RuntimeException(
                'Thiếu GHN_FROM_DISTRICT_ID trong file .env.'
            );
        }

        /*
         * .env của em đã chứa:
         * /shiip/public-api
         *
         * Vì vậy không nối thêm /shiip/public-api ở đây.
         */
        $apiBaseUrl = $baseUrl;

if (! str_ends_with($apiBaseUrl, '/shiip/public-api')) {
    $apiBaseUrl .= '/shiip/public-api';
}
        return Http::baseUrl($apiBaseUrl)
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                'Token' => $token,
                'ShopId' => (string) config('services.ghn.shop_id'),
                'Content-Type' => 'application/json',
            ])
            ->withOptions([
                'verify' => false,

                // Ép PHP cURL dùng IPv4.
                // Giúp tránh lỗi SSL timeout trên một số máy Windows/XAMPP.
                'curl' => [
                    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                ],
            ])
            ->connectTimeout(15)
            ->timeout(45)
            ->retry(2, 1000);
    }

    private function feePayload(array $payload): array
    {
        return [
            'from_district_id' => (int) config(
                'services.ghn.from_district_id'
            ),

            'service_type_id' => (int) config(
                'services.ghn.service_type_id',
                2
            ),

            'to_district_id' => (int) $payload['district_id'],

            'to_ward_code' => (string) $payload['ward_code'],

            'height' => (int) config(
                'services.ghn.package.height',
                10
            ),

            'length' => (int) config(
                'services.ghn.package.length',
                20
            ),

            'weight' => (int) config(
                'services.ghn.package.weight',
                1000
            ),

            'width' => (int) config(
                'services.ghn.package.width',
                20
            ),

            'insurance_value' => (int) (
                $payload['insurance_value'] ?? 0
            ),
        ];
    }
}