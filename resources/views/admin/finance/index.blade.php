@extends('layouts.admin')

@section('title', 'Tài chính')

@section('page_title', 'Tài chính')

@section('content')

<style>
    /* =========================================================
       FINANCE DASHBOARD
    ========================================================= */

    .finance-page {
        max-width: 1500px;
        margin: 0 auto;
    }

    .finance-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        padding: 28px 30px;
        margin-bottom: 24px;
        border-radius: 18px;
        background: linear-gradient(135deg, #111827 0%, #1f2937 100%);
        color: #fff;
        box-shadow: 0 10px 30px rgba(15, 23, 42, .10);
    }

    .finance-hero h2 {
        margin: 0 0 8px;
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -.4px;
    }

    .finance-hero p {
        margin: 0;
        color: #cbd5e1;
        font-size: 14px;
    }

    .finance-hero-actions {
        display: flex;
        gap: 10px;
        flex-shrink: 0;
    }

    .finance-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 0 17px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #111827;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
        transition: .2s ease;
    }

    .finance-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, .12);
    }

    .finance-btn-dark {
        background: #111827;
        border-color: #111827;
        color: #fff;
    }

    .finance-btn-dark:hover {
        background: #000;
        color: #fff;
    }

    /* SUMMARY */

    .finance-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .finance-summary-card {
        position: relative;
        overflow: hidden;
        min-height: 140px;
        padding: 22px;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 5px 20px rgba(15, 23, 42, .05);
    }

    .finance-summary-card::after {
        content: "";
        position: absolute;
        right: -28px;
        bottom: -35px;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: rgba(15, 23, 42, .035);
    }

    .finance-summary-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .finance-summary-label {
        color: #64748b;
        font-size: 13px;
        font-weight: 600;
    }

    .finance-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: #f1f5f9;
        font-size: 19px;
    }

    .finance-summary-value {
        display: block;
        margin-top: 15px;
        color: #0f172a;
        font-size: 27px;
        font-weight: 800;
        letter-spacing: -.5px;
    }

    .finance-summary-note {
        display: block;
        margin-top: 4px;
        color: #94a3b8;
        font-size: 12px;
    }

    .finance-card-money .finance-icon {
        background: #ecfdf5;
    }

    .finance-card-money .finance-summary-value {
        color: #047857;
    }

    .finance-card-cod .finance-icon {
        background: #fff7ed;
    }

    .finance-card-momo .finance-icon {
        background: #fdf2f8;
    }

    /* GENERAL CARD */

    .finance-card {
        margin-bottom: 24px;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 5px 20px rgba(15, 23, 42, .045);
    }

    .finance-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 20px 22px;
        border-bottom: 1px solid #eef2f7;
    }

    .finance-card-header h3 {
        margin: 0;
        color: #111827;
        font-size: 16px;
        font-weight: 800;
    }

    .finance-card-header p {
        margin: 4px 0 0;
        color: #94a3b8;
        font-size: 12px;
    }

    /* FILTER */

    .finance-filter {
        padding: 22px;
    }

    .finance-filter-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 17px;
    }

    .finance-field {
        min-width: 0;
    }

    .finance-field label {
        display: block;
        margin-bottom: 7px;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
    }

    .finance-field input,
    .finance-field select {
        width: 100%;
        height: 43px;
        box-sizing: border-box;
        padding: 0 12px;
        border: 1px solid #dbe2ea;
        border-radius: 9px;
        outline: none;
        background: #fff;
        color: #1e293b;
        font-size: 13px;
        transition: .2s ease;
    }

    .finance-field input:focus,
    .finance-field select:focus {
        border-color: #64748b;
        box-shadow: 0 0 0 3px rgba(100, 116, 139, .10);
    }

    .finance-filter-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 20px;
        padding-top: 18px;
        border-top: 1px solid #eef2f7;
    }

    .finance-reset {
        background: #fff;
        color: #475569;
    }

    /* STATUS */

    .finance-status-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 12px;
        padding: 20px 22px 22px;
    }

    .finance-status-card {
        padding: 16px;
        border: 1px solid #edf1f5;
        border-radius: 12px;
        background: #f8fafc;
    }

    .finance-status-card span {
        display: block;
        color: #64748b;
        font-size: 12px;
        font-weight: 600;
    }

    .finance-status-card strong {
        display: block;
        margin-top: 8px;
        color: #0f172a;
        font-size: 23px;
    }

    .finance-status-pending {
        border-left: 4px solid #f59e0b;
    }

    .finance-status-paid {
        border-left: 4px solid #10b981;
    }

    .finance-status-failed {
        border-left: 4px solid #ef4444;
    }

    .finance-status-refund {
        border-left: 4px solid #8b5cf6;
    }

    .finance-status-refunded {
        border-left: 4px solid #64748b;
    }

    /* TABLE */

    .finance-table-wrap {
        overflow-x: auto;
    }

    .finance-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 950px;
    }

    .finance-table th {
        padding: 13px 18px;
        border-bottom: 1px solid #e5e7eb;
        background: #f8fafc;
        color: #64748b;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .4px;
        text-align: left;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .finance-table td {
        padding: 16px 18px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        font-size: 13px;
        vertical-align: middle;
    }

    .finance-table tbody tr {
        transition: .15s ease;
    }

    .finance-table tbody tr:hover {
        background: #f8fafc;
    }

    .finance-order-code {
        color: #111827;
        font-weight: 800;
    }

    .finance-order-id {
        display: block;
        margin-top: 3px;
        color: #94a3b8;
        font-size: 11px;
    }

    .finance-customer-name {
        display: block;
        color: #1e293b;
        font-weight: 700;
    }

    .finance-phone {
        display: block;
        margin-top: 3px;
        color: #94a3b8;
        font-size: 11px;
    }

    .finance-amount {
        color: #111827;
        font-size: 14px;
        font-weight: 800;
        white-space: nowrap;
    }

    .finance-date {
        color: #64748b;
        font-size: 12px;
        white-space: nowrap;
    }

    /* BADGES */

    .finance-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        min-height: 27px;
        padding: 0 9px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }

    .finance-badge-cod {
        background: #fff7ed;
        color: #c2410c;
    }

    .finance-badge-momo {
        background: #fdf2f8;
        color: #be185d;
    }

    .finance-badge-paid {
        background: #ecfdf5;
        color: #047857;
    }

    .finance-badge-pending {
        background: #fffbeb;
        color: #b45309;
    }

    .finance-badge-failed {
        background: #fef2f2;
        color: #b91c1c;
    }

    .finance-badge-refund {
        background: #f5f3ff;
        color: #6d28d9;
    }

    .finance-badge-refunded {
        background: #f1f5f9;
        color: #475569;
    }

    .finance-badge-order {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .finance-badge-shipping {
        background: #f8fafc;
        color: #64748b;
        margin-top: 5px;
    }

    .finance-empty {
        padding: 55px 20px !important;
        text-align: center;
        color: #94a3b8 !important;
    }

    .finance-empty-icon {
        margin-bottom: 10px;
        font-size: 30px;
    }

    .finance-view-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 60px;
        height: 34px;
        padding: 0 11px;
        border: 1px solid #dbe2ea;
        border-radius: 8px;
        background: #fff;
        color: #334155;
        text-decoration: none;
        font-size: 12px;
        font-weight: 700;
        transition: .2s ease;
    }

    .finance-view-btn:hover {
        border-color: #111827;
        background: #111827;
        color: #fff;
    }

    /* PAGINATION */

    .finance-pagination {
        padding: 18px 22px;
        border-top: 1px solid #eef2f7;
    }

    /* RESPONSIVE */

    @media (max-width: 1200px) {
        .finance-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .finance-filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .finance-status-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .finance-hero {
            flex-direction: column;
            align-items: flex-start;
        }

        .finance-summary-grid,
        .finance-filter-grid,
        .finance-status-grid {
            grid-template-columns: 1fr;
        }

        .finance-hero-actions {
            width: 100%;
        }

        .finance-hero-actions .finance-btn {
            flex: 1;
        }
    }
</style>

<div class="finance-page">

    {{-- =====================================================
         HERO
    ====================================================== --}}
    <div class="finance-hero">

        <div>
            <h2>💰 Tổng quan tài chính</h2>

            <p>
                Theo dõi doanh thu, đơn hàng và tình trạng thanh toán
                của cửa hàng.
            </p>
        </div>

        <div class="finance-hero-actions">

            <a href="{{ route('admin.finance.transactions') }}"
               class="finance-btn">
                📋 Xem giao dịch
            </a>

        </div>

    </div>


    {{-- =====================================================
         SUMMARY
    ====================================================== --}}
    <div class="finance-summary-grid">

        <div class="finance-summary-card">

            <div class="finance-summary-top">

                <span class="finance-summary-label">
                    Tổng đơn hàng
                </span>

                <div class="finance-icon">
                    🧾
                </div>

            </div>

            <strong class="finance-summary-value">
                {{ number_format($totalOrders) }}
            </strong>

            <span class="finance-summary-note">
                Đơn hàng trong kết quả lọc
            </span>

        </div>


        <div class="finance-summary-card finance-card-money">

            <div class="finance-summary-top">

                <span class="finance-summary-label">
                    Tổng giá trị
                </span>

                <div class="finance-icon">
                    💰
                </div>

            </div>

            <strong class="finance-summary-value">
                {{ number_format($totalAmount, 0, ',', '.') }} ₫
            </strong>

            <span class="finance-summary-note">
                Tổng giá trị đơn hàng
            </span>

        </div>


        <div class="finance-summary-card finance-card-cod">

            <div class="finance-summary-top">

                <span class="finance-summary-label">
                    Thanh toán COD
                </span>

                <div class="finance-icon">
                    💵
                </div>

            </div>

            <strong class="finance-summary-value">
                {{ number_format($methodTotals['cod'] ?? 0) }}
            </strong>

            <span class="finance-summary-note">
                Giao dịch COD
            </span>

        </div>


        <div class="finance-summary-card finance-card-momo">

            <div class="finance-summary-top">

                <span class="finance-summary-label">
                    Thanh toán MoMo
                </span>

                <div class="finance-icon">
                    💳
                </div>

            </div>

            <strong class="finance-summary-value">
                {{ number_format($methodTotals['momo'] ?? 0) }}
            </strong>

            <span class="finance-summary-note">
                Giao dịch MoMo
            </span>

        </div>

    </div>


    {{-- =====================================================
         FILTER
    ====================================================== --}}
    <div class="finance-card">

        <div class="finance-card-header">

            <div>
                <h3>🔎 Bộ lọc dữ liệu</h3>

                <p>
                    Tìm kiếm và lọc các đơn hàng theo thông tin thanh toán.
                </p>
            </div>

        </div>


        <form method="GET"
              action="{{ route('admin.finance.index') }}"
              class="finance-filter">

            <div class="finance-filter-grid">

                {{-- Search --}}
                <div class="finance-field">

                    <label for="search">
                        Tìm kiếm
                    </label>

                    <input
                        type="text"
                        id="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Mã đơn, tên khách hàng, SĐT..."
                    >

                </div>


                {{-- Date from --}}
                <div class="finance-field">

                    <label for="date_from">
                        Từ ngày
                    </label>

                    <input
                        type="date"
                        id="date_from"
                        name="date_from"
                        value="{{ request('date_from') }}"
                    >

                </div>


                {{-- Date to --}}
                <div class="finance-field">

                    <label for="date_to">
                        Đến ngày
                    </label>

                    <input
                        type="date"
                        id="date_to"
                        name="date_to"
                        value="{{ request('date_to') }}"
                    >

                </div>


                {{-- Min amount --}}
                <div class="finance-field">

                    <label for="min_amount">
                        Giá trị từ
                    </label>

                    <input
                        type="number"
                        id="min_amount"
                        name="min_amount"
                        min="0"
                        value="{{ request('min_amount') }}"
                        placeholder="0"
                    >

                </div>


                {{-- Max amount --}}
                <div class="finance-field">

                    <label for="max_amount">
                        Giá trị đến
                    </label>

                    <input
                        type="number"
                        id="max_amount"
                        name="max_amount"
                        min="0"
                        value="{{ request('max_amount') }}"
                        placeholder="Không giới hạn"
                    >

                </div>


                {{-- Gateway --}}
                <div class="finance-field">

                    <label for="gateway">
                        Phương thức thanh toán
                    </label>

                    <select id="gateway"
                            name="gateway">

                        <option value="">
                            Tất cả
                        </option>

                        <option value="cod"
                            @selected(request('gateway') === 'cod')>
                            COD
                        </option>

                        <option value="momo"
                            @selected(request('gateway') === 'momo')>
                            MoMo
                        </option>

                    </select>

                </div>


                {{-- Payment status --}}
                <div class="finance-field">

                    <label for="payment_status">
                        Trạng thái thanh toán
                    </label>

                    <select id="payment_status"
                            name="payment_status">

                        <option value="">
                            Tất cả
                        </option>

                        <option value="pending"
                            @selected(request('payment_status') === 'pending')>
                            Chờ thanh toán
                        </option>

                        <option value="paid"
                            @selected(request('payment_status') === 'paid')>
                            Đã thanh toán
                        </option>

                        <option value="failed"
                            @selected(request('payment_status') === 'failed')>
                            Thất bại
                        </option>

                        <option value="refund_pending"
                            @selected(request('payment_status') === 'refund_pending')>
                            Chờ hoàn tiền
                        </option>

                        <option value="refunded"
                            @selected(request('payment_status') === 'refunded')>
                            Đã hoàn tiền
                        </option>

                    </select>

                </div>


                {{-- Sort --}}
                <div class="finance-field">

                    <label for="sort">
                        Sắp xếp
                    </label>

                    <select id="sort"
                            name="sort">

                        <option value="newest"
                            @selected(request('sort', 'newest') === 'newest')>
                            Mới nhất
                        </option>

                        <option value="oldest"
                            @selected(request('sort') === 'oldest')>
                            Cũ nhất
                        </option>

                        <option value="amount_desc"
                            @selected(request('sort') === 'amount_desc')>
                            Giá trị cao → thấp
                        </option>

                        <option value="amount_asc"
                            @selected(request('sort') === 'amount_asc')>
                            Giá trị thấp → cao
                        </option>

                    </select>

                </div>

            </div>


            <div class="finance-filter-actions">

                <button type="submit"
                        class="finance-btn finance-btn-dark">
                    🔍 Lọc dữ liệu
                </button>

                <a href="{{ route('admin.finance.index') }}"
                   class="finance-btn finance-reset">
                    ↻ Xóa bộ lọc
                </a>

            </div>

        </form>

    </div>


    {{-- =====================================================
         PAYMENT STATUS
    ====================================================== --}}
    <div class="finance-card">

        <div class="finance-card-header">

            <div>
                <h3>📊 Trạng thái thanh toán</h3>

                <p>
                    Phân bổ giao dịch theo trạng thái hiện tại.
                </p>
            </div>

        </div>


        <div class="finance-status-grid">

            <div class="finance-status-card finance-status-pending">

                <span>
                    Chờ thanh toán
                </span>

                <strong>
                    {{ number_format($statusTotals['pending'] ?? 0) }}
                </strong>

            </div>


            <div class="finance-status-card finance-status-paid">

                <span>
                    Đã thanh toán
                </span>

                <strong>
                    {{ number_format($statusTotals['paid'] ?? 0) }}
                </strong>

            </div>


            <div class="finance-status-card finance-status-failed">

                <span>
                    Thanh toán thất bại
                </span>

                <strong>
                    {{ number_format($statusTotals['failed'] ?? 0) }}
                </strong>

            </div>


            <div class="finance-status-card finance-status-refund">

                <span>
                    Chờ hoàn tiền
                </span>

                <strong>
                    {{ number_format($statusTotals['refund_pending'] ?? 0) }}
                </strong>

            </div>


            <div class="finance-status-card finance-status-refunded">

                <span>
                    Đã hoàn tiền
                </span>

                <strong>
                    {{ number_format($statusTotals['refunded'] ?? 0) }}
                </strong>

            </div>

        </div>

    </div>


    {{-- =====================================================
         ORDER LIST
    ====================================================== --}}
    <div class="finance-card">

        <div class="finance-card-header">

            <div>

                <h3>🧾 Đơn hàng tài chính</h3>

                <p>
                    Hiển thị {{ $orders->total() }} đơn hàng
                    theo điều kiện lọc.
                </p>

            </div>

        </div>


        <div class="finance-table-wrap">

            <table class="finance-table">

                <thead>

                    <tr>

                        <th>
                            Đơn hàng
                        </th>

                        <th>
                            Khách hàng
                        </th>

                        <th>
                            Thanh toán
                        </th>

                        <th>
                            Số tiền
                        </th>

                        <th>
                            Trạng thái
                        </th>

                        <th>
                            Ngày tạo
                        </th>

                        <th>
                            Thao tác
                        </th>

                    </tr>

                </thead>


                <tbody>

                @forelse($orders as $order)

                    @php

                        $payment = $order->paymentTransactions
                            ->sortByDesc(function ($transaction) {

                                return match ($transaction->status) {

                                    'paid' => 5,

                                    'refund_pending' => 4,

                                    'refunded' => 3,

                                    'pending' => 2,

                                    default => 1,

                                };

                            })
                            ->sortByDesc('id')
                            ->first();

                        $paymentStatus = $payment?->status;

                    @endphp


                    <tr>

                        {{-- ORDER --}}
                        <td>

                            <span class="finance-order-code">
                                {{ $order->order_code }}
                            </span>

                            <span class="finance-order-id">
                                #{{ $order->id }}
                            </span>

                        </td>


                        {{-- CUSTOMER --}}
                        <td>

                            <span class="finance-customer-name">
                                {{ $order->customer_name }}
                            </span>

                            <span class="finance-phone">
                                {{ $order->phone }}
                            </span>

                        </td>


                        {{-- PAYMENT --}}
                        <td>

                            @if($payment)

                                @if($payment->gateway === 'momo')

                                    <span class="finance-badge finance-badge-momo">
                                        ● MoMo
                                    </span>

                                @elseif($payment->gateway === 'cod')

                                    <span class="finance-badge finance-badge-cod">
                                        ● COD
                                    </span>

                                @else

                                    <span class="finance-badge">
                                        {{ strtoupper($payment->gateway) }}
                                    </span>

                                @endif


                                <div style="margin-top:7px;">

                                    @if($paymentStatus === 'paid')

                                        <span class="finance-badge finance-badge-paid">
                                            ✓ Đã thanh toán
                                        </span>

                                    @elseif($paymentStatus === 'pending' || $paymentStatus === 'initiated')

                                        <span class="finance-badge finance-badge-pending">
                                            ◷ Chờ thanh toán
                                        </span>

                                    @elseif($paymentStatus === 'failed')

                                        <span class="finance-badge finance-badge-failed">
                                            ! Thất bại
                                        </span>

                                    @elseif($paymentStatus === 'refund_pending')

                                        <span class="finance-badge finance-badge-refund">
                                            ↻ Chờ hoàn tiền
                                        </span>

                                    @elseif($paymentStatus === 'refunded')

                                        <span class="finance-badge finance-badge-refunded">
                                            ✓ Đã hoàn tiền
                                        </span>

                                    @else

                                        <span class="finance-badge">
                                            {{ $payment->statusLabel() }}
                                        </span>

                                    @endif

                                </div>

                            @else

                                <span style="color:#94a3b8;">
                                    Chưa có giao dịch
                                </span>

                            @endif

                        </td>


                        {{-- AMOUNT --}}
                        <td>

                            <span class="finance-amount">
                                {{ number_format($order->total, 0, ',', '.') }} ₫
                            </span>

                        </td>


                        {{-- STATUS --}}
                        <td>

                            @php
                                $orderStatus = $order->status;
                            @endphp


                            @if($orderStatus === 'paid')

                                <span class="finance-badge finance-badge-paid">
                                    ✓ Đã thanh toán
                                </span>

                            @elseif($orderStatus === 'cancelled')

                                <span class="finance-badge finance-badge-failed">
                                    ✕ Đã hủy
                                </span>

                            @elseif($orderStatus === 'completed')

                                <span class="finance-badge finance-badge-paid">
                                    ✓ Hoàn thành
                                </span>

                            @elseif($orderStatus === 'shipping')

                                <span class="finance-badge finance-badge-shipping">
                                    🚚 Đang giao
                                </span>

                            @elseif($orderStatus === 'processing')

                                <span class="finance-badge finance-badge-order">
                                    ⚙ Đang xử lý
                                </span>

                            @elseif($orderStatus === 'confirmed')

                                <span class="finance-badge finance-badge-order">
                                    ✓ Đã xác nhận
                                </span>

                            @else

                                <span class="finance-badge finance-badge-pending">
                                    ◷ Chờ xác nhận
                                </span>

                            @endif


                            @if($order->ghn_status)

                                <div style="margin-top:7px;">

                                    <span class="finance-badge finance-badge-shipping">
                                        {{ $order->ghnStatusLabel() }}
                                    </span>

                                </div>

                            @endif

                        </td>


                        {{-- CREATED --}}
                        <td>

                            <span class="finance-date">
                                {{ $order->created_at?->format('d/m/Y') }}
                            </span>

                            <br>

                            <span class="finance-date">
                                {{ $order->created_at?->format('H:i') }}
                            </span>

                        </td>


                        {{-- ACTION --}}
                        <td>

                            <a href="{{ route('admin.orders.show', $order) }}"
                               class="finance-view-btn">
                                Xem →
                            </a>

                        </td>

                    </tr>


                @empty

                    <tr>

                        <td colspan="7"
                            class="finance-empty">

                            <div class="finance-empty-icon">
                                📭
                            </div>

                            Không có dữ liệu đơn hàng.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        @if($orders->hasPages())

            <div class="finance-pagination">

                {{ $orders->withQueryString()->links() }}

            </div>

        @endif

    </div>

</div>

@endsection