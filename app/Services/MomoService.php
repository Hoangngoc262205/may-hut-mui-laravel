<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MomoService
{
    public function createPayment(Order $order, PaymentTransaction $transaction): array
    {
        $endpoint = config('services.momo.endpoint');
        $partnerCode = (string) config('services.momo.partner_code');
        $accessKey = (string) config('services.momo.access_key');
        $secretKey = (string) config('services.momo.secret_key');

        if (! $endpoint || ! $partnerCode || ! $accessKey || ! $secretKey) {
            throw new RuntimeException('Chưa cấu hình đầy đủ thông tin MoMo trong file .env.');
        }

        $amount = (string) ((int) $order->total);
        $orderId = $order->order_code.'-'.$transaction->id.'-'.time();
        $requestId = (string) now()->timestamp.'-'.$transaction->id;
        $orderInfo = 'Thanh toan don hang #'.$order->order_code;
        $redirectUrl = config('services.momo.redirect_url') ?: route('payment.momo.callback');
        $ipnUrl = config('services.momo.ipn_url') ?: route('payment.momo.ipn');
        $extraData = base64_encode(json_encode([
            'order_id' => $order->id,
            'order_code' => $order->order_code,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $requestType = 'payWithCC';

        $rawHash = 'accessKey='.$accessKey
            .'&amount='.$amount
            .'&extraData='.$extraData
            .'&ipnUrl='.$ipnUrl
            .'&orderId='.$orderId
            .'&orderInfo='.$orderInfo
            .'&partnerCode='.$partnerCode
            .'&redirectUrl='.$redirectUrl
            .'&requestId='.$requestId
            .'&requestType='.$requestType;

        $payload = [
            'partnerCode' => $partnerCode,
            'partnerName' => config('app.name', 'May Hut Mui Store'),
            'storeId' => 'MayHutMuiStore',
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => hash_hmac('sha256', $rawHash, $secretKey),
        ];

        $transaction->update([
            'gateway_order_id' => $orderId,
            'request_payload' => $payload,
            'status' => 'initiated',
        ]);

        $response = Http::withOptions([
            'verify' => filter_var(config('services.momo.verify_ssl', true), FILTER_VALIDATE_BOOLEAN),
        ])->timeout(30)->post($endpoint, $payload);

        $result = $response->json() ?? [];
        $transaction->update([
            'response_payload' => $result,
            'result_code' => isset($result['resultCode']) ? (int) $result['resultCode'] : null,
            'message' => $result['message'] ?? null,
            'status' => isset($result['payUrl']) ? 'initiated' : 'failed',
        ]);

        return $result;
    }

    public function isSuccessful(array $payload): bool
    {
        return (string) ($payload['resultCode'] ?? '') === '0';
    }

    public function isValidSuccessfulResponse(array $payload): bool
    {
        return $this->isValidResponse($payload) && $this->isSuccessful($payload);
    }

    public function isValidResponse(array $payload): bool
    {
        if (! isset($payload['signature'])) return false;

        $accessKey = (string) config('services.momo.access_key');
        $secretKey = (string) config('services.momo.secret_key');
        if ($accessKey === '' || $secretKey === '') return false;

        $rawHash = 'accessKey='.$accessKey
            .'&amount='.($payload['amount'] ?? '')
            .'&extraData='.($payload['extraData'] ?? '')
            .'&message='.($payload['message'] ?? '')
            .'&orderId='.($payload['orderId'] ?? '')
            .'&orderInfo='.($payload['orderInfo'] ?? '')
            .'&orderType='.($payload['orderType'] ?? '')
            .'&partnerCode='.($payload['partnerCode'] ?? '')
            .'&payType='.($payload['payType'] ?? '')
            .'&requestId='.($payload['requestId'] ?? '')
            .'&responseTime='.($payload['responseTime'] ?? '')
            .'&resultCode='.($payload['resultCode'] ?? '')
            .'&transId='.($payload['transId'] ?? '');

        return hash_equals(hash_hmac('sha256', $rawHash, $secretKey), (string) $payload['signature']);
    }

    public function markPaid(PaymentTransaction $transaction, array $payload): void
    {
        $transaction->update([
            'transaction_id' => $payload['transId'] ?? null,
            'result_code' => (int) ($payload['resultCode'] ?? 0),
            'message' => $payload['message'] ?? null,
            'response_payload' => $payload,
            'status' => 'paid',
            'paid_at' => Carbon::now(),
        ]);
    }

    public function markFailed(PaymentTransaction $transaction, array $payload): void
    {
        $transaction->update([
            'transaction_id' => $payload['transId'] ?? null,
            'result_code' => isset($payload['resultCode']) ? (int) $payload['resultCode'] : null,
            'message' => $payload['message'] ?? null,
            'response_payload' => $payload,
            'status' => 'failed',
        ]);
    }

    public function orderId(array $payload): ?int
    {
        $extraData = $payload['extraData'] ?? null;
        if (! is_string($extraData) || $extraData === '') return null;
        $decoded = json_decode(base64_decode($extraData, true) ?: '', true);
        if (is_array($decoded) && isset($decoded['order_id']) && is_numeric($decoded['order_id'])) {
            return (int) $decoded['order_id'];
        }
        return is_numeric($extraData) ? (int) $extraData : null;
    }
}
