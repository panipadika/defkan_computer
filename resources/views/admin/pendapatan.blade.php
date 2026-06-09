@extends('layouts.admin')

@section('title', 'Laporan Pendapatan — Defkan Computer')
@section('admin-title', 'Pendapatan')
@section('tab-pendapatan', 'active')

@section('content')
    <div class="revenue-layout revenue-fit-page">
        {{-- Toolbar --}}
        <div class="glass-panel revenue-toolbar">
            <div class="period-tabs" aria-label="Filter periode pendapatan">
                <button class="period-btn active" data-period="harian" type="button"
                    onclick="changePeriod('harian')">Harian</button>
                <button class="period-btn" data-period="mingguan" type="button"
                    onclick="changePeriod('mingguan')">Mingguan</button>
                <button class="period-btn" data-period="bulanan" type="button"
                    onclick="changePeriod('bulanan')">Bulanan</button>
                <button class="period-btn" data-period="tahunan" type="button"
                    onclick="changePeriod('tahunan')">Tahunan</button>
            </div>

            <div class="toolbar-actions">
                <label class="date-display date-picker-control" for="filter-end-date" title="Pilih tanggal akhir periode">
                    <i data-lucide="calendar-days" class="icon-xs"></i>
                    <span id="period-range-label">Memuat periode...</span>
                    <input type="date" id="filter-end-date" class="date-input-hidden"
                        onchange="changeSelectedDate(this.value)">
                    <i data-lucide="chevron-down" class="icon-xs muted-icon"></i>
                </label>

                <button class="btn-export-revenue" type="button" onclick="exportData()">
                    <i data-lucide="file-spreadsheet" class="icon-xs"></i>
                    Export Excel / CSV
                </button>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="revenue-stats-grid">
            <div class="revenue-stat-card">
                <div class="stat-icon-box icon-blue">
                    <i data-lucide="wallet" class="stat-icon"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-title">Total Pendapatan</span>
                    <h3 id="stat-revenue">Rp 0</h3>
                    <span id="stat-revenue-growth" class="stat-trend up">▲ 0% dari periode sebelumnya</span>
                </div>
            </div>

            <div class="revenue-stat-card">
                <div class="stat-icon-box icon-indigo">
                    <i data-lucide="shopping-bag" class="stat-icon"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-title">Pendapatan Pesanan</span>
                    <h3 id="stat-revenue-orders">Rp 0</h3>
                    <span id="stat-revenue-orders-growth" class="stat-trend up">▲ 0% dari periode sebelumnya</span>
                </div>
            </div>

            <div class="revenue-stat-card">
                <div class="stat-icon-box icon-orange">
                    <i data-lucide="wrench" class="stat-icon"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-title">Pendapatan Servis</span>
                    <h3 id="stat-revenue-servis">Rp 0</h3>
                    <span id="stat-revenue-servis-growth" class="stat-trend up">▲ 0% dari periode sebelumnya</span>
                </div>
            </div>

            <div class="revenue-stat-card">
                <div class="stat-icon-box icon-purple">
                    <i data-lucide="bar-chart-3" class="stat-icon"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-title">Jumlah Transaksi</span>
                    <h3 id="stat-transactions">0</h3>
                    <span id="stat-transactions-growth" class="stat-trend up">▲ 0% dari periode sebelumnya</span>
                </div>
            </div>

            <div class="revenue-stat-card">
                <div class="stat-icon-box icon-green">
                    <i data-lucide="trending-up" class="stat-icon"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-title">Pertumbuhan Pendapatan</span>
                    <h3 id="stat-growth">0%</h3>
                    <span class="stat-trend neutral">dari periode sebelumnya</span>
                </div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="charts-grid">
            <div class="glass-panel chart-card chart-wide">
                <div class="chart-card-header">
                    <h3>Tren Pendapatan</h3>
                    <select class="mini-select" id="revenue-chart-type" onchange="renderChart()">
                        <option value="revenue">Pendapatan (Rp)</option>
                        <option value="orders">Jumlah Order</option>
                    </select>
                </div>
                <div class="chart-canvas-wrap">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <div class="glass-panel chart-card chart-small">
                <div class="chart-card-header">
                    <h3>Jumlah Pesanan</h3>
                    <select class="mini-select" id="order-chart-type" onchange="renderOrdersChart()">
                        <option value="orders">Jumlah</option>
                        <option value="revenue">Pendapatan</option>
                    </select>
                </div>
                <div class="chart-canvas-wrap">
                    <canvas id="ordersChart"></canvas>
                </div>
            </div>

            <div class="glass-panel chart-card chart-small">
                <div class="chart-card-header">
                    <h3>Metode Pembayaran</h3>
                </div>
                <div class="payment-chart-layout">
                    <div class="donut-wrap">
                        <canvas id="paymentChart"></canvas>
                        <div class="donut-center">
                            <span>Total</span>
                            <strong id="payment-total-center">Rp 0</strong>
                        </div>
                    </div>
                    <div id="payment-legend" class="payment-legend"></div>
                </div>
            </div>
        </div>

        {{-- Details Table --}}
        <div class="glass-panel revenue-table-card">
            <div class="table-title-row">
                <div class="table-title-left">
                    <h3>
                        <i data-lucide="list" class="icon-xs"></i>
                        Rincian Data
                    </h3>
                </div>
                <div class="table-filters-right">
                    <div class="filter-search-box">
                        <i data-lucide="search" class="filter-search-icon"></i>
                        <input type="text" id="filter-search" class="filter-search-input" placeholder="Cari Nama / No HP..." oninput="handleFilterChange()">
                    </div>
                    
                    <div class="filter-select-box">
                        <select id="filter-type" class="filter-select" onchange="handleFilterTypeChange()">
                            <option value="all">Semua Tipe</option>
                            <option value="pesanan">Pesanan</option>
                            <option value="servis">Servis</option>
                        </select>
                    </div>

                    <div class="filter-select-box" id="filter-kerusakan-wrapper">
                        <select id="filter-kerusakan" class="filter-select" onchange="handleFilterChange()">
                            <option value="all">Semua Kerusakan</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="loading-state" class="flex-center compact-loader">
                <div class="loader"></div>
            </div>

            <div id="empty-state" class="empty-state" style="display:none;">
                <i data-lucide="inbox" class="empty-icon"></i>
                <p>Tidak ada data pendapatan ditemukan.</p>
            </div>

            <div id="table-wrapper" class="table-wrapper-fixed" style="display:none;">
                <table class="revenue-table order-detail-table">
                    <thead>
                        <tr>
                            <th style="width: 15%;">ID</th>
                            <th style="width: 11%;">Tanggal</th>
                            <th style="width: 13%;">Pelanggan</th>
                            <th style="width: 7%;">Tipe</th>
                            <th style="width: 24%;">Produk / Kendala</th>
                            <th class="total-header-cell" style="width: 11%;">TOTAL</th>
                            <th style="width: 11%;">Pembayaran</th>
                            <th style="width: 8%; text-align:center;">Status</th>
                        </tr>
                    </thead>
                    <tbody id="revenue-tbody"></tbody>
                </table>
            </div>

            <div id="pagination-wrapper" class="pagination-wrapper"></div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .revenue-layout {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 9px;
            padding-bottom: 10px;
        }

        .revenue-fit-page {
            min-height: calc(100vh - 120px);
        }

        .glass-panel {
            background: var(--bg-surface, #ffffff);
            border: 1px solid var(--glass-border, #e5eaf3);
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.045);
        }

        /* ===== Toolbar ===== */
        .revenue-toolbar {
            padding: 6px 8px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            min-height: 42px;
        }

        .period-tabs {
            display: inline-flex;
            align-items: center;
            gap: 2px;
            padding: 2px;
            background: #f1f5f9;
            border-radius: 9px;
            border: 1px solid var(--glass-border, #e5eaf3);
            flex-shrink: 0;
        }

        .period-btn {
            border: none;
            height: 24px;
            min-width: 58px;
            padding: 0 10px;
            border-radius: 7px;
            background: transparent;
            color: var(--text-muted, #64748b);
            font-size: 10.5px;
            font-weight: 800;
            font-family: inherit;
            cursor: pointer;
            transition: 0.22s ease;
        }

        .period-btn:hover {
            background: #ffffff;
            color: var(--primary, #2563eb);
        }

        .period-btn.active {
            background: var(--primary, #2563eb);
            color: #ffffff;
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.20);
        }

        .toolbar-actions {
            display: flex;
            align-items: center;
            gap: 7px;
            flex-wrap: wrap;
        }

        .date-display {
            height: 26px;
            min-width: 190px;
            padding: 0 9px;
            border-radius: 8px;
            border: 1px solid var(--glass-border, #dbe3ef);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            color: var(--text-muted, #64748b);
            font-size: 10.5px;
            font-weight: 800;
            background: #ffffff;
            white-space: nowrap;
        }

        .date-picker-control {
            position: relative;
            cursor: pointer;
            user-select: none;
            transition: 0.22s ease;
        }

        .date-picker-control:hover {
            border-color: rgba(37, 99, 235, 0.35);
            color: var(--primary, #2563eb);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.06);
        }

        .date-input-hidden {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .muted-icon {
            color: #94a3b8;
            margin-left: 2px;
        }

        .btn-export-revenue {
            height: 26px;
            padding: 0 11px;
            border-radius: 8px;
            border: 1px solid var(--primary, #2563eb);
            background: var(--primary, #2563eb);
            color: #ffffff;
            font-size: 10.5px;
            font-family: inherit;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            cursor: pointer;
            box-shadow: 0 5px 14px rgba(37, 99, 235, 0.16);
            transition: 0.22s ease;
        }

        .btn-export-revenue:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.24);
        }

        /* ===== Stats ===== */
        .revenue-stats-grid {
            display: flex;
            align-items: stretch;
            gap: 10px;
            width: 100%;
            overflow-x: auto;
            padding-bottom: 2px;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .revenue-stats-grid::-webkit-scrollbar {
            display: none;
        }

        .revenue-stat-card {
            flex: 1 1 0;
            min-width: 0;
            min-height: 76px;
            padding: 11px 12px;
            border-radius: 14px;
            background: #ffffff;
            border: 1px solid var(--glass-border, #e5eaf3);
            display: flex;
            align-items: flex-start;
            gap: 10px;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .stat-icon-box {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon {
            width: 16px;
            height: 16px;
        }

        .icon-blue {
            background: rgba(37, 99, 235, 0.10);
            color: var(--primary, #2563eb);
        }

        .icon-indigo {
            background: rgba(99, 102, 241, 0.11);
            color: #4f46e5;
        }

        .icon-purple {
            background: rgba(139, 92, 246, 0.12);
            color: #8b5cf6;
        }

        .icon-green {
            background: rgba(16, 185, 129, 0.13);
            color: #10b981;
        }

        .icon-orange {
            background: rgba(249, 115, 22, 0.13);
            color: #f97316;
        }

        .stat-content {
            min-width: 0;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .stat-title {
            color: var(--text-muted, #64748b);
            font-size: 10px;
            font-weight: 800;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .stat-content h3 {
            margin: 5px 0 2px;
            color: var(--text-main, #0f172a);
            font-size: 20px;
            line-height: 1;
            font-weight: 900;
            letter-spacing: -0.03em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .stat-trend {
            font-size: 10px;
            line-height: 1.25;
            font-weight: 800;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .stat-trend.up {
            color: #16a34a;
        }

        .stat-trend.down {
            color: #ef4444;
        }

        .stat-trend.neutral {
            color: var(--text-muted, #64748b);
        }

        /* ===== Charts - Horizontal Compact ===== */
        .charts-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.45fr) minmax(250px, 0.78fr) minmax(330px, 1.08fr);
            gap: 10px;
            align-items: stretch;
            width: 100%;
        }

        .chart-card {
            border-radius: 14px;
            padding: 10px 12px;
            min-height: 192px;
            height: 192px;
            display: flex;
            flex-direction: column;
            min-width: 0;
            overflow: hidden;
        }

        .chart-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            margin-bottom: 5px;
            min-height: 28px;
            flex-shrink: 0;
        }

        .chart-card-header h3,
        .table-title-row h3 {
            margin: 0;
            color: var(--text-main, #0f172a);
            font-size: 12.2px;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .mini-select {
            height: 26px;
            border-radius: 8px;
            border: 1px solid var(--glass-border, #dbe3ef);
            background: #ffffff;
            padding: 0 26px 0 8px;
            color: var(--text-main, #334155);
            font-size: 10px;
            font-weight: 700;
            font-family: inherit;
            outline: none;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 8px center;
            background-size: 11px;
        }

        .chart-canvas-wrap {
            position: relative;
            flex: 1;
            min-height: 142px;
            height: 142px;
        }

        .chart-canvas-wrap canvas {
            width: 100% !important;
            height: 142px !important;
        }

        .payment-chart-layout {
            display: grid;
            grid-template-columns: 170px minmax(0, 1fr);
            gap: 10px;
            align-items: center;
            flex: 1;
            min-height: 142px;
            height: 142px;
        }

        .donut-wrap {
            position: relative;
            width: 170px;
            height: 142px;
            min-height: 142px;
            justify-self: center;
        }

        .donut-wrap canvas {
            width: 170px !important;
            height: 142px !important;
        }

        .donut-center {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            pointer-events: none;
        }

        .donut-center span {
            font-size: 9px;
            color: var(--text-muted, #64748b);
            font-weight: 800;
        }

        .donut-center strong {
            font-size: 10px;
            color: var(--text-main, #0f172a);
            font-weight: 900;
            white-space: nowrap;
        }

        .payment-legend {
            display: flex;
            flex-direction: column;
            gap: 5px;
            min-width: 0;
            max-height: 142px;
            justify-content: center;
        }

        .payment-legend-item {
            display: grid;
            grid-template-columns: 8px 1fr;
            gap: 6px;
            align-items: start;
            color: var(--text-main, #0f172a);
            font-size: 10px;
            line-height: 1.18;
        }

        .legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            margin-top: 2px;
        }

        .legend-value {
            color: var(--text-muted, #64748b);
            font-size: 9.3px;
        }

        /* ===== Table ===== */
        .revenue-table-card {
            border-radius: 14px;
            padding: 9px 12px 8px;
            overflow: visible;
        }

        .table-title-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .table-title-left {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .table-filters-right {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .filter-search-box {
            position: relative;
            display: flex;
            align-items: center;
            min-width: 200px;
        }

        .filter-search-icon {
            position: absolute;
            left: 9px;
            width: 13px;
            height: 13px;
            color: var(--text-muted, #64748b);
            pointer-events: none;
        }

        .filter-search-input {
            width: 100%;
            height: 28px;
            padding: 0 10px 0 28px;
            border-radius: 7px;
            border: 1px solid var(--glass-border, #dbe3ef);
            background: #ffffff;
            color: var(--text-main, #334155);
            font-size: 10.5px;
            font-weight: 800;
            outline: none;
            transition: all 0.2s ease;
        }

        .filter-search-input:focus {
            border-color: var(--primary, #2563eb);
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
        }

        .filter-select-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .filter-select {
            height: 28px;
            border-radius: 7px;
            border: 1px solid var(--glass-border, #dbe3ef);
            background: #ffffff;
            color: var(--text-main, #334155);
            font-size: 10.5px;
            font-weight: 800;
            padding: 0 24px 0 8px;
            outline: none;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 7px center;
            background-size: 11px;
            min-width: 120px;
            transition: all 0.2s ease;
        }

        .filter-select:focus {
            border-color: var(--primary, #2563eb);
        }

        .compact-loader {
            min-height: 250px;
            padding: 40px;
        }

        .table-wrapper-fixed {
            width: 100%;
            overflow: visible;
            min-height: 218px;
        }

        .revenue-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            font-size: 10.8px;
        }

        .revenue-table thead th {
            text-align: left;
            padding: 6px 8px;
            color: var(--text-muted, #64748b);
            font-size: 9.2px;
            text-transform: uppercase;
            letter-spacing: 0.35px;
            font-weight: 900;
            border-bottom: 1px solid var(--glass-border, #e5eaf3);
            white-space: nowrap;
        }

        .revenue-table tbody td {
            padding: 6px 8px;
            color: var(--text-main, #0f172a);
            border-bottom: 1px solid rgba(15, 23, 42, 0.06);
            vertical-align: middle;
            height: 44px;
        }

        .revenue-table tbody tr:hover {
            background: #f8fbff;
        }

        .td-period {
            font-weight: 900;
        }

        .td-money {
            color: #059669;
            font-weight: 900;
            white-space: nowrap;
        }

        .td-muted {
            color: var(--text-muted, #64748b);
            font-weight: 800;
        }

        .growth-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 50px;
            height: 21px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 900;
            padding: 0 8px;
        }

        .growth-pill.up {
            background: rgba(16, 185, 129, 0.12);
            color: #059669;
        }

        .growth-pill.down {
            background: rgba(239, 68, 68, 0.12);
            color: #dc2626;
        }

        .growth-pill.neutral {
            background: rgba(100, 116, 139, 0.12);
            color: #64748b;
        }

        .pagination-wrapper {
            min-height: 32px;
            padding-top: 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            flex-wrap: wrap;
        }



        .pagination-info {
            font-size: 10.8px;
            color: var(--text-muted, #64748b);
            white-space: nowrap;
        }

        .pagination-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .page-buttons {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .rows-per-page {
            width: 104px;
            height: 25px;
            border-radius: 7px;
            border: 1px solid var(--glass-border, #dbe3ef);
            background-color: #ffffff;
            color: var(--text-main, #334155);
            font-size: 10.2px;
            font-weight: 800;
            padding: 0 24px 0 8px;
            outline: none;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 7px center;
            background-size: 11px;
            cursor: pointer;
        }

        .order-detail-table .order-id-text {
            color: var(--primary, #2563eb);
            font-weight: 900;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
            max-width: 100%;
        }

        .order-detail-table .order-main-text {
            font-weight: 800;
            color: var(--text-main, #0f172a);
            line-height: 1.15;
        }

        .order-detail-table .order-sub-text {
            display: block;
            margin-top: 1px;
            color: var(--text-muted, #64748b);
            font-size: 9.8px;
            line-height: 1.15;
        }

        .text-truncate {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
            max-width: 100%;
        }

        /* Column td styling - let fixed layout specify widths */

        .order-product-summary {
            display: flex;
            flex-direction: column;
            gap: 2px;
            width: 100%;
            min-width: 0;
            overflow: hidden;
        }

        .order-product-name {
            font-weight: 800;
            color: var(--text-main, #0f172a);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
            display: block;
            line-height: 1.15;
        }

        .order-item-badge,
        .order-status-badge {
            display: inline-flex;
            width: fit-content;
            align-items: center;
            justify-content: center;
            height: 17px;
            padding: 0 7px;
            border-radius: 999px;
            font-size: 9.3px;
            font-weight: 900;
            white-space: nowrap;
        }

        .order-item-badge {
            background: rgba(37, 99, 235, 0.10);
            color: var(--primary, #2563eb);
        }

        .order-status-badge.selesai {
            background: rgba(16, 185, 129, 0.13);
            color: #059669;
        }

        .order-status-badge.dikirim,
        .order-status-badge.diproses {
            background: rgba(37, 99, 235, 0.11);
            color: #2563eb;
        }

        .order-status-badge.pending {
            background: rgba(245, 158, 11, 0.14);
            color: #d97706;
        }

        .order-status-badge.dibatalkan {
            background: rgba(239, 68, 68, 0.12);
            color: #dc2626;
        }

        .page-btn {
            width: 25px;
            height: 25px;
            border-radius: 7px;
            border: 1px solid var(--glass-border, #dbe3ef);
            background: #ffffff;
            color: var(--text-main, #334155);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 10.5px;
            font-weight: 900;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .page-btn:hover:not(:disabled),
        .page-btn.active {
            background: var(--primary, #2563eb);
            border-color: var(--primary, #2563eb);
            color: #ffffff;
        }

        .page-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .empty-state {
            min-height: 250px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: var(--text-muted, #64748b);
        }

        .empty-icon {
            width: 42px;
            height: 42px;
            opacity: 0.45;
        }

        @media (max-width: 1280px) {
            .revenue-stat-card {
                min-width: 185px;
            }

            .charts-grid {
                grid-template-columns: minmax(430px, 1.35fr) minmax(260px, 0.82fr) minmax(360px, 1.05fr);
                overflow-x: auto;
                padding-bottom: 2px;
                scrollbar-width: none;
                -ms-overflow-style: none;
            }

            .charts-grid::-webkit-scrollbar {
                display: none;
            }

            .chart-card {
                min-height: 188px;
                height: 188px;
            }
        }

        @media (max-width: 768px) {
            .revenue-toolbar {
                align-items: stretch;
            }

            .period-tabs,
            .toolbar-actions,
            .date-display,
            .btn-export-revenue {
                width: 100%;
            }

            .period-tabs {
                overflow-x: auto;
                justify-content: flex-start;
            }

            .period-btn {
                min-width: 88px;
            }

            .revenue-stat-card {
                min-width: 165px;
                min-height: 68px;
            }

            .stat-content h3 {
                font-size: 16px;
            }

            .charts-grid {
                grid-template-columns: 1fr;
                overflow-x: visible;
            }

            .chart-card {
                height: 220px;
                min-height: 220px;
            }

            .chart-canvas-wrap,
            .chart-canvas-wrap canvas {
                height: 170px !important;
                min-height: 170px;
            }

            .payment-chart-layout {
                grid-template-columns: 150px 1fr;
                min-height: 170px;
                height: 170px;
            }

            .donut-wrap,
            .donut-wrap canvas {
                width: 150px !important;
                height: 150px !important;
                min-height: 150px;
            }

            .revenue-table {
                min-width: 760px;
            }

            .table-wrapper-fixed {
                overflow-x: auto;
            }
        }

        /* ===== Final Compact Fix: Toolbar & Rincian 4 Rows Visible ===== */
        .revenue-toolbar .icon-xs,
        .btn-export-revenue .icon-xs,
        .date-display .icon-xs {
            width: 13px;
            height: 13px;
        }

        .revenue-table-card {
            min-height: 292px;
        }

        .table-wrapper-fixed {
            min-height: 210px;
        }

        .revenue-table tbody tr {
            height: 44px;
        }

        .order-product-summary {
            gap: 1px;
        }

        .td-money {
            font-size: 10.8px;
        }

        @media (min-width: 769px) {
            .period-tabs {
                max-width: fit-content;
            }

            .toolbar-actions {
                margin-left: auto;
            }
        }



        /* ===== Final Fix 2: Compact Fit + Fixed 4 Rows Detail ===== */
        .revenue-layout {
            gap: 7px;
        }

        .revenue-toolbar {
            min-height: 36px;
            padding: 5px 7px;
            border-radius: 12px;
        }

        .period-tabs {
            padding: 2px;
            gap: 1px;
        }

        .period-btn {
            height: 22px;
            min-width: 54px;
            padding: 0 9px;
            font-size: 10px;
            border-radius: 7px;
        }

        .date-display {
            height: 24px;
            min-width: 172px;
            padding: 0 8px;
            font-size: 10px;
            border-radius: 8px;
            gap: 5px;
        }

        .btn-export-revenue {
            height: 24px;
            padding: 0 10px;
            font-size: 10px;
            border-radius: 8px;
            gap: 5px;
        }

        .revenue-stats-grid {
            gap: 8px;
        }

        .revenue-stat-card {
            min-height: 62px;
            padding: 8px 10px;
            gap: 8px;
            border-radius: 13px;
            align-items: center;
        }

        .stat-icon-box {
            width: 29px;
            height: 29px;
            border-radius: 10px;
        }

        .stat-icon {
            width: 15px;
            height: 15px;
        }

        .stat-title {
            font-size: 9px;
        }

        .stat-content h3 {
            margin: 2px 0 1px;
            font-size: 17px;
            line-height: 1.05;
        }

        .stat-trend {
            font-size: 9px;
        }

        .charts-grid {
            gap: 8px;
        }

        .chart-card {
            height: 162px;
            min-height: 162px;
            padding: 8px 10px;
            border-radius: 13px;
        }

        .chart-card-header {
            min-height: 24px;
            margin-bottom: 3px;
        }

        .chart-card-header h3,
        .table-title-row h3 {
            font-size: 11.6px;
        }

        .mini-select {
            height: 23px;
            font-size: 9.5px;
            border-radius: 7px;
        }

        .chart-canvas-wrap {
            min-height: 126px;
            height: 126px;
        }

        .chart-canvas-wrap canvas {
            height: 126px !important;
        }

        .payment-chart-layout {
            grid-template-columns: 136px minmax(0, 1fr);
            min-height: 126px;
            height: 126px;
            gap: 8px;
        }

        .donut-wrap,
        .donut-wrap canvas {
            width: 136px !important;
            height: 126px !important;
            min-height: 126px;
        }

        .payment-legend {
            gap: 4px;
            max-height: 126px;
        }

        .payment-legend-item {
            font-size: 9.4px;
            line-height: 1.12;
        }

        .legend-value {
            font-size: 8.8px;
        }

        .revenue-table-card {
            min-height: 302px;
            padding: 8px 12px 7px;
            border-radius: 14px;
        }

        .table-title-row {
            margin-bottom: 4px;
        }

        .table-wrapper-fixed {
            overflow: visible !important;
            min-height: 207px;
            height: 207px;
        }

        .revenue-table thead th {
            padding: 5px 8px;
            font-size: 9px;
            height: 28px;
        }

        .revenue-table tbody tr {
            height: 44px;
        }

        .revenue-table tbody td {
            height: 44px;
            padding: 5px 8px;
            line-height: 1.1;
        }

        .order-detail-table .order-main-text,
        .order-detail-table .order-sub-text,
        .order-product-name {
            display: block;
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .order-detail-table .order-main-text {
            font-size: 10.8px;
            line-height: 1.08;
        }

        .order-detail-table .order-sub-text {
            font-size: 9.2px;
            line-height: 1.08;
            margin-top: 1px;
        }

        .order-product-name {
            font-size: 10.8px;
            line-height: 1.08;
        }

        .order-item-badge,
        .order-status-badge {
            height: 16px;
            padding: 0 6px;
            font-size: 9px;
        }

        .pagination-wrapper {
            min-height: 29px;
            padding-top: 5px;
        }

        .pagination-info {
            font-size: 10.2px;
        }

        .page-btn {
            width: 24px;
            height: 24px;
            border-radius: 7px;
            font-size: 10px;
        }

        .rows-per-page {
            width: 100px;
            height: 24px;
            font-size: 9.8px;
            border-radius: 7px;
        }

        @media (max-width: 992px) {
            .table-wrapper-fixed {
                overflow-x: auto !important;
                height: auto;
            }
        }


        /* ===== FIX FINAL: Rincian Data dinamis tanpa scroll / tidak overlap ===== */
        .revenue-table-card {
            height: auto !important;
            min-height: auto !important;
            overflow: visible !important;
            padding: 8px 12px 8px !important;
        }

        .table-wrapper-fixed {
            --detail-header-height: 30px;
            --detail-row-height: 44px;
            --detail-visible-rows: 4;
            width: 100% !important;
            height: calc(var(--detail-header-height) + (var(--detail-row-height) * var(--detail-visible-rows))) !important;
            min-height: calc(var(--detail-header-height) + (var(--detail-row-height) * var(--detail-visible-rows))) !important;
            max-height: none !important;
            overflow: visible !important;
            transition: height .2s ease, min-height .2s ease;
        }

        .revenue-table {
            width: 100% !important;
            table-layout: fixed !important;
            border-collapse: collapse !important;
        }

        .revenue-table thead th {
            height: var(--detail-header-height) !important;
            padding: 5px 8px !important;
            line-height: 1.1 !important;
            vertical-align: middle !important;
        }

        .revenue-table tbody tr,
        .revenue-table tbody td {
            height: var(--detail-row-height) !important;
            max-height: var(--detail-row-height) !important;
        }

        .revenue-table tbody td {
            padding: 5px 8px !important;
            line-height: 1.1 !important;
            vertical-align: middle !important;
        }

        .order-detail-table .order-main-text,
        .order-detail-table .order-sub-text,
        .order-product-name {
            display: block !important;
            max-width: 100% !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .pagination-wrapper {
            position: relative !important;
            z-index: 1 !important;
            min-height: 32px !important;
            padding-top: 8px !important;
            margin-top: 0 !important;
            background: #ffffff !important;
        }

        @media (max-width: 992px) {
            .table-wrapper-fixed {
                overflow-x: auto !important;
                overflow-y: visible !important;
            }
        }



        /* ===== FINAL FIX: Pagination tidak kepotong + kolom total digeser kiri ===== */
        .revenue-table-card {
            padding: 7px 12px 13px !important;
            overflow: visible !important;
            margin-bottom: 12px !important;
        }

        .table-title-row {
            min-height: 26px !important;
            margin-bottom: 4px !important;
        }

        .table-title-row h3 {
            font-size: 13px !important;
            line-height: 1.1 !important;
            margin: 0 !important;
        }

        .table-wrapper-fixed {
            --detail-header-height: 28px !important;
            --detail-row-height: 39px !important;
            height: calc(var(--detail-header-height) + (var(--detail-row-height) * var(--detail-visible-rows))) !important;
            min-height: calc(var(--detail-header-height) + (var(--detail-row-height) * var(--detail-visible-rows))) !important;
            max-height: none !important;
            overflow: visible !important;
            margin-bottom: 6px !important;
        }

        .revenue-table thead th {
            height: var(--detail-header-height) !important;
            padding: 4px 8px !important;
            font-size: 9.8px !important;
            line-height: 1.05 !important;
            vertical-align: middle !important;
        }

        .revenue-table tbody tr,
        .revenue-table tbody td {
            height: var(--detail-row-height) !important;
            max-height: var(--detail-row-height) !important;
        }

        .revenue-table tbody td {
            padding: 4px 8px !important;
            line-height: 1.05 !important;
            vertical-align: middle !important;
        }



        .order-detail-table .order-main-text {
            font-size: 10.6px !important;
            line-height: 1.05 !important;
        }

        .order-detail-table .order-sub-text {
            font-size: 9.2px !important;
            line-height: 1.05 !important;
            margin-top: 1px !important;
        }

        .order-product-name {
            font-size: 10.6px !important;
            line-height: 1.05 !important;
        }

        .order-item-badge,
        .order-status-badge {
            height: 15px !important;
            padding: 0 6px !important;
            font-size: 8.8px !important;
        }

        .pagination-wrapper {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 10px !important;
            min-height: 34px !important;
            height: 34px !important;
            padding: 5px 0 0 !important;
            margin-top: 0 !important;
            overflow: visible !important;
            background: #ffffff !important;
            position: relative !important;
            z-index: 5 !important;
            flex-shrink: 0 !important;
        }

        .pagination-info {
            font-size: 10.5px !important;
            line-height: 1.2 !important;
            white-space: nowrap !important;
            overflow: visible !important;
            flex-shrink: 0 !important;
        }

        .pagination-actions,
        .page-buttons {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            flex-shrink: 0 !important;
            overflow: visible !important;
        }

        .page-btn {
            width: 25px !important;
            height: 25px !important;
            min-width: 25px !important;
            border-radius: 7px !important;
            font-size: 10.5px !important;
            line-height: 1 !important;
        }

        .rows-per-page {
            width: 106px !important;
            height: 26px !important;
            min-height: 26px !important;
            font-size: 10.5px !important;
            border-radius: 7px !important;
            padding: 0 24px 0 8px !important;
            background-position: right 8px center !important;
        }

        @media (max-width: 992px) {
            .pagination-wrapper {
                height: auto !important;
                min-height: 34px !important;
                align-items: flex-start !important;
                flex-direction: column !important;
            }

            .table-wrapper-fixed {
                overflow-x: auto !important;
                overflow-y: visible !important;
            }
        }




        .td-money {
            display: table-cell !important;
            white-space: nowrap !important;
            overflow: visible !important;
            text-overflow: clip !important;
            min-width: 118px !important;
            max-width: none !important;
            padding-right: 12px !important;
            font-size: 10.8px !important;
            letter-spacing: -0.01em !important;
        }

        @media (max-width: 1280px) {
            .revenue-table {
                min-width: 980px !important;
            }

            .table-wrapper-fixed {
                overflow-x: auto !important;
                overflow-y: visible !important;
            }
        }


        /* ===== FINAL FIX: Rincian Data Rapih + ID Kelola Pesanan + Total Tidak Kepotong ===== */
        .order-detail-table {
            table-layout: fixed !important;
            min-width: 1040px !important;
        }

        .order-detail-table th,
        .order-detail-table td {
            box-sizing: border-box !important;
            vertical-align: middle !important;
        }



        .order-detail-table .order-id-text {
            display: inline-flex !important;
            flex-direction: column !important;
            align-items: flex-start !important;
            justify-content: center !important;
            gap: 1px !important;
            color: var(--primary, #2563eb) !important;
            font-weight: 950 !important;
            line-height: 1.05 !important;
            white-space: normal !important;
            word-break: normal !important;
            overflow: visible !important;
            letter-spacing: -0.01em !important;
        }

        .order-id-prefix,
        .order-id-code-line {
            display: block !important;
            white-space: nowrap !important;
        }

        .order-id-prefix,
        .order-id-code-line {
            font-size: 10.8px !important;
        }

        .td-money {
            display: table-cell !important;
            min-width: 132px !important;
            max-width: none !important;
            white-space: nowrap !important;
            overflow: visible !important;
            text-overflow: clip !important;
            font-size: 11.2px !important;
            font-weight: 950 !important;
            letter-spacing: -0.02em !important;
            color: var(--text-main, #0f172a) !important;
        }

        .order-detail-table .order-main-text,
        .order-detail-table .order-sub-text,
        .order-product-name {
            max-width: 100% !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .table-wrapper-fixed {
            overflow-x: auto !important;
            overflow-y: visible !important;
        }

        @media (min-width: 1360px) {
            .order-detail-table {
                min-width: 0 !important;
            }
        }


        /* ===== FINAL PATCH: Produk Terjual sesuai Pesanan Selesai + Rincian tidak kepotong ===== */
        @media (min-width: 993px) {
            .table-wrapper-fixed {
                overflow-x: visible !important;
                overflow-y: visible !important;
            }

            .order-detail-table {
                min-width: 0 !important;
                width: 100% !important;
                table-layout: fixed !important;
            }
        }



        .order-detail-table .order-id-text {
            display: inline-flex !important;
            flex-direction: column !important;
            align-items: flex-start !important;
            justify-content: center !important;
            width: max-content !important;
            max-width: 100% !important;
            line-height: 1.06 !important;
            overflow: visible !important;
            white-space: normal !important;
        }

        .order-id-prefix,
        .order-id-code-line {
            font-size: 10.2px !important;
            line-height: 1.05 !important;
            letter-spacing: -0.02em !important;
            white-space: nowrap !important;
        }

        .td-money {
            display: table-cell !important;
            min-width: 0 !important;
            max-width: 100% !important;
            white-space: nowrap !important;
            overflow: visible !important;
            text-overflow: clip !important;
            font-size: 11px !important;
            padding-right: 14px !important;
        }

        .order-status-badge {
            max-width: 100% !important;
            white-space: nowrap !important;
            overflow: visible !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        let currentPeriod = 'harian';
        let selectedEndDate = toDateInput(new Date());

        let revenueChart = null;
        let ordersChart = null;
        let paymentChart = null;

        let currentData = null;
        let revenueRows = [];
        let orderCountRows = [];
        let allOrders = [];
        let previousAllOrders = [];
        let completedOrders = [];
        let previousCompletedOrders = [];

        let allServis = [];
        let previousAllServis = [];
        let completedServis = [];
        let previousCompletedServis = [];

        let tablePage = 1;
        let rowsPerPage = 4;

        const paymentColors = ['#2563eb', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444', '#64748b'];

        // Periode sesuai permintaan:
        // Harian  = data tanggal yang dipilih saja.
        // Mingguan = 7 hari terakhir dari tanggal yang dipilih.
        // Bulanan  = 30 hari terakhir dari tanggal yang dipilih.
        // Tahunan  = 365 hari terakhir dari tanggal yang dipilih.
        const periodDaySpans = {
            harian: 1,
            mingguan: 7,
            bulanan: 30,
            tahunan: 365
        };

        document.addEventListener('DOMContentLoaded', () => {
            if (!isLoggedIn() || !isAdmin()) {
                window.location.href = '/';
                return;
            }

            const dateInput = document.getElementById('filter-end-date');
            if (dateInput) dateInput.value = selectedEndDate;

            updatePeriodRangeLabel();

            if (typeof lucide !== 'undefined') lucide.createIcons();

            loadRevenueData();
        });

        function changePeriod(period) {
            if (!periodDaySpans[period]) return;

            currentPeriod = period;
            tablePage = 1;

            document.querySelectorAll('.period-btn').forEach(btn => {
                btn.classList.toggle('active', btn.getAttribute('data-period') === period);
            });

            updatePeriodRangeLabel();
            loadRevenueData();
        }

        function changeSelectedDate(value) {
            if (!value) return;

            selectedEndDate = value;
            tablePage = 1;

            updatePeriodRangeLabel();
            loadRevenueData();
        }

        async function loadRevenueData() {
            const loading = document.getElementById('loading-state');
            const tableWrapper = document.getElementById('table-wrapper');
            const emptyState = document.getElementById('empty-state');

            loading.style.display = 'flex';
            tableWrapper.style.display = 'none';
            emptyState.style.display = 'none';

            try {
                const activeRange = getPeriodDateRange(currentPeriod);
                const previousRange = getPreviousPeriodRange(activeRange);

                const params = new URLSearchParams({
                    period: currentPeriod,
                    dari_tanggal: activeRange.start,
                    sampai_tanggal: activeRange.end
                });

                // Tetap panggil endpoint lama agar fitur backend sebelumnya tidak berubah.
                // Namun statistik utama diprioritaskan dari data pesanan selesai yang sudah difilter.
                try {
                    const res = await apiFetch(`/admin/pendapatan?${params.toString()}`);
                    currentData = unwrapData(res);
                } catch (e) {
                    console.warn('Endpoint /admin/pendapatan tidak tersedia atau gagal:', e);
                    currentData = {};
                }

                // Ambil data dari menu Kelola Pesanan & Servis
                allOrders = await fetchOrdersForPeriod(activeRange);
                previousAllOrders = await fetchOrdersForPeriod(previousRange);
                completedOrders = await fetchCompletedOrdersForPeriod(activeRange);
                previousCompletedOrders = await fetchCompletedOrdersForPeriod(previousRange);

                allServis = await fetchServisForPeriod(activeRange);
                previousAllServis = await fetchServisForPeriod(previousRange);
                completedServis = await fetchCompletedServisForPeriod(activeRange);
                previousCompletedServis = await fetchCompletedServisForPeriod(previousRange);

                // Pastikan Produk Terjual mengambil data dari pesanan yang statusnya Selesai.
                // Jika endpoint /pesanan?status=selesai belum mengembalikan data lengkap,
                // ambil juga dari data Kelola Pesanan semua status lalu filter status selesai di frontend.
                completedOrders = mergeUniqueOrders([
                    ...completedOrders,
                    ...allOrders.filter(order => isCompletedOrder(order) && isDateInRange(getOrderRevenueDateValue(order) || getOrderDateValue(order), activeRange))
                ]);

                previousCompletedOrders = mergeUniqueOrders([
                    ...previousCompletedOrders,
                    ...previousAllOrders.filter(order => isCompletedOrder(order) && isDateInRange(getOrderRevenueDateValue(order) || getOrderDateValue(order), previousRange))
                ]);

                const endpointRows = normalizeRevenueItems(extractRevenueItems(currentData));
                const completedRows = buildRowsFromOrders([...completedOrders, ...completedServis], currentPeriod, activeRange);
                orderCountRows = buildRowsFromOrders([...allOrders, ...allServis], currentPeriod, activeRange);

                populateKerusakanDropdown();

                // Tren pendapatan memakai transaksi selesai, jumlah transaksi memakai semua transaksi.
                // Jika data transaksi tidak bisa diambil, baru fallback ke response /admin/pendapatan.
                revenueRows = completedRows.length > 0 ? completedRows : endpointRows;
                if (!orderCountRows.length) orderCountRows = endpointRows;

                loading.style.display = 'none';

                updatePeriodRangeLabel();
                updateStats();
                renderChart();
                renderOrdersChart();
                renderPaymentChart();

                const rowsForTable = getOrderRowsForTable();
                if (rowsForTable.length === 0) {
                    emptyState.style.display = 'flex';
                    tableWrapper.style.display = 'none';
                    document.getElementById('pagination-wrapper').innerHTML = '';
                } else {
                    tableWrapper.style.display = 'block';
                    renderTable();
                    renderPagination();
                }

                if (typeof lucide !== 'undefined') lucide.createIcons();
            } catch (err) {
                loading.style.display = 'none';
                emptyState.style.display = 'flex';
                document.getElementById('pagination-wrapper').innerHTML = '';
                showToast(err.message || 'Gagal memuat data pendapatan.', 'error');
            }
        }

        function unwrapData(res) {
            if (!res) return {};
            if (res.data && typeof res.data === 'object' && !Array.isArray(res.data)) return res.data;
            return res;
        }

        function getPeriodDateRange(period) {
            const endDate = parseDateInput(selectedEndDate) || new Date();
            endDate.setHours(0, 0, 0, 0);

            const span = periodDaySpans[period] || 1;
            const startDate = new Date(endDate);
            startDate.setDate(endDate.getDate() - (span - 1));
            startDate.setHours(0, 0, 0, 0);

            return {
                start: toDateInput(startDate),
                end: toDateInput(endDate),
                startDate,
                endDate,
                span
            };
        }

        function getPreviousPeriodRange(range) {
            const span = Number(range?.span || periodDaySpans[currentPeriod] || 1);

            const previousEnd = new Date(range.startDate);
            previousEnd.setDate(previousEnd.getDate() - 1);
            previousEnd.setHours(0, 0, 0, 0);

            const previousStart = new Date(previousEnd);
            previousStart.setDate(previousEnd.getDate() - (span - 1));
            previousStart.setHours(0, 0, 0, 0);

            return {
                start: toDateInput(previousStart),
                end: toDateInput(previousEnd),
                startDate: previousStart,
                endDate: previousEnd,
                span
            };
        }

        function parseDateInput(value) {
            if (!value) return null;

            const parts = String(value).split('-').map(Number);
            if (parts.length !== 3 || parts.some(Number.isNaN)) return null;

            return new Date(parts[0], parts[1] - 1, parts[2]);
        }

        function toDateInput(date) {
            const pad = n => String(n).padStart(2, '0');
            return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
        }

        function addEndOfDay(date) {
            const d = new Date(date);
            d.setHours(23, 59, 59, 999);
            return d;
        }

        function isDateInRange(value, range) {
            const date = new Date(value);
            if (isNaN(date.getTime())) return false;

            return date >= range.startDate && date <= addEndOfDay(range.endDate);
        }

        function isCompletedOrder(order = {}) {
            const status = String(order.status || order.status_pesanan || order.status_order || '').toLowerCase().trim();
            return ['selesai', 'completed', 'success', 'paid', 'lunas', 'done'].includes(status);
        }

        function mergeUniqueOrders(orders = []) {
            const map = new Map();

            orders.forEach((order, index) => {
                const key = firstFilledValue([
                    order.id_pesanan,
                    order.id_order,
                    order.order_id,
                    order.kode_pesanan,
                    order.kode_order,
                    order.id,
                    `${getOrderDateValue(order) || getOrderRevenueDateValue(order) || 'tanggal'}-${getOrderTotal(order)}-${index}`
                ]);

                if (!map.has(String(key))) {
                    map.set(String(key), order);
                }
            });

            return Array.from(map.values());
        }

        async function fetchOrdersForPeriod(range = getPeriodDateRange(currentPeriod), status = '') {
            const all = [];
            const maxPages = 60;
            let page = 1;
            let last = 1;

            try {
                do {
                    const params = new URLSearchParams({
                        page,
                        per_page: 100,
                        dari_tanggal: range.start,
                        sampai_tanggal: range.end
                    });

                    if (status) params.append('status', status);

                    const res = await apiFetch('/pesanan?' + params.toString());
                    const payload = unwrapData(res);

                    const list = Array.isArray(payload.data)
                        ? payload.data
                        : Array.isArray(payload)
                            ? payload
                            : [];

                    all.push(...list);

                    last = Number(payload.last_page || payload.meta?.last_page || 1);
                    page++;
                } while (page <= last && page <= maxPages);
            } catch (e) {
                console.warn('Gagal mengambil data pesanan:', e);
            }

            // Filter ulang di frontend supaya aman jika backend belum mendukung parameter tanggal.
            return all.filter(order => {
                const rawDate = getOrderDateValue(order) || getOrderRevenueDateValue(order);
                const statusText = String(order.status || order.status_pesanan || '').toLowerCase();
                const statusMatch = !status || statusText === status || (status === 'selesai' && isCompletedOrder(order));
                return statusMatch && isDateInRange(rawDate, range);
            });
        }

        async function fetchCompletedOrdersForPeriod(range = getPeriodDateRange(currentPeriod)) {
            const all = [];
            const maxPages = 60;
            let page = 1;
            let last = 1;

            try {
                do {
                    const params = new URLSearchParams({
                        page,
                        per_page: 100,
                        status: 'selesai',
                        dari_tanggal: range.start,
                        sampai_tanggal: range.end
                    });

                    const res = await apiFetch('/pesanan?' + params.toString());
                    const payload = unwrapData(res);

                    const list = Array.isArray(payload.data)
                        ? payload.data
                        : Array.isArray(payload)
                            ? payload
                            : [];

                    all.push(...list);

                    last = Number(payload.last_page || payload.meta?.last_page || 1);
                    page++;
                } while (page <= last && page <= maxPages);
            } catch (e) {
                console.warn('Gagal mengambil data pesanan selesai:', e);
            }

            // Pendapatan dan produk terjual hanya dihitung dari pesanan selesai.
            // Tanggal pendapatan diprioritaskan dari waktu pembayaran, lalu fallback ke tanggal pesanan.
            return all.filter(order => {
                const rawDate = getOrderRevenueDateValue(order) || getOrderDateValue(order);
                return isCompletedOrder(order) && isDateInRange(rawDate, range);
            });
        }

        async function fetchServisForPeriod(range = getPeriodDateRange(currentPeriod), status = '') {
            const all = [];
            const maxPages = 60;
            let page = 1;
            let last = 1;

            try {
                do {
                    const params = new URLSearchParams({
                        page,
                        per_page: 100,
                        dari_tanggal: range.start,
                        sampai_tanggal: range.end
                    });

                    if (status) params.append('status', status);

                    const res = await apiFetch('/servis?' + params.toString());
                    const payload = unwrapData(res);

                    const list = Array.isArray(payload.data)
                        ? payload.data
                        : Array.isArray(payload)
                            ? payload
                            : [];

                    all.push(...list.map(s => ({ ...s, _is_servis: true })));

                    last = Number(payload.last_page || payload.meta?.last_page || 1);
                    page++;
                } while (page <= last && page <= maxPages);
            } catch (e) {
                console.warn('Gagal mengambil data servis:', e);
            }

            return all.filter(order => {
                const rawDate = getOrderDateValue(order) || getOrderRevenueDateValue(order);
                const statusText = String(order.status || order.status_servis || '').toLowerCase();
                const statusMatch = !status || statusText === status || (status === 'selesai' && isCompletedOrder(order));
                return statusMatch && isDateInRange(rawDate, range);
            });
        }

        async function fetchCompletedServisForPeriod(range = getPeriodDateRange(currentPeriod)) {
            const all = [];
            const maxPages = 60;
            let page = 1;
            let last = 1;

            try {
                do {
                    const params = new URLSearchParams({
                        page,
                        per_page: 100,
                        status: 'selesai',
                        dari_tanggal: range.start,
                        sampai_tanggal: range.end
                    });

                    const res = await apiFetch('/servis?' + params.toString());
                    const payload = unwrapData(res);

                    const list = Array.isArray(payload.data)
                        ? payload.data
                        : Array.isArray(payload)
                            ? payload
                            : [];

                    all.push(...list.map(s => ({ ...s, _is_servis: true })));

                    last = Number(payload.last_page || payload.meta?.last_page || 1);
                    page++;
                } while (page <= last && page <= maxPages);
            } catch (e) {
                console.warn('Gagal mengambil data servis selesai:', e);
            }

            return all.filter(order => {
                const rawDate = getOrderRevenueDateValue(order) || getOrderDateValue(order);
                return isCompletedOrder(order) && isDateInRange(rawDate, range);
            });
        }

        function getOrderRevenueDateValue(order = {}) {
            return firstFilledValue([
                order.waktu_pembayaran,
                order.payment_at,
                order.paid_at,
                order.tanggal_bayar,
                order.updated_at,
                order.created_at,
                order.tanggal,
                order.order_date
            ]);
        }

        function getOrderDateValue(order = {}) {
            return firstFilledValue([
                order.created_at,
                order.tanggal,
                order.order_date,
                order.waktu_pembayaran,
                order.payment_at,
                order.paid_at,
                order.updated_at
            ]);
        }

        function getOrderPaymentDateValue(order = {}) {
            return firstFilledValue([
                order.waktu_pembayaran,
                order.payment_at,
                order.paid_at,
                order.tanggal_bayar,
                order.updated_at,
                order.created_at
            ]);
        }

        function extractRevenueItems(data = {}) {
            return firstFilledValue([
                data.items,
                data.data,
                data.rows,
                data.chart,
                data.detail,
                data.rincian,
                data.pendapatan,
                data.revenue_items
            ]) || [];
        }

        function normalizeRevenueItems(items) {
            if (!Array.isArray(items)) return [];

            return items.map((item, index) => {
                const orders = Number(firstFilledValue([
                    item.orders,
                    item.order,
                    item.total_orders,
                    item.jumlah_pesanan,
                    item.total_pesanan,
                    item.pesanan_count
                ]) || 0);

                const revenue = Number(firstFilledValue([
                    item.revenue,
                    item.pendapatan,
                    item.total_revenue,
                    item.total_pendapatan,
                    item.total,
                    item.nominal
                ]) || 0);

                const products = Number(firstFilledValue([
                    item.products,
                    item.produk_terjual,
                    item.total_produk,
                    item.items_sold,
                    item.total_items,
                    item.qty,
                    item.jumlah_produk
                ]) || 0);

                return {
                    key: String(index).padStart(3, '0'),
                    label: item.label || item.periode || item.tanggal || item.date || item.bulan || item.tahun || `Periode ${index + 1}`,
                    orders,
                    revenue,
                    products
                };
            }).filter(row => row.orders > 0 || row.revenue > 0 || row.products > 0);
        }

        function buildRowsFromOrders(orders = [], period = 'harian', range = getPeriodDateRange(period)) {
            const groupMode = getGroupMode(period);
            const groups = createEmptyGroupsForRange(period, range);

            orders.forEach(order => {
                const rawDate = getOrderRevenueDateValue(order);
                const date = new Date(rawDate);
                if (isNaN(date.getTime())) return;

                if (!isDateInRange(rawDate, range)) return;

                const key = getGroupKey(date, groupMode);
                const label = getGroupLabel(date, groupMode);
                const total = getOrderTotal(order);
                const qty = getOrderQty(order);

                if (!groups.has(key)) {
                    groups.set(key, { key, label, orders: 0, revenue: 0, products: 0 });
                }

                const row = groups.get(key);
                row.orders += 1;
                row.revenue += total;
                row.products += qty;
            });

            return Array.from(groups.values()).sort((a, b) => a.key.localeCompare(b.key));
        }

        function getGroupMode(period) {
            // Harian, mingguan, bulanan ditampilkan per hari.
            // Tahunan ditampilkan per bulan supaya grafik tidak terlalu padat.
            return period === 'tahunan' ? 'bulanan' : 'harian';
        }

        function createEmptyGroupsForRange(period, range) {
            const groups = new Map();
            const groupMode = getGroupMode(period);
            const cursor = new Date(range.startDate);
            const end = new Date(range.endDate);

            if (groupMode === 'bulanan') {
                cursor.setDate(1);

                const lastMonth = new Date(end.getFullYear(), end.getMonth(), 1);
                while (cursor <= lastMonth) {
                    const key = getGroupKey(cursor, groupMode);
                    const label = getGroupLabel(cursor, groupMode);
                    groups.set(key, { key, label, orders: 0, revenue: 0, products: 0 });
                    cursor.setMonth(cursor.getMonth() + 1);
                }

                return groups;
            }

            while (cursor <= end) {
                const key = getGroupKey(cursor, groupMode);
                const label = getGroupLabel(cursor, groupMode);
                groups.set(key, { key, label, orders: 0, revenue: 0, products: 0 });
                cursor.setDate(cursor.getDate() + 1);
            }

            return groups;
        }

        function getGroupKey(date, groupMode) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');

            if (groupMode === 'bulanan') return `${year}-${month}`;

            return `${year}-${month}-${day}`;
        }

        function getGroupLabel(date, groupMode) {
            if (groupMode === 'bulanan') {
                return date.toLocaleDateString('id-ID', { month: 'short', year: 'numeric' });
            }

            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        }

        function updatePeriodRangeLabel() {
            const label = document.getElementById('period-range-label');
            const dateInput = document.getElementById('filter-end-date');
            const range = getPeriodDateRange(currentPeriod);

            if (dateInput) dateInput.value = selectedEndDate;
            if (!label) return;

            if (currentPeriod === 'harian') {
                label.textContent = `${formatShortDate(range.endDate)} · 1 hari`;
                return;
            }

            label.textContent = `${formatShortDate(range.startDate)} - ${formatShortDate(range.endDate)}`;
        }

        function updateStats() {
            const currentCompletedOrdersStats = deriveStatsFromOrders(completedOrders);
            const previousCompletedOrdersStats = deriveStatsFromOrders(previousCompletedOrders);
            const currentAllOrdersStats = deriveStatsFromOrders(allOrders);
            const previousAllOrdersStats = deriveStatsFromOrders(previousAllOrders);

            const currentCompletedServisStats = deriveStatsFromOrders(completedServis);
            const previousCompletedServisStats = deriveStatsFromOrders(previousCompletedServis);
            const currentAllServisStats = deriveStatsFromOrders(allServis);
            const previousAllServisStats = deriveStatsFromOrders(previousAllServis);

            const fallbackRevenue = sumBy(revenueRows, 'revenue');
            const fallbackOrders = sumBy(orderCountRows, 'orders') || sumBy(revenueRows, 'orders');

            const revenueOrders = completedOrders.length > 0 ? currentCompletedOrdersStats.totalRevenue : fallbackRevenue;
            const revenueServis = currentCompletedServisStats.totalRevenue;
            const totalRevenue = revenueOrders + revenueServis;

            const prevRevenueOrders = previousCompletedOrdersStats.totalRevenue;
            const prevRevenueServis = previousCompletedServisStats.totalRevenue;
            const prevTotalRevenue = prevRevenueOrders + prevRevenueServis;

            const countOrders = allOrders.length > 0 ? currentAllOrdersStats.totalOrders : fallbackOrders;
            const countServis = currentAllServisStats.totalOrders;
            const totalTransactions = countOrders + countServis;

            const prevCountOrders = previousAllOrdersStats.totalOrders;
            const prevCountServis = previousAllServisStats.totalOrders;
            const prevTotalTransactions = prevCountOrders + prevCountServis;

            const revenueGrowth = calcGrowth(totalRevenue, prevTotalRevenue);
            const revenueOrdersGrowth = calcGrowth(revenueOrders, prevRevenueOrders);
            const revenueServisGrowth = calcGrowth(revenueServis, prevRevenueServis);
            const transactionsGrowth = calcGrowth(totalTransactions, prevTotalTransactions);

            document.getElementById('stat-revenue').textContent = formatMoney(totalRevenue);
            if(document.getElementById('stat-revenue-orders')) document.getElementById('stat-revenue-orders').textContent = formatMoney(revenueOrders);
            if(document.getElementById('stat-revenue-servis')) document.getElementById('stat-revenue-servis').textContent = formatMoney(revenueServis);
            if(document.getElementById('stat-transactions')) document.getElementById('stat-transactions').textContent = formatNumber(totalTransactions);
            if(document.getElementById('stat-growth')) document.getElementById('stat-growth').textContent = `${revenueGrowth}%`;

            setTrendText('stat-revenue-growth', revenueGrowth, 'dari periode sebelumnya');
            setTrendText('stat-revenue-orders-growth', revenueOrdersGrowth, 'dari periode sebelumnya');
            setTrendText('stat-revenue-servis-growth', revenueServisGrowth, 'dari periode sebelumnya');
            setTrendText('stat-transactions-growth', transactionsGrowth, 'dari periode sebelumnya');
        }

        function deriveStatsFromOrders(orders = []) {
            return orders.reduce((acc, order) => {
                acc.totalOrders += 1;
                acc.totalRevenue += getOrderTotal(order);
                acc.totalProducts += getOrderQty(order);
                return acc;
            }, { totalOrders: 0, totalRevenue: 0, totalProducts: 0 });
        }

        function getOrderTotal(order = {}) {
            return Number(firstFilledValue([
                order.total_harga,
                order.total_biaya_final,
                order.total_biaya,
                order.total,
                order.grand_total,
                order.total_pendapatan,
                order.total_bayar,
                order.nominal
            ]) || 0);
        }

        function getOrderQty(order = {}) {
            const detail = order.detail || order.details || order.items || order.produk || [];

            if (Array.isArray(detail) && detail.length) {
                return detail.reduce((sum, item) => {
                    return sum + Number(item.jumlah || item.qty || item.quantity || 1);
                }, 0);
            }

            return Number(order.total_item || order.total_items || order.jumlah_item || 1);
        }

        function calcAov(item = {}) {
            return Number(item.orders || 0) > 0
                ? Math.round(Number(item.revenue || 0) / Number(item.orders || 0))
                : 0;
        }

        function calcGrowth(current, previous) {
            current = Number(current || 0);
            previous = Number(previous || 0);

            if (previous === 0) return current > 0 ? 100 : 0;

            return Math.round(((current - previous) / previous) * 100);
        }

        function setTrendText(id, value, suffix) {
            const el = document.getElementById(id);
            if (!el) return;

            const numeric = Number(value || 0);
            el.classList.remove('up', 'down', 'neutral');
            el.classList.add(numeric > 0 ? 'up' : numeric < 0 ? 'down' : 'neutral');
            el.textContent = `${numeric > 0 ? '▲' : numeric < 0 ? '▼' : '•'} ${Math.abs(numeric)}% ${suffix}`;
        }

        function renderChart() {
            const canvas = document.getElementById('revenueChart');
            if (!canvas || !window.Chart) return;

            const ctx = canvas.getContext('2d');
            const chartType = document.getElementById('revenue-chart-type')?.value || 'revenue';
            const sourceRows = chartType === 'orders' ? (orderCountRows.length ? orderCountRows : revenueRows) : revenueRows;
            const labels = sourceRows.map(item => item.label);
            const values = sourceRows.map(item => chartType === 'orders' ? item.orders : item.revenue);

            if (revenueChart) revenueChart.destroy();

            revenueChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: chartType === 'orders' ? 'Jumlah Order' : 'Pendapatan',
                        data: values,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.09)',
                        borderWidth: 3,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        tension: 0.35,
                        fill: true
                    }]
                },
                options: defaultChartOptions(chartType === 'orders' ? 'orders' : 'revenue')
            });
        }

        function renderOrdersChart() {
            const canvas = document.getElementById('ordersChart');
            if (!canvas || !window.Chart) return;

            const ctx = canvas.getContext('2d');
            const chartType = document.getElementById('order-chart-type')?.value || 'orders';
            const sourceRows = chartType === 'orders' ? (orderCountRows.length ? orderCountRows : revenueRows) : revenueRows;
            const labels = sourceRows.map(item => item.label);
            const values = sourceRows.map(item => chartType === 'revenue' ? item.revenue : item.orders);

            if (ordersChart) ordersChart.destroy();

            ordersChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: chartType === 'revenue' ? 'Pendapatan' : 'Jumlah Pesanan',
                        data: values,
                        backgroundColor: '#2563eb',
                        borderRadius: 4,
                        maxBarThickness: 22
                    }]
                },
                options: defaultChartOptions(chartType === 'revenue' ? 'revenue' : 'orders')
            });
        }

        function renderPaymentChart() {
            const canvas = document.getElementById('paymentChart');
            if (!canvas || !window.Chart) return;

            const methods = normalizePaymentMethods();
            const ctx = canvas.getContext('2d');

            if (paymentChart) paymentChart.destroy();

            paymentChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: methods.map(item => item.label),
                    datasets: [{
                        data: methods.map(item => item.value),
                        backgroundColor: paymentColors.slice(0, methods.length),
                        borderWidth: 0,
                        cutout: '66%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => `${ctx.label}: ${formatMoney(ctx.raw)}`
                            }
                        }
                    }
                }
            });

            document.getElementById('payment-total-center').textContent = formatMoney(sumBy(methods, 'value'));
            renderPaymentLegend(methods);
        }

        function normalizePaymentMethods() {
            const methodsMap = new Map();

            completedOrders.forEach(order => {
                const label = getOrderPaymentMethod(order);
                const value = getOrderTotal(order);
                methodsMap.set(label, (methodsMap.get(label) || 0) + value);
            });

            let methods = Array.from(methodsMap.entries()).map(([label, value]) => ({ label, value }));

            // Fallback kalau data pesanan kosong tetapi backend /admin/pendapatan sudah mengirim metode pembayaran.
            if (methods.length === 0) {
                const raw = firstFilledValue([
                    currentData?.payment_methods,
                    currentData?.metode_pembayaran,
                    currentData?.payments,
                    currentData?.summary?.payment_methods
                ]) || [];

                if (Array.isArray(raw)) {
                    methods = raw.map(item => ({
                        label: item.label || item.metode || item.method || item.nama || item.name || 'Lainnya',
                        value: Number(item.value ?? item.total ?? item.revenue ?? item.jumlah ?? item.nominal ?? 0)
                    }));
                } else if (raw && typeof raw === 'object') {
                    methods = Object.entries(raw).map(([label, value]) => ({ label, value: Number(value || 0) }));
                }
            }

            methods = methods.filter(item => Number(item.value || 0) > 0);

            if (methods.length === 0) {
                methods = [{ label: 'Belum ada pembayaran', value: 0 }];
            }

            return methods;
        }

        function renderPaymentLegend(methods) {
            const wrapper = document.getElementById('payment-legend');
            const total = sumBy(methods, 'value');

            wrapper.innerHTML = methods.map((item, index) => {
                const percent = total > 0 ? Math.round((item.value / total) * 100) : 0;
                return `
                                        <div class="payment-legend-item">
                                            <span class="legend-dot" style="background:${paymentColors[index % paymentColors.length]};"></span>
                                            <span>
                                                <strong>${escapeHtml(item.label)}</strong><br>
                                                <span class="legend-value">${formatMoney(item.value)} (${percent}%)</span>
                                            </span>
                                        </div>
                                    `;
            }).join('');
        }

        function defaultChartOptions(type = 'revenue') {
            return {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => type === 'orders'
                                ? `Jumlah: ${formatNumber(ctx.raw)}`
                                : `Pendapatan: ${formatMoney(ctx.raw)}`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#64748b',
                            font: { size: 9.5 },
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: currentPeriod === 'tahunan' ? 7 : 6
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(148, 163, 184, 0.18)' },
                        ticks: {
                            color: '#64748b',
                            font: { size: 9.5 },
                            precision: 0,
                            callback: value => type === 'orders' ? value : compactMoney(value)
                        }
                    }
                }
            };
        }

        function getOrderRowsForTable() {
            let rows = [];
            if (Array.isArray(completedOrders)) rows.push(...completedOrders);
            if (Array.isArray(completedServis)) rows.push(...completedServis);

            // Apply search input
            const searchVal = (document.getElementById('filter-search')?.value || '').toLowerCase().trim();
            if (searchVal) {
                rows = rows.filter(order => {
                    const name = getOrderCustomerName(order).toLowerCase();
                    const phone = getOrderCustomerPhone(order).toLowerCase();
                    return name.includes(searchVal) || phone.includes(searchVal);
                });
            }

            // Apply type filter
            const typeVal = document.getElementById('filter-type')?.value || 'all';
            if (typeVal === 'pesanan') {
                rows = rows.filter(order => !order._is_servis);
            } else if (typeVal === 'servis') {
                rows = rows.filter(order => order._is_servis);
            }

            // Apply damage type (kerusakan) filter
            const kerusakanVal = document.getElementById('filter-kerusakan')?.value || 'all';
            if (kerusakanVal !== 'all') {
                rows = rows.filter(order => {
                    if (!order._is_servis) return false;
                    const kendala = order.jenis_kerusakan || order.kendala || '';
                    return kendala === kerusakanVal;
                });
            }

            return rows.sort((a, b) => {
                const ad = new Date(getOrderRevenueDateValue(a) || getOrderDateValue(a) || 0).getTime() || 0;
                const bd = new Date(getOrderRevenueDateValue(b) || getOrderDateValue(b) || 0).getTime() || 0;
                return bd - ad;
            });
        }

        function setDetailTableVisibleRows(count) {
            const wrapper = document.getElementById('table-wrapper');
            if (!wrapper) return;

            const rows = Math.max(4, Number(count || rowsPerPage || 4));
            wrapper.style.setProperty('--detail-visible-rows', rows);
            wrapper.dataset.visibleRows = String(rows);
        }

        function getRawOrderId(order = {}) {
            return firstFilledValue([
                order.kode_servis,
                order.id_servis,
                order.id_pesanan,
                order.id_order,
                order.order_id,
                order.kode_pesanan,
                order.kode_order,
                order.id
            ]) || '—';
        }

        function formatOrderIdLikeKelolaPesanan(order = {}) {
            const rawId = String(getRawOrderId(order));
            if (!rawId || rawId === '—') return { prefix: '#', code: '—', full: '#—' };

            if (rawId.toUpperCase().includes('PP-') || rawId.toUpperCase().includes('SRV-')) {
                const clean = rawId.replace(/^#/, '');
                const parts = clean.split('-');
                return {
                    prefix: `#${parts[0]}-`,
                    code: parts.slice(1).join('-') || clean,
                    full: `#${clean}`
                };
            }

            const dateValue = getOrderDateValue(order) || getOrderRevenueDateValue(order) || new Date();
            const dObj = new Date(dateValue);
            if (isNaN(dObj.getTime())) {
                return { prefix: '#', code: rawId, full: `#${rawId}` };
            }

            const pad = n => String(n).padStart(2, '0');
            const yy = String(dObj.getFullYear()).slice(-2);
            const mm = pad(dObj.getMonth() + 1);
            const dd = pad(dObj.getDate());
            const code = `${yy}${mm}${dd}-${rawId}`;

            return {
                prefix: '#PP-',
                code,
                full: `#PP-${code}`
            };
        }

        function renderTable() {
            const tbody = document.getElementById('revenue-tbody');
            tbody.innerHTML = '';

            const rows = getOrderRowsForTable();
            const start = (tablePage - 1) * rowsPerPage;
            const pageRows = rows.slice(start, start + rowsPerPage);

            setDetailTableVisibleRows(pageRows.length || rowsPerPage || 4);

            pageRows.forEach(order => {
                const date = getOrderDateParts(order);
                const isServis = order._is_servis;
                let productDesc = '';
                if (isServis) {
                    const kendala = order.jenis_kerusakan || order.kendala || 'Servis Kendaraan';
                    productDesc = `<div class="order-product-summary">
                                        <span class="order-product-name" title="${escapeHtml(kendala)}">${escapeHtml(kendala)}</span>
                                        <span class="order-item-badge" style="background:rgba(245,158,11,0.1);color:#d97706;">1 Layanan</span>
                                    </div>`;
                } else {
                    const product = getOrderProductSummary(order);
                    productDesc = `<div class="order-product-summary">
                                        <span class="order-product-name" title="${escapeHtml(product.name)}">${escapeHtml(product.name)}</span>
                                        <span class="order-item-badge">${formatNumber(product.items || 0)} Item</span>
                                    </div>`;
                }
                const statusClass = String(order.status || 'selesai').toLowerCase();
                const total = getOrderTotal(order);
                const paymentTime = formatDateTime(getOrderPaymentDateValue(order));
                const orderId = formatOrderIdLikeKelolaPesanan(order);

                const typeBadge = isServis
                    ? `<span class="order-status-badge" style="background:rgba(245,158,11,0.1);color:#d97706;">Servis</span>`
                    : `<span class="order-status-badge" style="background:rgba(37,99,235,0.1);color:#2563eb;">Pesanan</span>`;

                const tr = document.createElement('tr');
                tr.innerHTML = `
                                        <td class="td-id">
                                            <span class="order-id-text" title="${escapeHtml(orderId.full)}">
                                                <span class="order-id-prefix">${escapeHtml(orderId.prefix)}</span>
                                                <span class="order-id-code-line">${escapeHtml(orderId.code)}</span>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="order-main-text">${escapeHtml(date.date)}</span>
                                            <span class="order-sub-text">${escapeHtml(date.time)}</span>
                                        </td>
                                        <td class="td-customer">
                                            <span class="order-main-text text-truncate" title="${escapeHtml(getOrderCustomerName(order))}">${escapeHtml(getOrderCustomerName(order))}</span>
                                            <span class="order-sub-text text-truncate" title="${escapeHtml(getOrderCustomerPhone(order))}">${escapeHtml(getOrderCustomerPhone(order))}</span>
                                        </td>
                                        <td>${typeBadge}</td>
                                        <td class="td-product">${productDesc}</td>
                                        <td class="td-money total-value-cell">${formatMoney(total)}</td>
                                        <td class="td-payment">
                                            <span class="order-main-text text-truncate" title="${escapeHtml(getOrderPaymentMethod(order))}">${escapeHtml(getOrderPaymentMethod(order))}</span>
                                            <span class="order-sub-text text-truncate" title="${escapeHtml(paymentTime)}">${escapeHtml(paymentTime)}</span>
                                        </td>
                                        <td style="text-align:center;">
                                            <span class="order-status-badge ${escapeHtml(statusClass)}">${escapeHtml(getOrderStatusLabel(order))}</span>
                                        </td>
                                    `;
                tbody.appendChild(tr);
            });
        }

        function renderPagination() {
            const wrapper = document.getElementById('pagination-wrapper');
            const rows = getOrderRowsForTable();
            const totalRows = rows.length;

            if (totalRows === 0) {
                wrapper.innerHTML = '';
                return;
            }

            const totalPages = Math.max(1, Math.ceil(totalRows / rowsPerPage));
            if (tablePage > totalPages) tablePage = totalPages;

            const startItem = ((tablePage - 1) * rowsPerPage) + 1;
            const endItem = Math.min(tablePage * rowsPerPage, totalRows);

            let buttons = '';
            buttons += `<button class="page-btn" ${tablePage === 1 ? 'disabled' : ''} onclick="goTablePage(${tablePage - 1})"><i data-lucide="chevron-left" class="icon-xs"></i></button>`;

            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || Math.abs(i - tablePage) <= 1) {
                    buttons += `<button class="page-btn ${i === tablePage ? 'active' : ''}" onclick="goTablePage(${i})">${i}</button>`;
                } else if (Math.abs(i - tablePage) === 2) {
                    buttons += `<span style="color:#94a3b8;font-size:12px;">...</span>`;
                }
            }

            buttons += `<button class="page-btn" ${tablePage === totalPages ? 'disabled' : ''} onclick="goTablePage(${tablePage + 1})"><i data-lucide="chevron-right" class="icon-xs"></i></button>`;

            wrapper.innerHTML = `
                                    <div class="pagination-info">Menampilkan ${startItem}-${endItem} dari ${totalRows} data</div>
                                    <div class="pagination-actions">
                                        <div class="page-buttons">${buttons}</div>
                                        <select class="rows-per-page" onchange="changeRowsPerPage(this.value)">
                                            <option value="4" ${rowsPerPage == 4 ? 'selected' : ''}>4 / halaman</option>
                                            <option value="10" ${rowsPerPage == 10 ? 'selected' : ''}>10 / halaman</option>
                                            <option value="25" ${rowsPerPage == 25 ? 'selected' : ''}>25 / halaman</option>
                                            <option value="50" ${rowsPerPage == 50 ? 'selected' : ''}>50 / halaman</option>
                                        </select>
                                    </div>
                                `;

            if (typeof lucide !== 'undefined') lucide.createIcons();
        }

        function handleFilterChange() {
            tablePage = 1;
            renderTable();
            renderPagination();
        }

        function handleFilterTypeChange() {
            const typeVal = document.getElementById('filter-type')?.value || 'all';
            const kerusakanWrapper = document.getElementById('filter-kerusakan-wrapper');
            if (kerusakanWrapper) {
                if (typeVal === 'pesanan') {
                    kerusakanWrapper.style.display = 'none';
                    const kerusakanSelect = document.getElementById('filter-kerusakan');
                    if (kerusakanSelect) kerusakanSelect.value = 'all';
                } else {
                    kerusakanWrapper.style.display = 'block';
                }
            }
            handleFilterChange();
        }

        function populateKerusakanDropdown() {
            const select = document.getElementById('filter-kerusakan');
            if (!select) return;

            const selectedVal = select.value;

            // Keep the default "Semua Kerusakan" option
            select.innerHTML = '<option value="all">Semua Kerusakan</option>';

            // Extract unique values
            const uniqueKerusakan = new Set();
            if (Array.isArray(completedServis)) {
                completedServis.forEach(s => {
                    const k = s.jenis_kerusakan || s.kendala;
                    if (k && String(k).trim() !== '') {
                        uniqueKerusakan.add(String(k).trim());
                    }
                });
            }

            // Append each unique kerusakan as an option
            Array.from(uniqueKerusakan).sort().forEach(k => {
                const opt = document.createElement('option');
                opt.value = k;
                opt.textContent = k;
                select.appendChild(opt);
            });

            // Restore selection if it still exists
            if (uniqueKerusakan.has(selectedVal)) {
                select.value = selectedVal;
            } else {
                select.value = 'all';
            }
        }

        function goTablePage(page) {
            const totalRows = getOrderRowsForTable().length;
            const totalPages = Math.max(1, Math.ceil(totalRows / rowsPerPage));

            tablePage = Math.min(Math.max(1, page), totalPages);

            renderTable();
            renderPagination();
        }

        function changeRowsPerPage(value) {
            rowsPerPage = parseInt(value, 10) || 4;
            tablePage = 1;

            setDetailTableVisibleRows(rowsPerPage);
            renderTable();
            renderPagination();
        }

        function getOrderDateParts(order = {}) {
            const raw = getOrderDateValue(order);
            const obj = raw ? new Date(raw) : null;

            if (!obj || isNaN(obj.getTime())) return { date: '—', time: '—' };

            return {
                date: obj.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }),
                time: obj.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB'
            };
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

        function getOrderCustomerName(order = {}) {
            return firstFilledValue([
                order.pengguna?.nama,
                order.pengguna?.name,
                order.nama_pelanggan,
                order.customer_name,
                order.nama
            ]) || '—';
        }

        function getOrderCustomerPhone(order = {}) {
            return firstFilledValue([
                order.pengguna?.no_hp,
                order.pengguna?.phone,
                order.no_hp,
                order.telepon,
                order.phone
            ]) || '—';
        }

        function getOrderProductSummary(order = {}) {
            if (order._is_servis) {
                const kendala = order.jenis_kerusakan || order.kendala || 'Servis Laptop';
                const merek = order.merek_laptop ? ` (${order.merek_laptop})` : '';
                return {
                    name: kendala + merek,
                    items: 1
                };
            }

            const detail = order.detail || order.details || order.items || order.produk || [];

            if (!Array.isArray(detail) || detail.length === 0) {
                return {
                    name: firstFilledValue([order.nama_produk, order.produk_nama, order.product_name]) || 'Produk tidak tersedia',
                    items: Number(order.total_item || order.total_items || order.jumlah_item || 0) || 0
                };
            }

            const first = detail[0] || {};
            const produk = first.produk || first.product || {};
            const name = firstFilledValue([
                produk.nama_produk,
                produk.nama,
                produk.name,
                first.nama_produk,
                first.nama,
                first.product_name
            ]) || 'Produk Dihapus';

            return {
                name,
                items: getOrderQty(order)
            };
        }

        function getOrderPaymentMethod(order = {}) {
            return firstFilledValue([
                order.metode_pembayaran,
                order.payment_method,
                order.payment,
                order.bank
            ]) || 'Lainnya';
        }

        function getOrderStatusLabel(order = {}) {
            const status = String(order.status || 'selesai').toLowerCase();
            const labels = {
                pending: 'Pending',
                diproses: 'Diproses',
                dikirim: 'Dikirim',
                selesai: 'Selesai',
                completed: 'Selesai',
                success: 'Selesai',
                paid: 'Selesai',
                dibatalkan: 'Dibatalkan'
            };

            return labels[status] || status.charAt(0).toUpperCase() + status.slice(1);
        }

        function exportData() {
            const rows = getOrderRowsForTable();

            if (!rows.length) {
                showToast('Tidak ada data untuk diekspor.', 'error');
                return;
            }

            const header = ['ID Pesanan', 'Tanggal', 'Pelanggan', 'No HP', 'Produk', 'Qty', 'Total', 'Pembayaran', 'Status'];
            const csvRows = rows.map(order => {
                const date = getOrderDateParts(order);
                const product = getOrderProductSummary(order);
                const orderId = firstFilledValue([order.id_pesanan, order.id_order, order.order_id, order.id]) || '—';

                return [
                    orderId,
                    `${date.date} ${date.time}`,
                    getOrderCustomerName(order),
                    getOrderCustomerPhone(order),
                    product.name,
                    product.items,
                    getOrderTotal(order),
                    getOrderPaymentMethod(order),
                    getOrderStatusLabel(order)
                ];
            });

            const csv = [header, ...csvRows]
                .map(row => row.map(cell => `"${String(cell ?? '').replace(/"/g, '""')}"`).join(','))
                .join('\n');

            const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');

            const range = getPeriodDateRange(currentPeriod);
            a.href = url;
            a.download = `laporan_pendapatan_${currentPeriod}_${range.start}_sd_${range.end}.csv`;

            document.body.appendChild(a);
            a.click();
            a.remove();

            URL.revokeObjectURL(url);

            showToast('Data pendapatan berhasil diekspor.', 'success');
        }

        function sumBy(items = [], key) {
            return items.reduce((total, item) => total + Number(item[key] || 0), 0);
        }

        function firstFilledValue(values = []) {
            return values.find(value => value !== undefined && value !== null && String(value).trim() !== '');
        }

        function formatNumber(value) {
            return new Intl.NumberFormat('id-ID').format(Number(value || 0));
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

        function compactMoney(value) {
            value = Number(value || 0);

            if (value >= 1000000000) return `Rp ${Math.round(value / 1000000000)}M`;
            if (value >= 1000000) return `Rp ${Math.round(value / 1000000)}jt`;
            if (value >= 1000) return `Rp ${Math.round(value / 1000)}rb`;

            return `Rp ${value}`;
        }

        function formatShortDate(date) {
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }
    </script>
@endpush