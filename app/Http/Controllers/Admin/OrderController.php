<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\GHNOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('user')
            ->when($request->query('q'), function ($query, $search) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('order_code', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($request->query('status'), fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('user', 'items.variant.product.images', 'items.variant.size', 'items.variant.color');

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['pending', 'confirmed', 'processing', 'shipping', 'completed', 'cancelled'])],
        ]);

        $next = $data['status'];
        $allowed = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['processing', 'cancelled'],
            'processing' => ['shipping', 'cancelled'],
            'shipping' => ['completed'],
            'completed' => [],
            'cancelled' => [],
        ];

        if ($next !== $order->status && ! in_array($next, $allowed[$order->status] ?? [], true)) {
            return back()->with('error', 'Không thể chuyển trạng thái đơn hàng theo hướng này.');
        }

        DB::transaction(function () use ($order, $next) {
            if ($next === 'cancelled' && $order->status !== 'cancelled') {
                foreach ($order->items()->with('variant')->get() as $item) {
                    $item->variant?->increment('stock', $item->quantity);
                }
            }

            $order->update(['status' => $next]);
        });

        return back()->with('success', 'Đã cập nhật trạng thái đơn hàng.');
    }

    public function createGhnOrder(Order $order, GHNOrderService $ghnOrderService)
    {
        if ($order->ghn_order_code) {
            return back()->with('error', 'Đơn hàng này đã có mã GHN.');
        }

        try {
            $data = $ghnOrderService->createForOrder($order);

            $order->update([
                'ghn_order_code' => $data['order_code'] ?? null,
                'ghn_fee' => $data['total_fee'] ?? $order->shipping_fee,
                'ghn_status' => 'created',
            ]);

            return back()->with('success', 'Đã tạo đơn GHN.');
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('error', 'Không thể tạo đơn GHN: '.$exception->getMessage());
        }
    }

    public function cancelGhnOrder(Order $order, GHNOrderService $ghnOrderService)
    {
        if (! $order->ghn_order_code) {
            return back()->with('error', 'Đơn hàng chưa có mã GHN.');
        }

        try {
            $ghnOrderService->cancel($order);

            $order->update(['ghn_status' => 'cancelled']);

            return back()->with('success', 'Đã gửi yêu cầu hủy đơn GHN.');
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('error', 'Không thể hủy đơn GHN: '.$exception->getMessage());
        }
    }
}
