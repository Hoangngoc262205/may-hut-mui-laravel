<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\GHNOrderService;
use App\Services\MomoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class MomoController extends Controller
{
    public function start(Order $order, MomoService $momo)
    {
        abort_unless((int) $order->user_id === (int) Auth::id(), 403);

        if ($order->status === 'paid') {
            return redirect()->route('orders.show', $order)
                ->with('success', 'Đơn hàng này đã được thanh toán.');
        }

        $transaction = $order->paymentTransactions()
            ->where('gateway', 'momo')
            ->whereIn('status', ['pending', 'initiated'])
            ->latest()
            ->first();

        if (! $transaction) {
            $transaction = $this->newTransaction($order);
        }

        return $this->redirectToMomo($order, $transaction, $momo);
    }

    public function payAgain(Order $order, MomoService $momo)
    {
        abort_unless((int) $order->user_id === (int) Auth::id(), 403);

        if (in_array($order->status, ['paid', 'completed', 'cancelled'], true)) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Đơn hàng hiện không thể thanh toán lại.');
        }

        return $this->redirectToMomo($order, $this->newTransaction($order), $momo);
    }

    public function callback(Request $request, GHNOrderService $ghnOrders, MomoService $momo)
    {
        Log::info('MoMo callback received', [
            'payload' => $request->except('signature'),
            'has_signature' => $request->has('signature'),
        ]);

        $payload = $request->all();

        if (! $momo->isValidResponse($payload)) {
            return redirect()->route('orders.index')->with('error', 'Phản hồi MoMo không hợp lệ.');
        }

        if (! $momo->isSuccessful($payload)) {
            $this->markFailed($payload, $momo);
            return redirect()->route('orders.index')->with('error', 'Giao dịch MoMo thất bại.');
        }

        $result = $this->completePayment($payload, $ghnOrders, $momo);

        return redirect()->route('orders.index')->with(
            $result === 'created' || $result === 'already_created' ? 'success' : 'warning',
            $result === 'created'
                ? 'Thanh toán MoMo thành công! Vận đơn GHN đã được khởi tạo.'
                : ($result === 'already_created'
                    ? 'Thanh toán MoMo đã được ghi nhận.'
                    : 'Thanh toán thành công. Đơn hàng đang chờ tạo vận đơn GHN.')
        );
    }

    public function ipn(Request $request, GHNOrderService $ghnOrders, MomoService $momo)
    {
        $payload = $request->all();

        Log::info('MoMo IPN received', [
            'payload' => $request->except('signature'),
            'has_signature' => $request->has('signature'),
        ]);

        if (! $momo->isValidResponse($payload)) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        if ($momo->isSuccessful($payload)) {
            $this->completePayment($payload, $ghnOrders, $momo);
        } else {
            $this->markFailed($payload, $momo);
        }

        return response()->json(['message' => 'Received']);
    }

    private function newTransaction(Order $order): PaymentTransaction
    {
        return PaymentTransaction::create([
            'order_id' => $order->id,
            'gateway' => 'momo',
            'amount' => $order->total,
            'status' => 'pending',
        ]);
    }

    private function redirectToMomo(Order $order, PaymentTransaction $transaction, MomoService $momo)
    {
        try {
            $result = $momo->createPayment($order, $transaction);
        } catch (Throwable $exception) {
            report($exception);
            return redirect()->route('orders.show', $order)
                ->with('error', 'Không thể kết nối tới MoMo: '.$exception->getMessage());
        }

        if (! empty($result['payUrl'])) {
            return redirect($result['payUrl']);
        }

        return redirect()->route('orders.show', $order)
            ->with('error', 'MoMo từ chối tạo phiên thanh toán: '.($result['message'] ?? 'Không rõ lỗi.'));
    }

    private function completePayment(array $payload, GHNOrderService $ghnOrders, MomoService $momo): string
    {
        $result = DB::transaction(function () use ($payload, $momo) {
            $transaction = PaymentTransaction::where('gateway', 'momo')
                ->where('gateway_order_id', $payload['orderId'] ?? '')
                ->lockForUpdate()
                ->first();

            if (! $transaction) {
                return 'invalid';
            }

            $order = Order::lockForUpdate()->find($transaction->order_id);
            if (! $order) {
                return 'invalid';
            }

            if ((int) $transaction->amount !== (int) ($payload['amount'] ?? 0)) {
                $momo->markFailed($transaction, $payload);
                return 'invalid';
            }

            if ($transaction->status === 'paid' || $order->status === 'paid') {
                return $order->ghn_order_code ? 'already_created' : ['create', $order->id];
            }

            $momo->markPaid($transaction, $payload);
            $order->update(['status' => 'paid']);

            return ['create', $order->id];
        });

        if ($result === 'invalid') {
            return 'invalid';
        }

        if ($result === 'already_created') {
            return 'already_created';
        }

        if (! is_array($result)) {
            return (string) $result;
        }

        $order = Order::with('items')->find($result[1]);
        if (! $order) return 'invalid';

        if ($order->ghn_order_code) return 'already_created';

        try {
            $response = $ghnOrders->createForOrder($order, true);
            $order->update([
                'ghn_order_code' => $response['order_code'] ?? null,
                'ghn_fee' => $response['total_fee'] ?? $order->shipping_fee,
                'ghn_status' => 'created',
            ]);
            return 'created';
        } catch (Throwable $exception) {
            report($exception);
            $order->update(['ghn_status' => 'pending']);
            return 'failed';
        }
    }


    private function markFailed(array $payload, MomoService $momo): void
    {
        $transaction = PaymentTransaction::where('gateway', 'momo')
            ->where('gateway_order_id', $payload['orderId'] ?? '')
            ->first();

        if ($transaction && $transaction->status !== 'paid') {
            $momo->markFailed($transaction, $payload);
        }
    }
}
