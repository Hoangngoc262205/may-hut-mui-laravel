<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\FinanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function __construct(private readonly FinanceService $financeService)
    {
    }

    /**
     * Dashboard tài chính và tổng quan doanh thu.
     */
    public function index(Request $request)
    {
        $orders = $this->financeService
            ->getOrdersQuery($request)
            ->paginate(15)
            ->withQueryString();

        $summary = $this->financeService->getFinanceSummary($request);

        return view('admin.finance.index', array_merge(['orders' => $orders], $summary));
    }

    /**
     * Danh sách lịch sử giao dịch thanh toán.
     */
    public function transactions(Request $request)
    {
        $transactions = $this->financeService
            ->getTransactionsQuery($request)
            ->paginate(15)
            ->withQueryString();

        return view('admin.finance.transactions', compact('transactions'));
    }

    /**
     * Cập nhật trạng thái thanh toán (COD được phép thủ công, MoMo tự động).
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => [
                'required',
                'in:pending,paid,failed,refund_pending,refunded',
            ],
            'current_payment_status' => ['nullable', 'string'],
            'current_order_status' => ['nullable', 'string'],
            'current_payment_id' => ['nullable', 'integer'],
        ]);

        $payment = $order->paymentTransactions()
            ->orderByRaw("
                CASE status
                    WHEN 'paid' THEN 1
                    WHEN 'refund_pending' THEN 2
                    WHEN 'refunded' THEN 3
                    ELSE 4
                END
            ")
            ->orderByDesc('id')
            ->first();

        if (! $payment) {
            return back()->with('error', 'Đơn hàng chưa có giao dịch thanh toán.');
        }

        if ($payment->gateway !== 'cod') {
            return back()->with('error', 'Không thể cập nhật trạng thái thanh toán MoMo thủ công.');
        }

        // Optimistic Locking Check
        if ($request->filled('current_payment_id') && (int) $request->current_payment_id !== (int) $payment->id) {
            return back()->with('error', 'Giao dịch đã thay đổi. Vui lòng tải lại trang.');
        }

        if ($request->filled('current_payment_status') && $request->current_payment_status !== $payment->status) {
            return back()->with('error', 'Trạng thái thanh toán đã thay đổi. Vui lòng tải lại trang.');
        }

        if ($request->filled('current_order_status') && $request->current_order_status !== $order->status) {
            return back()->with('error', 'Trạng thái đơn hàng đã thay đổi. Vui lòng tải lại trang.');
        }

        $newStatus = $request->input('status');

        $allowedTransitions = [
            'pending' => ['pending', 'paid', 'failed'],
            'failed' => ['failed', 'pending', 'paid'],
            'paid' => ['paid', 'refund_pending'],
            'refund_pending' => ['refund_pending', 'refunded'],
            'refunded' => ['refunded'],
            'cancelled' => ['cancelled'],
        ];

        $currentStatus = $payment->status;

        if (! isset($allowedTransitions[$currentStatus]) || ! in_array($newStatus, $allowedTransitions[$currentStatus], true)) {
            return back()->with('error', "Không thể chuyển trạng thái từ {$currentStatus} sang {$newStatus}.");
        }

        DB::transaction(function () use ($payment, $order, $newStatus) {
            $payment->update([
                'status' => $newStatus,
                'paid_at' => $newStatus === 'paid' ? now() : $payment->paid_at,
            ]);

            if ($newStatus === 'paid') {
                $order->update(['status' => 'paid']);
            }
        });

        return back()->with('success', 'Đã cập nhật trạng thái thanh toán.');
    }
}