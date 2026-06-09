@extends('layouts.admin')

@section('title', 'Admin — Kelola Pesanan | Defkan Computer')
@section('admin-title', 'Kelola Pesanan')
@section('tab-pesanan', 'active')

@section('content')
    <div class="pesanan-layout orders-fit-page">
        {{-- Stats Cards --}}
        <div class="stats-grid order-summary-grid">
            <div class="stat-card clickable" onclick="setFilterStatus('')">
                <div class="stat-icon-wrapper icon-blue">
                    <i data-lucide="clipboard-list" class="stat-icon"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Total Pesanan</span>
                    <h3 class="stat-value" id="stat-total">0</h3>
                    <span class="stat-subtext">Semua Pesanan</span>
                </div>
            </div>

            <div class="stat-card clickable" onclick="setFilterStatus('pending')">
                <div class="stat-icon-wrapper icon-orange">
                    <i data-lucide="hourglass" class="stat-icon"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Menunggu Pembayaran</span>
                    <h3 class="stat-value" id="stat-pending">0</h3>
                    <span class="stat-subtext">Perlu Dikonfirmasi</span>
                </div>
            </div>

            <div class="stat-card clickable" onclick="setFilterStatus('diproses')">
                <div class="stat-icon-wrapper icon-purple">
                    <i data-lucide="settings" class="stat-icon"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Diproses</span>
                    <h3 class="stat-value" id="stat-diproses">0</h3>
                    <span class="stat-subtext">Sedang Dikerjakan</span>
                </div>
            </div>

            <div class="stat-card clickable" onclick="setFilterStatus('dikirim')">
                <div class="stat-icon-wrapper icon-sky">
                    <i data-lucide="truck" class="stat-icon"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Sedang Dikirim</span>
                    <h3 class="stat-value" id="stat-dikirim">0</h3>
                    <span class="stat-subtext">Dalam Pengiriman</span>
                </div>
            </div>

            <div class="stat-card clickable" onclick="setFilterStatus('selesai')">
                <div class="stat-icon-wrapper icon-green">
                    <i data-lucide="check-circle-2" class="stat-icon"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Selesai</span>
                    <h3 class="stat-value" id="stat-selesai">0</h3>
                    <span class="stat-subtext">Pesanan Selesai</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-wrapper icon-cyan">
                    <i data-lucide="wallet" class="stat-icon"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Total Pendapatan</span>
                    <h3 class="stat-value stat-currency" id="stat-pendapatan">Rp 0</h3>
                    <span class="stat-subtext" id="stat-pendapatan-sub">Dari 0 Pesanan</span>
                </div>
            </div>
        </div>

        {{-- Filter Toolbar --}}
        <div class="glass-panel order-filter-card">
            <div class="order-filter-top">
                <div class="compact-search">
                    <i data-lucide="search" class="filter-search-icon"></i>
                    <input type="text" id="filter-search" class="filter-input"
                        placeholder="Cari ID, nama pelanggan, atau produk..." oninput="debounceFilter()">
                </div>

                <select id="filter-status" class="filter-input compact-select" onchange="loadPesanan()">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="diproses">Diproses</option>
                    <option value="dikirim">Dikirim</option>
                    <option value="selesai">Selesai</option>
                    <option value="dibatalkan">Dibatalkan</option>
                </select>

                <div class="date-range-filter">
                    <input type="date" id="filter-start" class="filter-input compact-date" onchange="loadPesanan()">
                    <span>-</span>
                    <input type="date" id="filter-end" class="filter-input compact-date" onchange="loadPesanan()">
                </div>

                <button class="btn-export-mini" type="button" onclick="exportToExcel()">
                    <i data-lucide="download" class="icon-sm"></i>
                    Export
                </button>
            </div>

            <div class="order-filter-bottom">
                <span class="filter-label">Filter Cepat:</span>
                <button class="quick-filter-btn" id="qf-today" onclick="setQuickDate('today')">Hari Ini</button>
                <button class="quick-filter-btn" id="qf-week" onclick="setQuickDate('week')">Minggu Ini</button>
                <button class="quick-filter-btn" id="qf-month" onclick="setQuickDate('month')">Bulan Ini</button>
                <button class="quick-filter-btn" id="qf-year" onclick="setQuickDate('year')">Tahun Ini</button>
            </div>
        </div>

        {{-- Table Area --}}
        <div class="glass-panel order-table-card">
            <div id="pesanan-table-wrapper" class="order-table-scroll">
                <div class="flex-center compact-loader">
                    <div class="loader"></div>
                </div>
            </div>

            <div id="pagination-wrapper" class="pagination-wrapper">
                <!-- Pagination -->
            </div>
        </div>
    </div>

    {{-- Modal detail pesanan --}}
    <div id="pesanan-modal" class="pesanan-modal" onclick="closePesananModal(event)">
        <div class="glass-panel pesanan-modal-box" onclick="event.stopPropagation()">
            <div id="pesanan-modal-content"></div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .pesanan-layout {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding-bottom: 12px;
        }

        .orders-fit-page {
            min-height: calc(100vh - 120px);
        }

        .glass-panel {
            background: var(--bg-surface, #ffffff);
            border: 1px solid var(--glass-border, #e5eaf3);
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.045);
        }

        /* ===== Summary Cards - Compact 1 Row ===== */
        .order-summary-grid {
            display: flex;
            align-items: stretch;
            gap: 7px;
            margin-bottom: 0;
            flex-shrink: 0;
            width: 100%;
            overflow-x: auto;
            padding-bottom: 2px;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .order-summary-grid::-webkit-scrollbar {
            display: none;
        }

        .stat-card {
            flex: 1 1 0;
            min-width: 0;
            min-height: 54px;
            padding: 7px 9px;
            border-radius: 13px;
            background: var(--bg-surface, #ffffff);
            border: 1px solid var(--glass-border, #e5eaf3);
            display: flex;
            align-items: center;
            gap: 7px;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.035);
            transition: 0.22s ease;
            overflow: hidden;
        }

        .stat-card.clickable {
            cursor: pointer;
        }

        .stat-card.clickable:hover {
            transform: translateY(-2px);
            border-color: rgba(37, 99, 235, 0.35);
            box-shadow: 0 9px 22px rgba(37, 99, 235, 0.08);
        }

        .stat-icon-wrapper {
            width: 30px;
            height: 30px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon {
            width: 15px;
            height: 15px;
        }

        .icon-blue {
            background: rgba(37, 99, 235, 0.10);
            color: var(--primary, #2563eb);
        }

        .icon-orange {
            background: rgba(245, 158, 11, 0.12);
            color: var(--warning, #f59e0b);
        }

        .icon-purple {
            background: rgba(139, 92, 246, 0.12);
            color: #8b5cf6;
        }

        .icon-sky {
            background: rgba(14, 165, 233, 0.12);
            color: var(--accent, #0ea5e9);
        }

        .icon-green {
            background: rgba(16, 185, 129, 0.12);
            color: var(--success, #10b981);
        }

        .icon-cyan {
            background: rgba(14, 165, 233, 0.12);
            color: var(--accent, #0ea5e9);
        }

        .stat-info {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 1px;
            flex: 1;
        }

        .stat-label {
            font-size: 8.2px;
            line-height: 1.12;
            font-weight: 800;
            color: var(--text-muted, #64748b);
            text-transform: uppercase;
            letter-spacing: 0.25px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .stat-value {
            font-size: 15px;
            line-height: 1.05;
            font-weight: 800;
            color: var(--text-main, #0f172a);
            margin: 0;
            letter-spacing: -0.02em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .stat-currency {
            font-size: 12.6px;
        }

        .stat-subtext {
            font-size: 8.8px;
            line-height: 1.15;
            color: var(--text-muted, #94a3b8);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .order-summary-grid .stat-card {
            max-width: none;
        }

        .order-summary-grid .stat-card:last-child .stat-info {
            min-width: 0;
        }

        /* ===== Filter Card - Modern Compact ===== */
        .order-filter-card {
            padding: 9px 12px;
            border-radius: 14px;
            flex-shrink: 0;
        }

        .order-filter-top {
            display: grid;
            grid-template-columns: minmax(320px, 1.45fr) 150px minmax(270px, 0.95fr) 112px;
            gap: 8px;
            align-items: center;
            margin-bottom: 8px;
        }

        .compact-search {
            position: relative;
            display: flex;
            align-items: center;
            min-width: 0;
        }

        .compact-search .filter-search-icon,
        .compact-search>svg {
            position: absolute;
            left: 11px;
            top: 50%;
            width: 14px;
            height: 14px;
            transform: translateY(-50%);
            color: var(--text-muted, #64748b);
            pointer-events: none;
            z-index: 2;
        }

        .filter-input {
            width: 100%;
            height: 32px;
            border: 1px solid var(--glass-border, #dbe3ef);
            border-radius: 9px;
            background: var(--bg-surface, #ffffff);
            color: var(--text-main, #0f172a);
            font-size: 12px;
            font-family: inherit;
            outline: none;
            transition: 0.22s ease;
            line-height: 32px;
        }

        .compact-search .filter-input {
            padding: 0 12px 0 32px;
        }

        .compact-select,
        .compact-date {
            padding: 0 11px;
        }

        .filter-input::placeholder {
            color: #94a3b8;
        }

        .filter-input:hover {
            border-color: #cbd5e1;
            background: #ffffff;
        }

        .filter-input:focus {
            border-color: var(--primary, #2563eb);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.09);
        }

        select.filter-input,
        .action-select,
        .rows-per-page {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 9px center;
            background-size: 12px;
            padding-right: 30px;
            cursor: pointer;
        }

        .date-range-filter {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
            gap: 6px;
            align-items: center;
            min-width: 0;
        }

        .date-range-filter span {
            color: var(--text-muted, #64748b);
            font-size: 11px;
            line-height: 1;
        }

        .btn-export-mini {
            height: 32px;
            padding: 0 13px;
            border-radius: 9px;
            border: 1px solid var(--primary, #2563eb);
            background: var(--primary, #2563eb);
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            cursor: pointer;
            box-shadow: 0 5px 14px rgba(37, 99, 235, 0.16);
            transition: 0.22s ease;
            white-space: nowrap;
        }

        .btn-export-mini:hover {
            transform: translateY(-1px);
            box-shadow: 0 7px 18px rgba(37, 99, 235, 0.22);
        }

        .order-filter-bottom {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            min-height: 24px;
        }

        .filter-label {
            font-size: 11.5px;
            font-weight: 800;
            color: var(--text-main, #0f172a);
            margin-right: 1px;
        }

        .quick-filter-btn {
            height: 24px;
            padding: 0 10px;
            border-radius: 999px;
            border: 1px solid var(--glass-border, #dbe3ef);
            background: #ffffff;
            color: var(--text-muted, #64748b);
            font-size: 11px;
            font-family: inherit;
            font-weight: 700;
            cursor: pointer;
            transition: 0.22s ease;
        }

        .quick-filter-btn:hover,
        .quick-filter-btn.active {
            background: #eff6ff;
            border-color: rgba(37, 99, 235, 0.35);
            color: var(--primary, #2563eb);
        }

        /* ===== Table ===== */
        .order-table-card {
            border-radius: 14px;
            overflow: hidden;
            flex: 1;
            min-height: 0;
            display: flex;
            flex-direction: column;
        }

        .order-table-scroll {
            width: 100%;
            overflow-x: auto;
            overflow-y: auto;
            flex: 1;
            scrollbar-color: rgba(37, 99, 235, 0.20) transparent;
        }

        .order-table-scroll.rows-4 {
            height: 330px;
            max-height: 330px;
        }

        .order-table-scroll.rows-more {
            max-height: calc(100vh - 275px);
        }

        .compact-loader {
            padding: 44px;
        }

        .pesanan-table {
            width: 100%;
            min-width: 940px;
            table-layout: fixed;
            border-collapse: collapse;
            font-size: 12px;
        }

        .pesanan-table thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: #ffffff;
            padding: 8px 10px;
            text-align: left;
            color: var(--text-muted, #64748b);
            font-size: 10px;
            line-height: 1.2;
            text-transform: uppercase;
            font-weight: 800;
            border-bottom: 1px solid var(--glass-border, #e5eaf3);
            white-space: nowrap;
        }

        .pesanan-table tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid rgba(15, 23, 42, 0.06);
            vertical-align: middle;
            color: var(--text-main, #0f172a);
        }

        .pesanan-table tbody tr:hover {
            background: #f8fbff;
        }

        .order-id {
            display: inline-flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
            min-width: 0;
            max-width: 74px;
            font-weight: 900;
            color: var(--primary, #2563eb);
            white-space: normal;
            word-break: break-word;
            overflow-wrap: anywhere;
            font-size: 11px;
            line-height: 1.18;
        }

        .order-id-prefix,
        .order-id-code {
            display: block;
            color: var(--primary, #2563eb);
        }

        .order-id-code {
            letter-spacing: 0.1px;
        }

        .date-main,
        .customer-name,
        .product-name {
            font-weight: 700;
            color: var(--text-main, #0f172a);
            line-height: 1.25;
        }

        .date-sub,
        .customer-phone,
        .payment-date,
        .muted-small {
            color: var(--text-muted, #64748b);
            font-size: 10.5px;
            line-height: 1.22;
        }

        .stack-sm {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .customer-phone {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .product-cell {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }

        .product-img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid var(--glass-border, #e5eaf3);
            background: #f8fafc;
            flex-shrink: 0;
        }

        .product-info {
            display: flex;
            flex-direction: column;
            gap: 1px;
            min-width: 0;
        }

        .product-name {
            max-width: 168px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 12px;
        }

        .item-badge {
            display: inline-flex;
            width: fit-content;
            align-items: center;
            height: 17px;
            padding: 0 6px;
            border-radius: 5px;
            background: rgba(37, 99, 235, 0.10);
            color: var(--primary, #2563eb);
            font-size: 9.5px;
            font-weight: 800;
        }

        .text-link-mini {
            color: var(--primary, #2563eb);
            text-decoration: none;
            font-size: 11px;
            font-weight: 800;
            line-height: 1.1;
        }

        .text-link-mini:hover {
            text-decoration: underline;
        }

        .total-price {
            text-align: right;
            font-size: 13px;
            font-weight: 900;
            color: var(--text-main, #0f172a);
            white-space: nowrap;
            letter-spacing: -0.01em;
        }

        .payment-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
        }

        .payment-method {
            font-weight: 700;
            color: var(--text-main, #0f172a);
            font-size: 11.5px;
        }

        .payment-badge,
        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: fit-content;
            border-radius: 999px;
            white-space: nowrap;
            font-weight: 800;
        }

        .payment-badge {
            height: 18px;
            padding: 0 8px;
            font-size: 9.5px;
        }

        .payment-paid {
            background: rgba(16, 185, 129, 0.13);
            color: var(--success, #10b981);
        }

        .payment-waiting {
            background: rgba(245, 158, 11, 0.14);
            color: var(--warning, #f59e0b);
        }

        .status-badge {
            height: 21px;
            padding: 0 9px;
            font-size: 10px;
            margin: 0 auto;
        }

        .s-pending {
            background: rgba(245, 158, 11, 0.14);
            color: var(--warning, #f59e0b);
        }

        .s-diproses {
            background: rgba(139, 92, 246, 0.14);
            color: #8b5cf6;
        }

        .s-dikirim {
            background: rgba(14, 165, 233, 0.14);
            color: var(--accent, #0ea5e9);
        }

        .s-selesai {
            background: rgba(16, 185, 129, 0.14);
            color: var(--success, #10b981);
        }

        .s-dibatalkan {
            background: rgba(239, 68, 68, 0.14);
            color: var(--danger, #ef4444);
        }

        .action-group {
            display: grid;
            grid-template-columns: 1fr;
            gap: 5px;
            width: 142px;
            margin: 0 auto;
        }

        .action-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5px;
        }

        .btn-action-mini,
        .action-select {
            height: 26px;
            border-radius: 8px;
            font-size: 10.5px;
            font-family: inherit;
        }

        .btn-action-mini {
            border: 1px solid var(--glass-border, #dbe3ef);
            background: #ffffff;
            color: var(--text-main, #334155);
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 0 7px;
            cursor: pointer;
            text-decoration: none;
            transition: 0.2s ease;
            min-width: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .btn-action-mini:hover {
            transform: translateY(-1px);
        }

        .btn-action-mini.primary {
            border-color: #bfdbfe;
            background: #f8fbff;
            color: var(--primary, #2563eb);
        }

        .btn-action-mini.purple {
            border-color: #e9d5ff;
            background: #faf5ff;
            color: #a855f7;
        }

        .btn-action-mini.disabled {
            opacity: 0.45;
            pointer-events: none;
        }

        .action-select {
            width: 100%;
            padding: 0 26px 0 8px;
            border: 1px solid var(--glass-border, #dbe3ef);
            background-color: #ffffff;
            color: var(--text-main, #334155);
            font-weight: 600;
            outline: none;
        }

        .action-select:focus {
            border-color: var(--primary, #2563eb);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
        }

        .empty-state,
        .error-state {
            min-height: 230px;
            padding: 44px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: var(--text-muted, #64748b);
            text-align: center;
        }

        .empty-state i {
            width: 42px;
            height: 42px;
            opacity: 0.45;
        }

        .error-state {
            color: var(--danger, #ef4444);
        }

        /* ===== Pagination ===== */
        .pagination-wrapper {
            min-height: 48px;
            padding: 9px 14px;
            border-top: 1px solid var(--glass-border, #e5eaf3);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
            background: #ffffff;
        }

        .footer-info {
            font-size: 12px;
            color: var(--text-muted, #64748b);
        }

        .pagination-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-buttons {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .pagination-btn {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            border: 1px solid var(--glass-border, #dbe3ef);
            background: #ffffff;
            color: var(--text-main, #334155);
            font-size: 12px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .pagination-btn:hover:not(:disabled) {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: var(--primary, #2563eb);
        }

        .pagination-btn.active {
            background: var(--primary, #2563eb);
            color: #ffffff;
            border-color: var(--primary, #2563eb);
        }

        .pagination-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .rows-per-page {
            width: 112px;
            height: 30px;
            border-radius: 8px;
            border: 1px solid var(--glass-border, #dbe3ef);
            background-color: #ffffff;
            padding: 0 28px 0 10px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-main, #334155);
            outline: none;
        }

        .page-ellipsis {
            color: var(--text-muted, #94a3b8);
            font-size: 12px;
            padding: 0 2px;
        }

        /* ===== Modal ===== */
        .pesanan-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.72);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(6px);
        }

        .pesanan-modal-box {
            max-width: 860px;
            width: min(92%, 860px);
            max-height: 90vh;
            overflow-y: auto;
            border-radius: 16px;
        }


        /* ===== Invoice Modal ===== */
        .invoice-modal {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
        }

        .invoice-topbar {
            padding: 14px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--glass-border, #e5eaf3);
            background: #f8fafc;
        }

        .invoice-topbar h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 800;
            color: var(--text-main, #0f172a);
        }

        .invoice-topbar p {
            margin: 2px 0 0;
            font-size: 11.5px;
            color: var(--text-muted, #64748b);
        }

        .btn-modal-close {
            width: 30px;
            height: 30px;
            border-radius: 9px;
            border: 1px solid var(--glass-border, #dbe3ef);
            background: #ffffff;
            color: var(--text-main, #334155);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .invoice-body {
            padding: 16px;
        }

        .invoice-brand-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: flex-start;
            margin-bottom: 14px;
        }

        .invoice-brand h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 900;
            color: var(--primary, #2563eb);
            letter-spacing: -0.03em;
        }

        .invoice-brand p,
        .invoice-meta p {
            margin: 3px 0;
            font-size: 11.5px;
            color: var(--text-muted, #64748b);
        }

        .invoice-meta {
            text-align: right;
        }

        .invoice-meta strong {
            display: block;
            font-size: 13px;
            color: var(--text-main, #0f172a);
            margin-bottom: 3px;
        }

        .invoice-info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 14px;
        }

        .invoice-info-card {
            border: 1px solid var(--glass-border, #e5eaf3);
            border-radius: 12px;
            padding: 10px 12px;
            background: #ffffff;
        }

        .invoice-info-card h4 {
            margin: 0 0 8px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.35px;
            color: var(--text-muted, #64748b);
        }

        .invoice-info-card p {
            margin: 4px 0;
            font-size: 12px;
            color: var(--text-main, #0f172a);
        }

        .invoice-items-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            border: 1px solid var(--glass-border, #e5eaf3);
            border-radius: 12px;
            overflow: hidden;
            font-size: 12px;
        }

        .invoice-items-table th,
        .invoice-items-table td {
            padding: 8px 9px;
            border-bottom: 1px solid var(--glass-border, #eef2f7);
            text-align: left;
            word-break: break-word;
        }

        .invoice-items-table th {
            background: #f8fafc;
            font-size: 10.5px;
            text-transform: uppercase;
            color: var(--text-muted, #64748b);
            font-weight: 800;
        }

        .invoice-items-table tr:last-child td {
            border-bottom: none;
        }

        .invoice-product-name {
            line-height: 1.35;
        }

        .invoice-total-row {
            margin-top: 12px;
            display: flex;
            justify-content: flex-end;
        }

        .invoice-total-box {
            min-width: 260px;
            border: 1px solid var(--glass-border, #e5eaf3);
            border-radius: 12px;
            overflow: hidden;
        }

        .invoice-total-line {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            padding: 10px 12px;
            font-size: 12px;
            color: var(--text-main, #0f172a);
        }

        .invoice-total-line.grand {
            background: #eff6ff;
            color: var(--primary, #2563eb);
            font-weight: 900;
            font-size: 14px;
        }

        .invoice-actions {
            padding: 12px 16px;
            border-top: 1px solid var(--glass-border, #e5eaf3);
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            background: #f8fafc;
        }

        .btn-invoice-action {
            height: 34px;
            border-radius: 10px;
            border: 1px solid var(--glass-border, #dbe3ef);
            background: #ffffff;
            color: var(--text-main, #334155);
            padding: 0 13px;
            font-size: 12px;
            font-weight: 800;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
        }

        .btn-invoice-action.primary {
            background: var(--primary, #2563eb);
            border-color: var(--primary, #2563eb);
            color: #ffffff;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            #invoice-print-area,
            #invoice-print-area * {
                visibility: visible;
            }

            #invoice-print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }

        @media (max-width: 1280px) {
            .stat-card {
                min-width: 150px;
            }

            .order-filter-top {
                grid-template-columns: minmax(260px, 1.25fr) 145px minmax(245px, 0.9fr) 106px;
            }
        }

        @media (max-width: 992px) {
            .order-filter-top {
                grid-template-columns: 1fr 145px;
            }

            .date-range-filter {
                grid-column: span 1;
            }

            .btn-export-mini {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .order-summary-grid {
                gap: 7px;
            }

            .stat-card {
                min-width: 150px;
                min-height: 52px;
                padding: 7px 9px;
            }

            .stat-icon-wrapper {
                width: 29px;
                height: 29px;
                border-radius: 10px;
            }

            .stat-icon {
                width: 15px;
                height: 15px;
            }

            .stat-label {
                font-size: 8.2px;
            }

            .stat-value {
                font-size: 14.5px;
            }

            .stat-currency {
                font-size: 12.5px;
            }

            .stat-subtext {
                font-size: 8.8px;
            }

            .order-filter-top {
                grid-template-columns: 1fr;
                gap: 7px;
                margin-bottom: 7px;
            }

            .order-filter-card {
                padding: 8px 10px;
            }

            .date-range-filter {
                grid-column: auto;
            }

            .filter-input,
            .btn-export-mini {
                height: 31px;
            }

            .pagination-wrapper {
                align-items: flex-start;
                flex-direction: column;
            }
        }


        /* ===== Final Fix: Table & Invoice Compact No Internal Scroll ===== */
        .order-table-card {
            overflow: visible;
        }

        .order-table-scroll,
        .order-table-scroll.rows-4,
        .order-table-scroll.rows-more {
            overflow-x: visible;
            overflow-y: visible;
            height: auto;
            max-height: none;
            flex: 0 0 auto;
        }

        .order-table-scroll.rows-4 {
            min-height: 318px;
        }

        .order-table-scroll.rows-more {
            min-height: auto;
        }

        .pesanan-table {
            width: 100%;
            min-width: 0;
            table-layout: fixed;
        }

        .pesanan-table th,
        .pesanan-table td {
            box-sizing: border-box;
        }

        .pesanan-table th:nth-child(1),
        .pesanan-table td:nth-child(1) {
            width: 8.5% !important;
        }

        .pesanan-table th:nth-child(2),
        .pesanan-table td:nth-child(2) {
            width: 9% !important;
        }

        .pesanan-table th:nth-child(3),
        .pesanan-table td:nth-child(3) {
            width: 12.5% !important;
        }

        .pesanan-table th:nth-child(4),
        .pesanan-table td:nth-child(4) {
            width: 23% !important;
        }

        .pesanan-table th:nth-child(5),
        .pesanan-table td:nth-child(5) {
            width: 11% !important;
        }

        .pesanan-table th:nth-child(6),
        .pesanan-table td:nth-child(6) {
            width: 15% !important;
        }

        .pesanan-table th:nth-child(7),
        .pesanan-table td:nth-child(7) {
            width: 8% !important;
        }

        .pesanan-table th:nth-child(8),
        .pesanan-table td:nth-child(8) {
            width: 13% !important;
        }

        .pesanan-table tbody td {
            padding: 7px 9px;
        }

        .product-img {
            width: 38px;
            height: 38px;
        }

        .product-name {
            max-width: 100%;
            font-size: 11.8px;
        }

        .total-price {
            font-size: 12.5px;
        }

        .payment-date {
            white-space: normal;
        }

        .action-group {
            width: 132px;
            max-width: 100%;
            gap: 4px;
        }

        .action-row {
            gap: 4px;
        }

        .btn-action-mini,
        .action-select {
            height: 25px;
            font-size: 10px;
            border-radius: 7px;
        }

        .btn-action-mini {
            padding: 0 5px;
            gap: 3px;
        }

        .btn-action-mini svg,
        .action-select svg {
            width: 12px;
            height: 12px;
            flex-shrink: 0;
        }

        .action-select {
            padding: 0 23px 0 7px;
            background-position: right 7px center;
        }

        .pagination-wrapper {
            min-height: 42px;
            padding: 7px 12px;
            overflow: visible;
        }

        .pagination-actions {
            flex-shrink: 0;
            gap: 8px;
        }

        .pagination-btn {
            width: 26px;
            height: 26px;
            border-radius: 7px;
            font-size: 11px;
        }

        .rows-per-page {
            width: 108px;
            height: 28px;
            font-size: 11px;
            padding-left: 8px;
        }

        .pesanan-modal-box {
            width: min(96vw, 930px);
            max-width: 930px;
            max-height: none;
            overflow: visible;
        }

        .invoice-modal {
            max-height: none;
        }

        .invoice-topbar {
            padding: 11px 14px;
        }

        .invoice-body {
            padding: 12px 14px;
        }

        .invoice-brand-row {
            margin-bottom: 10px;
        }

        .invoice-brand h2 {
            font-size: 18px;
        }

        .invoice-info-grid {
            gap: 8px;
            margin-bottom: 10px;
        }

        .invoice-info-card {
            padding: 8px 10px;
            border-radius: 10px;
        }

        .invoice-info-card h4 {
            margin-bottom: 6px;
            font-size: 10.5px;
        }

        .invoice-info-card p {
            margin: 3px 0;
            font-size: 11.5px;
            line-height: 1.25;
        }

        .invoice-items-table {
            font-size: 11.3px;
        }

        .invoice-items-table th,
        .invoice-items-table td {
            padding: 6px 7px;
        }

        .invoice-total-row {
            margin-top: 9px;
        }

        .invoice-total-box {
            min-width: 240px;
        }

        .invoice-total-line {
            padding: 8px 10px;
            font-size: 11.5px;
        }

        .invoice-total-line.grand {
            font-size: 13px;
        }

        .invoice-warranty-note {
            margin-top: 9px;
            padding: 8px 10px;
            border-radius: 10px;
            border: 1px solid rgba(37, 99, 235, 0.18);
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 11.5px;
            line-height: 1.35;
        }

        .invoice-actions {
            padding: 10px 14px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        let currentPage = 1;
        let lastPage = 1;
        let perPage = 4;
        let debounceTimer;
        let pesananCache = {};

        document.addEventListener('DOMContentLoaded', () => {
            if (!isLoggedIn() || !isAdmin()) {
                window.location.href = '/';
                return;
            }
            loadPesanan(1);
        });

        function debounceFilter() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                loadPesanan(1);
            }, 450);
        }

        function setFilterStatus(status) {
            document.getElementById('filter-status').value = status;
            loadPesanan(1);
        }

        function setQuickDate(type) {
            document.querySelectorAll('.quick-filter-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById('qf-' + type).classList.add('active');

            const today = new Date();
            const startInput = document.getElementById('filter-start');
            const endInput = document.getElementById('filter-end');

            let start, end;

            if (type === 'today') {
                start = today;
                end = today;
            } else if (type === 'week') {
                start = new Date(today.getFullYear(), today.getMonth(), today.getDate() - today.getDay() + 1);
                end = today;
            } else if (type === 'month') {
                start = new Date(today.getFullYear(), today.getMonth(), 1);
                end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            } else if (type === 'year') {
                start = new Date(today.getFullYear(), 0, 1);
                end = new Date(today.getFullYear(), 11, 31);
            }

            const pad = n => n.toString().padStart(2, '0');
            startInput.value = `${start.getFullYear()}-${pad(start.getMonth() + 1)}-${pad(start.getDate())}`;
            endInput.value = `${end.getFullYear()}-${pad(end.getMonth() + 1)}-${pad(end.getDate())}`;

            loadPesanan(1);
        }

        function updatePaginationUI(total) {
            const wrapper = document.getElementById('pagination-wrapper');
            if (total === 0) {
                wrapper.innerHTML = '';
                return;
            }

            const startItem = ((currentPage - 1) * perPage) + 1;
            const endItem = Math.min(currentPage * perPage, total);

            let pageButtons = '';
            for (let i = 1; i <= lastPage; i++) {
                if (i === 1 || i === lastPage || Math.abs(i - currentPage) <= 1) {
                    pageButtons +=
                        `<button class="pagination-btn ${i === currentPage ? 'active' : ''}" onclick="loadPesanan(${i})">${i}</button>`;
                } else if (i === currentPage - 2 || i === currentPage + 2) {
                    pageButtons += `<span class="page-ellipsis">...</span>`;
                }
            }

            wrapper.innerHTML = `
                                    <span class="footer-info">Menampilkan ${startItem}-${endItem} dari ${total} pesanan</span>
                                    <div class="pagination-actions">
                                        <div class="page-buttons">
                                            <button class="pagination-btn" onclick="loadPesanan(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>
                                                <i data-lucide="chevron-left" class="icon-sm"></i>
                                            </button>
                                            ${pageButtons}
                                            <button class="pagination-btn" onclick="loadPesanan(${currentPage + 1})" ${currentPage === lastPage ? 'disabled' : ''}>
                                                <i data-lucide="chevron-right" class="icon-sm"></i>
                                            </button>
                                        </div>

                                        <select class="rows-per-page" onchange="changePerPage(this.value)">
                                            <option value="4" ${perPage == 4 ? 'selected' : ''}>4 / halaman</option>
                                            <option value="10" ${perPage == 10 ? 'selected' : ''}>10 / halaman</option>
                                            <option value="20" ${perPage == 20 ? 'selected' : ''}>20 / halaman</option>
                                            <option value="50" ${perPage == 50 ? 'selected' : ''}>50 / halaman</option>
                                            <option value="100" ${perPage == 100 ? 'selected' : ''}>100 / halaman</option>
                                        </select>
                                    </div>
                                `;
            lucide.createIcons();
        }

        function changePerPage(val) {
            perPage = parseInt(val);
            loadPesanan(1);
        }


        const PRODUCT_PLACEHOLDER = 'https://placehold.co/90x90/e5e7eb/64748b?text=Laptop';

        function firstFilledValue(values = []) {
            return values.find(value => value !== undefined && value !== null && String(value).trim() !== '');
        }

        function normalizeImagePath(path) {
            if (!path) return PRODUCT_PLACEHOLDER;
            let value = String(path).trim();
            if (!value) return PRODUCT_PLACEHOLDER;

            if (value.startsWith('http://') || value.startsWith('https://') || value.startsWith('data:') || value.startsWith('blob:')) {
                return value;
            }

            value = value.replace(/^\/+/, '');
            value = value.replace(/^public\//, '');

            if (value.startsWith('storage/')) return '/' + value;
            return '/storage/' + value;
        }

        function resolveProductImage(produk = {}, item = {}) {
            let galleryValue = produk.galeri_foto || produk.galeri || produk.images || produk.fotos;
            if (typeof galleryValue === 'string') {
                try {
                    const parsed = JSON.parse(galleryValue);
                    galleryValue = Array.isArray(parsed) ? parsed : galleryValue;
                } catch (e) {
                    galleryValue = galleryValue.includes(',') ? galleryValue.split(',') : galleryValue;
                }
            }

            const galleryFirst = Array.isArray(galleryValue) ? galleryValue[0] : galleryValue;
            const rawImage = firstFilledValue([
                produk.foto,
                produk.gambar,
                produk.image,
                produk.thumbnail,
                produk.foto_produk,
                produk.gambar_produk,
                produk.main_image,
                galleryFirst,
                item.foto_produk,
                item.gambar_produk,
                item.foto,
                item.gambar,
                item.image
            ]);

            return normalizeImagePath(rawImage);
        }

        function resolveStatValue(stats = {}, keys = []) {
            for (const key of keys) {
                if (stats[key] !== undefined && stats[key] !== null) {
                    return stats[key];
                }
            }
            return 0;
        }

        function hasAnyStatKey(stats = {}, keys = []) {
            return keys.some(key => stats[key] !== undefined && stats[key] !== null);
        }

        async function loadDikirimStat() {
            const target = document.getElementById('stat-dikirim');
            if (!target) return;

            try {
                const params = new URLSearchParams({
                    page: 1,
                    per_page: 1,
                    status: 'dikirim'
                });
                const res = await apiFetch('/pesanan?' + params.toString());
                const total = Number(res?.data?.total ?? res?.total ?? 0);
                target.textContent = total;
            } catch (e) {
                target.textContent = '0';
                console.warn('Gagal memuat jumlah pesanan dikirim:', e);
            }
        }

        async function loadPesanan(page = 1) {
            currentPage = page;
            const status = document.getElementById('filter-status').value;
            const search = document.getElementById('filter-search').value;
            const start = document.getElementById('filter-start').value;
            const end = document.getElementById('filter-end').value;

            const wrapper = document.getElementById('pesanan-table-wrapper');
            wrapper.classList.toggle('rows-4', perPage <= 4);
            wrapper.classList.toggle('rows-more', perPage > 4);
            wrapper.innerHTML = '<div class="flex-center compact-loader"><div class="loader"></div></div>';

            try {
                let params = new URLSearchParams({
                    page,
                    per_page: perPage
                });
                if (status) params.append('status', status);
                if (search) params.append('search', search);
                if (start) params.append('dari_tanggal', start);
                if (end) params.append('sampai_tanggal', end);

                const res = await apiFetch('/pesanan?' + params.toString());
                const data = res.data;
                const list = data.data;
                pesananCache = {};
                lastPage = data.last_page;

                if (res.stats) {
                    document.getElementById('stat-total').textContent = res.stats.total;
                    document.getElementById('stat-pending').textContent = res.stats.pending;
                    document.getElementById('stat-diproses').textContent = res.stats.diproses;
                    document.getElementById('stat-dikirim').textContent = resolveStatValue(res.stats, ['dikirim', 'sedang_dikirim', 'total_dikirim', 'dikirim_count']);
                    document.getElementById('stat-selesai').textContent = res.stats.selesai;
                    document.getElementById('stat-pendapatan').textContent = formatMoney(res.stats.total_pendapatan);
                    document.getElementById('stat-pendapatan-sub').textContent =
                        `Dari ${res.stats.selesai} Pesanan Selesai`;

                    if (!hasAnyStatKey(res.stats, ['dikirim', 'sedang_dikirim', 'total_dikirim', 'dikirim_count'])) {
                        loadDikirimStat();
                    }
                }

                if (list.length === 0) {
                    wrapper.innerHTML = `
                                            <div class="empty-state">
                                                <i data-lucide="inbox"></i>
                                                <p>Tidak ada pesanan ditemukan.</p>
                                            </div>
                                        `;
                    updatePaginationUI(0);
                    lucide.createIcons();
                    if (typeof loadSidebarCounts === 'function') loadSidebarCounts();
                    return;
                }

                let tableHtml = `
                                        <table class="pesanan-table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 8.5%;">ID</th>
                                                    <th style="width: 9%;">Tanggal</th>
                                                    <th style="width: 12.5%;">Pelanggan</th>
                                                    <th style="width: 23%;">Produk</th>
                                                    <th style="text-align: center; width: 11%;">Total</th>
                                                    <th style="width: 15%;">Pembayaran</th>
                                                    <th style="text-align: center; width: 8%;">Status</th>
                                                    <th style="text-align: center; width: 13%;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                    `;

                list.forEach(p => {
                    pesananCache[p.id_pesanan] = p;
                    const dateObj = new Date(p.created_at);
                    const dateStr = dateObj.toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric'
                    });
                    const timeStr = dateObj.toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit'
                    }) + ' WIB';
                    const statusPesanan = p.status || 'pending';
                    const statusLabel = statusPesanan.charAt(0).toUpperCase() + statusPesanan.slice(1);
                    const formattedId = formatPesananId(p);

                    let productDisplay = '<span class="muted-small">—</span>';
                    if (p.detail && p.detail.length > 0) {
                        const firstItem = p.detail[0] || {};
                        const firstProduct = firstItem.produk || {};
                        const totalItems = p.detail.reduce((sum, item) => sum + Number(item.jumlah || item.qty || 0), 0);
                        const imgSrc = resolveProductImage(firstProduct, firstItem);
                        const pName = firstProduct.nama_produk || firstItem.nama_produk || 'Produk Dihapus';

                        productDisplay = `
                                                <div class="product-cell">
                                                    <img src="${imgSrc}" class="product-img" alt="Produk" loading="lazy" onerror="this.onerror=null;this.src='${PRODUCT_PLACEHOLDER}'">
                                                    <div class="product-info">
                                                        <span class="product-name" title="${escapeHtml(pName)}">${escapeHtml(pName)}</span>
                                                        <span class="item-badge">${totalItems || p.detail.length} Item</span>
                                                    </div>
                                                </div>
                                            `;
                    }

                    let payBadge = '';
                    if (p.bukti_pembayaran) {
                        payBadge = `<span class="payment-badge payment-paid">Dibayar</span>`;
                    } else {
                        payBadge = `<span class="payment-badge payment-waiting">Belum Dibayar</span>`;
                    }

                    let payMethodStr = p.metode_pembayaran || '—';
                    let payTimeStr = '';
                    if (p.waktu_pembayaran) {
                        const tObj = new Date(p.waktu_pembayaran);
                        payTimeStr = tObj.toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        }) + ', ' + tObj.toLocaleTimeString('id-ID', {
                            hour: '2-digit',
                            minute: '2-digit'
                        }) + ' WIB';
                    }

                    const buktiUrl = p.bukti_pembayaran ? normalizeImagePath(p.bukti_pembayaran) : '';
                    const buktiButton = p.bukti_pembayaran ?
                        `<a href="${buktiUrl}" target="_blank" class="btn-action-mini primary"><i data-lucide="file-check" class="icon-xs"></i> Bukti</a>` :
                        `<span class="btn-action-mini disabled"><i data-lucide="file-x" class="icon-xs"></i> Bukti</span>`;

                    tableHtml += `
                                            <tr>
                                                <td><span class="order-id" title="${escapeHtml(formattedId)}">${renderPesananIdDisplay(formattedId)}</span></td>
                                                <td>
                                                    <div class="stack-sm">
                                                        <span class="date-main">${dateStr}</span>
                                                        <span class="date-sub">${timeStr}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="stack-sm">
                                                        <span class="customer-name">${escapeHtml(p.pengguna?.nama || '—')}</span>
                                                        <span class="customer-phone">
                                                            ${escapeHtml(p.pengguna?.no_hp || '—')}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>${productDisplay}</td>
                                                <td class="total-price">${formatMoney(p.total_harga)}</td>
                                                <td>
                                                    <div class="payment-info">
                                                        ${payBadge}
                                                        <span class="payment-method">${escapeHtml(payMethodStr)}</span>
                                                        ${payTimeStr ? `<span class="payment-date">${payTimeStr}</span>` : ''}
                                                    </div>
                                                </td>
                                                <td style="text-align:center;">
                                                    <span class="status-badge s-${statusPesanan}">${statusLabel}</span>
                                                </td>
                                                <td>
                                                    <div class="action-group">
                                                        <select class="action-select" data-current="${statusPesanan}" onchange="updateStatusPesanan(${p.id_pesanan}, this.value, this)">
                                                            <option value="pending" ${statusPesanan === 'pending' ? 'selected' : ''}>Pending</option>
                                                            <option value="diproses" ${statusPesanan === 'diproses' ? 'selected' : ''}>Diproses</option>
                                                            <option value="dikirim" ${statusPesanan === 'dikirim' ? 'selected' : ''}>Dikirim</option>
                                                            <option value="selesai" ${statusPesanan === 'selesai' ? 'selected' : ''}>Selesai</option>
                                                            <option value="dibatalkan" ${statusPesanan === 'dibatalkan' ? 'selected' : ''}>Dibatalkan</option>
                                                        </select>
                                                        <div class="action-row">
                                                            ${buktiButton}
                                                            <button class="btn-action-mini purple" type="button" title="Invoice dan detail pesanan" onclick="showInvoice(${p.id_pesanan})">
                                                                <i data-lucide="receipt-text" class="icon-xs"></i> Invoice
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        `;
                });

                tableHtml += `</tbody></table>`;
                wrapper.innerHTML = tableHtml;
                updatePaginationUI(data.total);
                lucide.createIcons();
                if (typeof loadSidebarCounts === 'function') loadSidebarCounts();

            } catch (e) {
                wrapper.innerHTML = `<div class="error-state"><p>Gagal memuat: ${e.message}</p></div>`;
            }
        }

        async function updateStatusPesanan(id, status, selectEl) {
            if (!status) return;
            try {
                await apiFetch(`/pesanan/${id}/status`, {
                    method: 'PATCH',
                    body: {
                        status
                    }
                });
                showToast(`Status pesanan #${id} berhasil diperbarui`);
                loadPesanan(currentPage);
            } catch (e) {
                showToast(e.message, 'error');
                selectEl.value = selectEl.getAttribute('data-current');
            }
        }

        document.addEventListener('focus', function (e) {
            if (e.target.matches('.action-select')) {
                e.target.setAttribute('data-current', e.target.value);
            }
        }, true);

        function closePesananModal(e) {
            if (e.target === document.getElementById('pesanan-modal')) {
                closeInvoiceModal();
            }
        }

        function closeInvoiceModal() {
            document.getElementById('pesanan-modal').style.display = 'none';
            document.getElementById('pesanan-modal-content').innerHTML = '';
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function formatMoney(value) {
            const number = Number(value || 0);
            if (typeof formatRupiah === 'function') return formatRupiah(number);
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(number);
        }

        function formatDateTime(value) {
            if (!value) return '—';
            const obj = new Date(value);
            if (isNaN(obj.getTime())) return '—';
            return obj.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            }) + ', ' + obj.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            }) + ' WIB';
        }


        function formatPesananId(p = {}) {
            const pad = (n) => String(n).padStart(2, '0');
            const rawId = firstFilledValue([p.id_pesanan, p.id, p.order_id, p.id_order]) || '0';
            const dateSource = firstFilledValue([p.created_at, p.tanggal_pesanan, p.tanggal, p.createdAt]);
            const dObj = new Date(dateSource);

            if (!dateSource || isNaN(dObj.getTime())) {
                return `#PP-${rawId}`;
            }

            const yy = String(dObj.getFullYear()).slice(-2);
            const mm = pad(dObj.getMonth() + 1);
            const dd = pad(dObj.getDate());

            return `#PP-${yy}${mm}${dd}-${rawId}`;
        }


        function renderPesananIdDisplay(formattedId = '') {
            const idText = String(formattedId || '').trim();
            const match = idText.match(/^(#PP-)(.+)$/);

            if (match) {
                return `
                        <span class="order-id-prefix">${escapeHtml(match[1])}</span>
                        <span class="order-id-code">${escapeHtml(match[2])}</span>
                    `;
            }

            return `<span class="order-id-code">${escapeHtml(idText || '-')}</span>`;
        }

        function getStatusLabel(status) {
            const labels = {
                pending: 'Pending',
                diproses: 'Diproses',
                dikirim: 'Dikirim',
                selesai: 'Selesai',
                dibatalkan: 'Dibatalkan'
            };
            return labels[status] || (status ? status.charAt(0).toUpperCase() + status.slice(1) : 'Pending');
        }



        function getCustomerAddress(p = {}) {
            return firstFilledValue([
                p.alamat_pengiriman,
                p.alamat,
                p.detail_alamat,
                p.shipping_address,
                p.pengguna?.alamat,
                p.pengguna?.address,
                p.pengguna?.alamat_lengkap,
                p.pengguna?.detail_alamat
            ]) || '—';
        }

        function buildInvoiceItems(detail = []) {
            if (!detail || detail.length === 0) {
                return `
                                    <tr>
                                        <td colspan="6" style="text-align:center; color:#64748b;">Tidak ada detail produk.</td>
                                    </tr>
                                `;
            }

            return detail.map((item, index) => {
                const produk = item.produk || {};
                const namaProduk = produk.nama_produk || item.nama_produk || 'Produk Dihapus';
                const qty = Number(item.jumlah || item.qty || 0);
                const harga = Number(item.harga || item.harga_satuan || produk.harga || 0);
                const subtotal = Number(item.subtotal || item.total || (qty * harga));

                return `
                                    <tr>
                                        <td style="text-align:center; font-weight:800;">${index + 1}</td>
                                        <td class="invoice-product-name">${escapeHtml(namaProduk)}</td>
                                        <td style="text-align:center; white-space:nowrap; color:#2563eb; font-weight:800;">1 Bulan</td>
                                        <td style="text-align:center;">${qty || '-'}</td>
                                        <td style="text-align:right; white-space:nowrap;">${formatMoney(harga)}</td>
                                        <td style="text-align:right; font-weight:800; white-space:nowrap;">${formatMoney(subtotal)}</td>
                                    </tr>
                                `;
            }).join('');
        }

        function showInvoice(id) {
            const p = pesananCache[id];
            if (!p) {
                showToast('Data pesanan tidak ditemukan. Coba muat ulang halaman.', 'error');
                return;
            }

            const formattedId = formatPesananId(p);
            const invoiceCode = formattedId.replace(/^#/, '');
            const statusPesanan = p.status || 'pending';
            const namaPelanggan = p.pengguna?.nama || '—';
            const noHp = p.pengguna?.no_hp || '—';
            const email = p.pengguna?.email || '—';
            const alamat = getCustomerAddress(p);
            const metodePembayaran = p.metode_pembayaran || '—';
            const pembayaranStatus = p.bukti_pembayaran ? 'Dibayar' : 'Belum Dibayar';
            const totalHarga = Number(p.total_harga || 0);
            const createdAt = formatDateTime(p.created_at);
            const paidAt = formatDateTime(p.waktu_pembayaran);

            const invoiceHtml = `
                                <div class="invoice-modal">
                                    <div class="invoice-topbar">
                                        <div>
                                            <h3>Invoice Pesanan ${escapeHtml(formattedId)}</h3>
                                            <p>Detail pesanan, pembayaran, dan produk pelanggan.</p>
                                        </div>
                                        <button type="button" class="btn-modal-close" onclick="closeInvoiceModal()" title="Tutup">
                                            <i data-lucide="x" class="icon-sm"></i>
                                        </button>
                                    </div>

                                    <div class="invoice-body" id="invoice-print-area">
                                        <div class="invoice-brand-row">
                                            <div class="invoice-brand">
                                                <h2>Defkan Computer</h2>
                                                <p>Invoice Penjualan Laptop.</p>
                                            </div>
                                            <div class="invoice-meta">
                                                <strong>INV-${escapeHtml(invoiceCode)}</strong>
                                                <p>Tanggal: ${createdAt}</p>
                                                <p>Status: ${escapeHtml(getStatusLabel(statusPesanan))}</p>
                                            </div>
                                        </div>

                                        <div class="invoice-info-grid">
                                            <div class="invoice-info-card">
                                                <h4>Data Pelanggan</h4>
                                                <p><strong>${escapeHtml(namaPelanggan)}</strong></p>
                                                <p>No. HP: ${escapeHtml(noHp)}</p>
                                                <p>Email: ${escapeHtml(email)}</p>
                                                <p>Alamat: ${escapeHtml(alamat)}</p>
                                            </div>
                                            <div class="invoice-info-card">
                                                <h4>Data Pembayaran</h4>
                                                <p>Status: <strong>${escapeHtml(pembayaranStatus)}</strong></p>
                                                <p>Metode: ${escapeHtml(metodePembayaran)}</p>
                                                <p>Waktu Bayar: ${paidAt}</p>
                                            </div>
                                        </div>

                                        <table class="invoice-items-table">
                                            <thead>
                                                <tr>
                                                    <th style="text-align:center; width:42px;">No</th>
                                                    <th>Produk</th>
                                                    <th style="text-align:center; width:82px;">Garansi</th>
                                                    <th style="text-align:center; width:52px;">Qty</th>
                                                    <th style="text-align:right; width:120px;">Harga</th>
                                                    <th style="text-align:right; width:132px;">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${buildInvoiceItems(p.detail)}
                                            </tbody>
                                        </table>

                                        <div class="invoice-warranty-note">
                                            <strong>Garansi Pembelian:</strong> Setiap pembelian laptop mendapatkan garansi toko selama <strong>1 bulan</strong> sejak tanggal transaksi.
                                        </div>

                                        <div class="invoice-total-row">
                                            <div class="invoice-total-box">
                                                <div class="invoice-total-line">
                                                    <span>Total Pesanan</span>
                                                    <strong>${formatMoney(totalHarga)}</strong>
                                                </div>
                                                <div class="invoice-total-line grand">
                                                    <span>Grand Total</span>
                                                    <span>${formatMoney(totalHarga)}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="invoice-actions">
                                        <button type="button" class="btn-invoice-action" onclick="closeInvoiceModal()">
                                            <i data-lucide="x" class="icon-sm"></i> Tutup
                                        </button>
                                        <button type="button" class="btn-invoice-action primary" onclick="printInvoice()">
                                            <i data-lucide="printer" class="icon-sm"></i> Cetak Invoice
                                        </button>
                                    </div>
                                </div>
                            `;

            document.getElementById('pesanan-modal-content').innerHTML = invoiceHtml;
            document.getElementById('pesanan-modal').style.display = 'flex';
            lucide.createIcons();
        }

        function printInvoice() {
            const printArea = document.getElementById('invoice-print-area');
            if (!printArea) {
                showToast('Invoice belum tersedia.', 'error');
                return;
            }

            const printWindow = window.open('', '_blank', 'width=900,height=700');
            if (!printWindow) {
                showToast('Popup print diblokir browser.', 'error');
                return;
            }

            printWindow.document.write(`
                                <!doctype html>
                                <html>
                                <head>
                                    <meta charset="utf-8">
                                    <title>Invoice Pesanan</title>
                                    <style>
                                        body { font-family: Arial, sans-serif; margin: 24px; color: #0f172a; }
                                        h2 { margin: 0; color: #2563eb; }
                                        p { margin: 4px 0; color: #475569; font-size: 12px; }
                                        table { width: 100%; border-collapse: collapse; margin-top: 14px; font-size: 12px; }
                                        th, td { border: 1px solid #e5e7eb; padding: 10px; text-align: left; }
                                        th { background: #f8fafc; color: #475569; text-transform: uppercase; font-size: 11px; }
                                        .invoice-brand-row, .invoice-info-grid, .invoice-total-row { margin-bottom: 14px; }
                                        .invoice-brand-row { display: flex; justify-content: space-between; gap: 12px; }
                                        .invoice-meta { text-align: right; }
                                        .invoice-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
                                        .invoice-info-card { border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px; }
                                        .invoice-info-card h4 { margin: 0 0 8px; font-size: 11px; color: #64748b; text-transform: uppercase; }
                                        .invoice-total-row { display: flex; justify-content: flex-end; margin-top: 14px; }
                                        .invoice-warranty-note { margin-top: 10px; padding: 10px; border: 1px solid #bfdbfe; background: #eff6ff; color: #1d4ed8; border-radius: 8px; font-size: 12px; }
                                        .invoice-total-box { min-width: 270px; border: 1px solid #e5e7eb; }
                                        .invoice-total-line { display: flex; justify-content: space-between; padding: 10px 12px; }
                                        .invoice-total-line.grand { background: #eff6ff; color: #2563eb; font-weight: 800; }


                </style>
                                </head>
                                <body>${printArea.innerHTML}</body>
                                </html>
                            `);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        }

        function lihatDetailPesanan(id) {
            showInvoice(id);
        }

        function exportToExcel() {
            showToast('Mengekspor data pesanan...', 'info');
        }
    </script>
@endpush