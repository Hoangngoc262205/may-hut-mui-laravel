@extends('layouts.admin')

@section('title', 'Giao dịch tài chính')

@section('page_title', 'Giao dịch tài chính')

@section('content')

<style>
    /* =========================================================
       FINANCE TRANSACTIONS
    ========================================================= */

    .finance-transactions {
        max-width: 1500px;
        margin: 0 auto;
    }

    /* HERO */
    .finance-tx-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        padding: 28px 30px;
        margin-bottom: 22px;
        border-radius: 18px;
        background: linear-gradient(135deg, #111827 0%, #273449 100%);
        color: #fff;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .10);
    }

    .finance-tx-hero-left {
        display: flex;
        align-items: center;
        gap: 17px;
    }

    .finance-tx-icon {
        width: 52px;
        height: 52px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 14px;
        background: rgba(255,255,255,.12);
        font-size: 25px;
    }

    .finance-tx-hero h2 {
        margin: 0 0 6px;
        color: #fff;
        font-size: 24px;
        font-weight: 800;
        letter-spacing: -.4px;
    }

    .finance-tx-hero p {
        margin: 0;
        color: #cbd5e1;
        font-size: 13px;
    }

    .finance-tx-back {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 41px;
        padding: 0 16px;
        border: 1px solid rgba(255,255,255,.20);
        border-radius: 10px;
        background: rgba(255,255,255,.08);
        color: #fff;
        text-decoration: none;
        font-size: 12px;
        font-weight: 700;
        transition: .2s ease;
    }

    .finance-tx-back:hover {
        background: rgba(255,255,255,.16);
        color: #fff;
        transform: translateY(-1px);
    }

    /* SUMMARY */
    .finance-tx-summary {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 22px;
    }

    .finance-tx-summary-card {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 19px 20px;
        border: 1px solid #e5e7eb;
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 5px 18px rgba(15, 23, 42, .045);
    }

    .finance-tx-summary-icon {
        width: 43px;
        height: 43px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 11px;
        background: #f1f5f9;
        font-size: 19px;
    }

    .finance-tx-summary-label {
        display: block;
        margin-bottom: 4px;
        color: #94a3b8;
        font-size: 11px;
        font-weight: 700;
    }

    .finance-tx-summary-value {
        display: block;
        color: #111827;
        font-size: 18px;
        font-weight: 800;
    }

    /* CARD */
    .finance-tx-card {
        margin-bottom: 22px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 5px 20px rgba(15, 23, 42, .045);
    }

    .finance-tx-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 19px 22px;
        border-bottom: 1px solid #eef2f7;
    }

    .finance-tx-card-title {
        margin: 0;
        color: #111827;
        font-size: 16px;
        font-weight: 800;
    }

    .finance-tx-card-subtitle {
        margin: 5px 0 0;
        color: #94a3b8;
        font-size: 12px;
    }

    /* FILTER */
    .finance-tx-filter {
        padding: 22px;
    }

    .finance-tx-filter-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
        gap: 15px;
    }

    .finance-tx-field label {
        display: block;
        margin-bottom: 7px;
        color: #475569;
        font-size: 11px;
        font-weight: 800;
    }

    .finance-tx-field input,
    .finance-tx-field select {
        width: 100%;
        height: 42px;
        box-sizing: border-box;
        padding: 0 11px;
        border: 1px solid #dbe2ea;
        border-radius: 9px;
        outline: none;
        background: #fff;
        color: #334155;
        font-size: 12px;
        transition: .2s ease;
    }

    .finance-tx-field input:focus,
    .finance-tx-field select:focus {
        border-color: #64748b;
        box-shadow: 0 0 0 3px rgba(100,116,139,.10);
    }

    .finance-tx-filter-actions {
        display: flex;
        gap: 9px;
        margin-top: 18px;
        padding-top: 17px;
        border-top: 1px solid #eef2f7;
    }

    .finance-tx-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 39px;
        padding: 0 14px;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
        transition: .2s ease;
    }

    .finance-tx-btn-dark {
        border: 1px solid #111827;
        background: #111827;
        color: #fff;
    }

    .finance-tx-btn-dark:hover {
        background: #000;
        color: #fff;
        transform: translateY(-1px);
    }

    .finance-tx-btn-light {
        border: 1px solid #dbe2ea;
        background: #fff;
        color: #475569;
    }

    .finance-tx-btn-light:hover {
        border-color: #94a3b8;
        color: #111827;
    }

    /* TABLE */
    .finance-tx-table-wrap {
        overflow-x: auto;
    }

    .finance-tx-table {
        width: 100%;
        min-width: 1180px;
        border-collapse: collapse;
    }

    .finance-tx-table th {
        padding: 13px 17px;
        border-bottom: 1px solid #e5e7eb;
        background: #f8fafc;
        color: #64748b;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .45px;
        text-align: left;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .finance-tx-table td {
        padding: 15px 17px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        font-size: 12px;
        vertical-align: middle;
    }

    .finance-tx-table tbody tr {
        transition: .15s ease;
    }

    .finance-tx-table tbody tr:hover {
        background: #f8fafc;
    }

    /* ID */
    .finance-tx-id {
        color: #64748b;
        font-weight: 700;
        white-space: nowrap;
    }

    /* TRANSACTION */
    .finance-tx-code {
        display: block;
        max-width: 210px;
        overflow: hidden;
        color: #111827;
        font-weight: 800;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .finance-tx-sub {
        display: block;
        max-width: 210px;
        margin-top: 4px;
        overflow: hidden;
        color: #94a3b8;
        font-size: 10px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* ORDER */
    .finance-tx-order {
        display: block;
        color: #111827;
        font-weight: 800;
    }

    .finance-tx-order-id {
        display: block;
        margin-top: 3px;
        color: #94a3b8;
        font-size: 10px;
    }

    /* CUSTOMER */
    .finance-tx-customer {
        display: block;
        color: #334155;
        font-weight: 700;
    }

    .finance-tx-phone {
        display: block;
        margin-top: 3px;
        color: #94a3b8;
        font-size: 10px;
    }

    /* GATEWAY */
    .finance-tx-gateway {
        display: inline-flex;
        align-items: center;
        min-height: 27px;
        padding: 0 10px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
    }

    .finance-tx-gateway-cod {
        background: #fff7ed;
        color: #c2410c;
    }

    .finance-tx-gateway-momo {
        background: #fdf2f8;
        color: #be185d;
    }

    .finance-tx-gateway-other {
        background: #f1f5f9;
        color: #475569;
    }

    /* AMOUNT */
    .finance-tx-amount {
        color: #111827;
        font-size: 13px;
        font-weight: 800;
        white-space: nowrap;
    }

    /* STATUS */
    .finance-tx-status {
        display: inline-flex;
        align-items: center;
        min-height: 27px;
        padding: 0 10px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
    }

    .finance-tx-status-paid {
        background: #ecfdf5;
        color: #047857;
    }

    .finance-tx-status-failed {
        background: #fef2f2;
        color: #b91c1c;
    }

    .finance-tx-status-warning {
        background: #fffbeb;
        color: #b45309;
    }

    .finance-tx-status-info {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .finance-tx-status-purple {
        background: #f5f3ff;
        color: #6d28d9;
    }

    .finance-tx-status-neutral {
        background: #f1f5f9;
        color: #64748b;
    }

    .finance-tx-message {
        display: block;
        max-width: 170px;
        margin-top: 5px;
        color: #94a3b8;
        font-size: 10px;
        line-height: 1.4;
    }

    /* DATE */
    .finance-tx-date {
        color: #475569;
        font-size: 11px;
        white-space: nowrap;
    }

    .finance-tx-paid-date {
        display: block;
        margin-top: 4px;
        color: #94a3b8;
        font-size: 10px;
    }

    /* ACTION */
    .finance-tx-view {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 34px;
        padding: 0 11px;
        border: 1px solid #dbe2ea;
        border-radius: 8px;
        background: #fff;
        color: #334155;
        text-decoration: none;
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
        transition: .2s ease;
    }

    .finance-tx-view:hover {
        border-color: #111827;
        background: #111827;
        color: #fff;
    }

    /* EMPTY */
    .finance-tx-empty {
        padding: 65px 20px !important;
        text-align: center;
    }

    .finance-tx-empty-icon {
        margin-bottom: 9px;
        font-size: 34px;
    }

    .finance-tx-empty-title {
        margin-bottom: 4px;
        color: #475569;
        font-size: 14px;
        font-weight: 800;
    }

    .finance-tx-empty-text {
        color: #94a3b8;
        font-size: 12px;
    }

    /* PAGINATION */
    .finance-tx-pagination {
        padding: 18px 22px;
        border-top: 1px solid #eef2f7;
    }

    /* RESPONSIVE */
    @media (max-width: 1200px) {
        .finance-tx-filter-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .finance-tx-summary {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .finance-tx-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .finance-tx-filter-grid {
            grid-template-columns: 1fr;
        }

        .finance-tx-filter-actions {
            flex-direction: column;
        }

        .finance-tx-btn {
            width: 100%;
        }
    }
</style>


<div class="finance-transactions">

    {{-- =====================================================
         HERO
    ====================================================== --}}
    <div class="finance-tx-hero">

        <div class="finance-tx-hero-left">

            <div class="finance-tx-icon">
                💳
            </div>

            <div>

                <h2>Giao dịch tài chính</h2>

                <p>
                    Theo dõi toàn bộ giao dịch thanh toán của hệ thống.
                </p>

            </div>

        </div>


        <a href="{{ route('admin.finance.index') }}"
           class="finance-tx-back">
            ← Tổng quan tài chính
        </a>

    </div>


    {{-- =====================================================
         SUMMARY
    ====================================================== --}}
    <div class="finance-tx-summary">

        <div class="finance-tx-summary-card">

            <div class="finance-tx-summary-icon">
                📋
            </div>

            <div>

                <span class="finance-tx-summary-label">
                    Tổng giao dịch
                </span>

                <span class="finance-tx-summary-value">
                    {{ number_format($transactions->total()) }}
                </span>

            </div>

        </div>


        <div class="finance-tx-summary-card">

            <div class="finance-tx-summary-icon">
                💰
            </div>

            <div>

                <span class="finance-tx-summary-label">
                    Đang hiển thị
                </span>

                <span class="finance-tx-summary-value">
                    {{ $transactions->count() }}
                </span>

            </div>

        </div>


        <div class="finance-tx-summary-card">

            <div class="finance-tx-summary-icon">
                🔎
            </div>

            <div>

                <span class="finance-tx-summary-label">
                    Bộ lọc hiện tại
                </span>

                <span class="finance-tx-summary-value">
                    {{ collect(request()->only([
                        'search',
                        'gateway',
                        'status',
                        'date_from',
                        'date_to'
                    ]))->filter()->count() }}
                </span>

            </div>

        </div>

    </div>


    {{-- =====================================================
         FILTER
    ====================================================== --}}
    <div class="finance-tx-card">

        <div class="finance-tx-card-header">

            <div>

                <h3 class="finance-tx-card-title">
                    🔎 Bộ lọc giao dịch
                </h3>

                <p class="finance-tx-card-subtitle">
                    Tìm kiếm nhanh theo mã đơn, khách hàng,
                    phương thức hoặc trạng thái.
                </p>

            </div>

        </div>


        <form method="GET"
              action="{{ route('admin.finance.transactions') }}"
              class="finance-tx-filter">

            <div class="finance-tx-filter-grid">

                {{-- Search --}}
                <div class="finance-tx-field">

                    <label for="search">
                        TÌM KIẾM
                    </label>

                    <input
                        type="text"
                        id="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Mã giao dịch, mã đơn, tên, SĐT..."
                    >

                </div>


                {{-- Gateway --}}
                <div class="finance-tx-field">

                    <label for="gateway">
                        PHƯƠNG THỨC
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


                {{-- Status --}}
                <div class="finance-tx-field">

                    <label for="status">
                        TRẠNG THÁI
                    </label>

                    <select id="status"
                            name="status">

                        <option value="">
                            Tất cả
                        </option>

                        <option value="pending"
                            @selected(request('status') === 'pending')>
                            Chờ thanh toán
                        </option>

                        <option value="initiated"
                            @selected(request('status') === 'initiated')>
                            Đang chờ thanh toán
                        </option>

                        <option value="paid"
                            @selected(request('status') === 'paid')>
                            Đã thanh toán
                        </option>

                        <option value="failed"
                            @selected(request('status') === 'failed')>
                            Thanh toán thất bại
                        </option>

                        <option value="refund_pending"
                            @selected(request('status') === 'refund_pending')>
                            Chờ hoàn tiền
                        </option>

                        <option value="refunded"
                            @selected(request('status') === 'refunded')>
                            Đã hoàn tiền
                        </option>

                        <option value="cancelled"
                            @selected(request('status') === 'cancelled')>
                            Đã hủy
                        </option>

                    </select>

                </div>


                {{-- Date from --}}
                <div class="finance-tx-field">

                    <label for="date_from">
                        TỪ NGÀY
                    </label>

                    <input
                        type="date"
                        id="date_from"
                        name="date_from"
                        value="{{ request('date_from') }}"
                    >

                </div>


                {{-- Date to --}}
                <div class="finance-tx-field">

                    <label for="date_to">
                        ĐẾN NGÀY
                    </label>

                    <input
                        type="date"
                        id="date_to"
                        name="date_to"
                        value="{{ request('date_to') }}"
                    >

                </div>

            </div>


            <div class="finance-tx-filter-actions">

                <button type="submit"
                        class="finance-tx-btn finance-tx-btn-dark">
                    🔍 Lọc giao dịch
                </button>

                <a href="{{ route('admin.finance.transactions') }}"
                   class="finance-tx-btn finance-tx-btn-light">
                    ↻ Xóa bộ lọc
                </a>

            </div>

        </form>

    </div>


    {{-- =====================================================
         TABLE
    ====================================================== --}}
    <div class="finance-tx-card">

        <div class="finance-tx-card-header">

            <div>

                <h3 class="finance-tx-card-title">
                    📋 Danh sách giao dịch
                </h3>

                <p class="finance-tx-card-subtitle">
                    {{ number_format($transactions->total()) }}
                    giao dịch được tìm thấy
                </p>

            </div>

            @if($transactions->total() > 0)

                <div class="finance-tx-card-subtitle">
                    Hiển thị
                    {{ $transactions->firstItem() }}
                    –
                    {{ $transactions->lastItem() }}
                </div>

            @endif

        </div>


        <div class="finance-tx-table-wrap">

            <table class="finance-tx-table">

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Mã giao dịch</th>

                        <th>Đơn hàng</th>

                        <th>Khách hàng</th>

                        <th>Phương thức</th>

                        <th>Số tiền</th>

                        <th>Trạng thái</th>

                        <th>Thời gian</th>

                        <th>Chi tiết</th>

                    </tr>

                </thead>


                <tbody>

                @forelse($transactions as $transaction)

                    @php

                        $statusClass = match ($transaction->status) {

                            'paid'
                                => 'finance-tx-status finance-tx-status-paid',

                            'failed', 'cancelled'
                                => 'finance-tx-status finance-tx-status-failed',

                            'pending', 'initiated'
                                => 'finance-tx-status finance-tx-status-warning',

                            'refund_pending'
                                => 'finance-tx-status finance-tx-status-purple',

                            'refunded'
                                => 'finance-tx-status finance-tx-status-info',

                            default
                                => 'finance-tx-status finance-tx-status-neutral',

                        };

                        $statusLabel = $transaction->statusLabel();

                    @endphp


                    <tr>

                        {{-- ID --}}
                        <td>

                            <span class="finance-tx-id">
                                #{{ $transaction->id }}
                            </span>

                        </td>


                        {{-- TRANSACTION --}}
                        <td>

                            <span class="finance-tx-code">

                                {{ $transaction->transaction_id
                                    ?: $transaction->gateway_order_id
                                    ?: '—' }}

                            </span>


                            @if($transaction->gateway_order_id)

                                <span class="finance-tx-sub">

                                    Gateway:
                                    {{ $transaction->gateway_order_id }}

                                </span>

                            @endif

                        </td>


                        {{-- ORDER --}}
                        <td>

                            @if($transaction->order)

                                <span class="finance-tx-order">
                                    {{ $transaction->order->order_code }}
                                </span>

                                <span class="finance-tx-order-id">
                                    #{{ $transaction->order->id }}
                                </span>

                            @else

                                <span class="muted">
                                    Không có đơn
                                </span>

                            @endif

                        </td>


                        {{-- CUSTOMER --}}
                        <td>

                            @if($transaction->order)

                                <span class="finance-tx-customer">
                                    {{ $transaction->order->customer_name }}
                                </span>

                                <span class="finance-tx-phone">
                                    {{ $transaction->order->phone }}
                                </span>

                            @else

                                <span class="muted">
                                    —
                                </span>

                            @endif

                        </td>


                        {{-- GATEWAY --}}
                        <td>

                            @if($transaction->gateway === 'momo')

                                <span class="finance-tx-gateway finance-tx-gateway-momo">
                                    ● MoMo
                                </span>

                            @elseif($transaction->gateway === 'cod')

                                <span class="finance-tx-gateway finance-tx-gateway-cod">
                                    ● COD
                                </span>

                            @else

                                <span class="finance-tx-gateway finance-tx-gateway-other">
                                    {{ strtoupper($transaction->gateway) }}
                                </span>

                            @endif

                        </td>


                        {{-- AMOUNT --}}
                        <td>

                            <span class="finance-tx-amount">
                                {{ number_format($transaction->amount, 0, ',', '.') }} ₫
                            </span>

                        </td>


                        {{-- STATUS --}}
                        <td>

                            <span class="{{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>


                            @if($transaction->message)

                                <span class="finance-tx-message">
                                    {{ $transaction->message }}
                                </span>

                            @endif

                        </td>


                        {{-- DATE --}}
                        <td>

                            <span class="finance-tx-date">

                                {{ $transaction->created_at?->format('d/m/Y') }}

                                <br>

                                {{ $transaction->created_at?->format('H:i') }}

                            </span>


                            @if($transaction->paid_at)

                                <span class="finance-tx-paid-date">

                                    ✓ Thanh toán:
                                    {{ $transaction->paid_at->format('d/m/Y H:i') }}

                                </span>

                            @endif

                        </td>


                        {{-- ACTION --}}
                        <td>

                            @if($transaction->order)

                                <a href="{{ route('admin.orders.show', $transaction->order) }}"
                                   class="finance-tx-view">
                                    Xem đơn →
                                </a>

                            @else

                                <span class="muted">
                                    —
                                </span>

                            @endif

                        </td>

                    </tr>


                @empty

                    <tr>

                        <td colspan="9"
                            class="finance-tx-empty">

                            <div class="finance-tx-empty-icon">
                                📭
                            </div>

                            <div class="finance-tx-empty-title">
                                Chưa có giao dịch
                            </div>

                            <div class="finance-tx-empty-text">
                                Không tìm thấy giao dịch phù hợp với bộ lọc hiện tại.
                            </div>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        @if($transactions->hasPages())

            <div class="finance-tx-pagination">

                {{ $transactions->withQueryString()->links() }}

            </div>

        @endif

    </div>

</div>

@endsection