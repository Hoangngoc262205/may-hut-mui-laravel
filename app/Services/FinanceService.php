<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FinanceService
{
    /**
     * Build query danh sách đơn hàng kèm transaction thanh toán ưu tiên.
     */
    public function getOrdersQuery(Request $request)
    {
        $prioritySubquery = PaymentTransaction::query()
            ->select('id')
            ->whereColumn('payment_transactions.order_id', 'orders.id')
            ->orderByRaw("
                CASE status
                    WHEN 'paid' THEN 1
                    WHEN 'refund_pending' THEN 2
                    WHEN 'refunded' THEN 3
                    ELSE 4
                END
            ")
            ->orderByDesc('id')
            ->limit(1);

        $query = Order::query()
            ->with([
                'user',
                'paymentTransactions',
                'items',
            ])
            ->addSelect([
                'payment_transaction_id' => $prioritySubquery,
            ]);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('orders.id', $search)
                    ->orWhere('orders.order_code', 'like', "%{$search}%")
                    ->orWhere('orders.customer_name', 'like', "%{$search}%")
                    ->orWhere('orders.phone', 'like', "%{$search}%")
                    ->orWhere('orders.ghn_order_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('orders.created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('orders.created_at', '<=', $request->input('date_to'));
        }

        if ($request->filled('min_amount')) {
            $query->where('orders.total', '>=', (int) $request->input('min_amount'));
        }

        if ($request->filled('max_amount')) {
            $query->where('orders.total', '<=', (int) $request->input('max_amount'));
        }

        if ($request->filled('gateway')) {
            $gateway = $request->input('gateway');
            $query->whereHas('paymentTransactions', fn ($q) => $q->where('gateway', $gateway));
        }

        if ($request->filled('payment_status')) {
            $paymentStatus = $request->input('payment_status');
            $query->whereHas('paymentTransactions', fn ($q) => $q->where('status', $paymentStatus));
        }

        switch ($request->input('sort', 'newest')) {
            case 'oldest':
                $query->orderBy('orders.created_at', 'asc');
                break;
            case 'amount_asc':
                $query->orderBy('orders.total', 'asc');
                break;
            case 'amount_desc':
                $query->orderBy('orders.total', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('orders.created_at', 'desc');
                break;
        }

        return $query;
    }

    /**
     * Thống kê tổng quan số liệu tài chính.
     */
    public function getFinanceSummary(Request $request): array
    {
        $summaryQuery = $this->getOrdersQuery($request);

        $totalOrders = (clone $summaryQuery)->count();
        $totalAmount = (clone $summaryQuery)->sum('orders.total');

        $paymentStatuses = ['pending', 'paid', 'failed', 'refund_pending', 'refunded'];
        $statusTotals = [];

        foreach ($paymentStatuses as $status) {
            $statusTotals[$status] = PaymentTransaction::query()
                ->where('status', $status)
                ->whereHas('order', function ($q) use ($request) {
                    $this->applyOrderFilters($q, $request);
                })
                ->count();
        }

        $methodTotals = [
            'cod' => PaymentTransaction::where('gateway', 'cod')
                ->whereHas('order', function ($q) use ($request) {
                    $this->applyOrderFilters($q, $request);
                })
                ->count(),

            'momo' => PaymentTransaction::where('gateway', 'momo')
                ->whereHas('order', function ($q) use ($request) {
                    $this->applyOrderFilters($q, $request);
                })
                ->count(),
        ];

        return [
            'totalOrders' => $totalOrders,
            'totalAmount' => $totalAmount,
            'statusTotals' => $statusTotals,
            'methodTotals' => $methodTotals,
        ];
    }

    /**
     * Áp dụng các bộ lọc dùng chung cho thống kê.
     */
    public function applyOrderFilters($query, Request $request): void
    {
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('orders.id', $search)
                    ->orWhere('orders.order_code', 'like', "%{$search}%")
                    ->orWhere('orders.customer_name', 'like', "%{$search}%")
                    ->orWhere('orders.phone', 'like', "%{$search}%")
                    ->orWhere('orders.ghn_order_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('orders.created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('orders.created_at', '<=', $request->input('date_to'));
        }

        if ($request->filled('min_amount')) {
            $query->where('orders.total', '>=', (int) $request->input('min_amount'));
        }

        if ($request->filled('max_amount')) {
            $query->where('orders.total', '<=', (int) $request->input('max_amount'));
        }
    }

    /**
     * Query danh sách giao dịch tài chính.
     */
    public function getTransactionsQuery(Request $request)
    {
        $query = PaymentTransaction::query()->with(['order', 'order.user']);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('payment_transactions.id', $search)
                    ->orWhere('payment_transactions.gateway_order_id', 'like', "%{$search}%")
                    ->orWhereHas('order', function ($orderQuery) use ($search) {
                        $orderQuery->where('order_code', 'like', "%{$search}%")
                            ->orWhere('customer_name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('gateway')) {
            $query->where('payment_transactions.gateway', $request->input('gateway'));
        }

        if ($request->filled('status')) {
            $query->where('payment_transactions.status', $request->input('status'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_transactions.created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_transactions.created_at', '<=', $request->input('date_to'));
        }

        return $query->orderByDesc('payment_transactions.created_at');
    }
}
