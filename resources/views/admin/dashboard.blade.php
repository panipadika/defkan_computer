@extends('layouts.admin')

@section('title', 'Admin Dashboard — Defkan Computer')
@section('admin-title', 'Dashboard')
@section('tab-dashboard', 'active')

@section('content')
<div class="dashboard-wrapper">


    {{-- Stats Cards Grid (6 Columns) --}}
    <div class="dashboard-stats-grid">
        {{-- Card 1: Total Produk --}}
        <a href="/admin/produk" class="dashboard-stat-card">
            <div class="stat-card-header">
                <div class="stat-icon-container bg-light-blue">
                    <i data-lucide="package" class="icon text-primary"></i>
                </div>
            </div>
            <div class="stat-card-body">
                <h3 id="stat-produk-val" class="stat-value">—</h3>
                <span class="stat-label">Total Produk</span>
            </div>
            <div class="stat-card-footer">
                <span id="stat-produk-trend" class="stat-trend trend-up">
                    <i data-lucide="arrow-up" class="icon icon-xs"></i> 0% dari bulan lalu
                </span>
            </div>
        </a>

        {{-- Card 2: Total Pelanggan --}}
        <a href="/admin/pelanggan" class="dashboard-stat-card">
            <div class="stat-card-header">
                <div class="stat-icon-container bg-light-purple">
                    <i data-lucide="users" class="icon text-secondary"></i>
                </div>
            </div>
            <div class="stat-card-body">
                <h3 id="stat-pengguna-val" class="stat-value">—</h3>
                <span class="stat-label">Total Pelanggan</span>
            </div>
            <div class="stat-card-footer">
                <span id="stat-pengguna-trend" class="stat-trend trend-up">
                    <i data-lucide="arrow-up" class="icon icon-xs"></i> 0% dari bulan lalu
                </span>
            </div>
        </a>

        {{-- Card 3: Total Pesanan --}}
        <a href="/admin/pesanan" class="dashboard-stat-card">
            <div class="stat-card-header">
                <div class="stat-icon-container bg-light-green">
                    <i data-lucide="shopping-cart" class="icon text-success"></i>
                </div>
            </div>
            <div class="stat-card-body">
                <h3 id="stat-pesanan-val" class="stat-value">—</h3>
                <span class="stat-label">Total Pesanan</span>
            </div>
            <div class="stat-card-footer">
                <span id="stat-pesanan-trend" class="stat-trend trend-up">
                    <i data-lucide="arrow-up" class="icon icon-xs"></i> 0% dari bulan lalu
                </span>
            </div>
        </a>

        {{-- Card 4: Perlu Verifikasi --}}
        <a href="/admin/pesanan" class="dashboard-stat-card">
            <div class="stat-card-header">
                <div class="stat-icon-container bg-light-orange">
                    <i data-lucide="shield-alert" class="icon text-warning"></i>
                </div>
            </div>
            <div class="stat-card-body">
                <h3 id="stat-pending-val" class="stat-value">—</h3>
                <span class="stat-label">Perlu Verifikasi</span>
            </div>
            <div class="stat-card-footer">
                <span id="stat-pending-trend" class="stat-trend trend-muted">Semua aman</span>
            </div>
        </a>

        {{-- Card 5: Servis Aktif --}}
        <a href="/admin/servis" class="dashboard-stat-card">
            <div class="stat-card-header">
                <div class="stat-icon-container bg-light-cyan">
                    <i data-lucide="wrench" class="icon text-accent"></i>
                </div>
            </div>
            <div class="stat-card-body">
                <h3 id="stat-servis-val" class="stat-value">—</h3>
                <span class="stat-label">Servis Aktif</span>
            </div>
            <div class="stat-card-footer">
                <span id="stat-servis-trend" class="stat-trend trend-muted">0 dari kemarin</span>
            </div>
        </a>

        {{-- Card 6: Pendapatan Bulan Ini --}}
        <a href="/admin/pendapatan" class="dashboard-stat-card">
            <div class="stat-card-header">
                <div class="stat-icon-container bg-light-success">
                    <i data-lucide="banknote" class="icon text-success"></i>
                </div>
            </div>
            <div class="stat-card-body">
                <h3 id="stat-revenue-val" class="stat-value val-revenue">—</h3>
                <span class="stat-label">Pendapatan Bulan Ini</span>
            </div>
            <div class="stat-card-footer">
                <span id="stat-revenue-trend" class="stat-trend trend-up">
                    <i data-lucide="arrow-up" class="icon icon-xs"></i> 0% dari bulan lalu
                </span>
            </div>
        </a>
    </div>

    {{-- Row 2: Charts & Top Products (3 Columns) --}}
    <div class="dashboard-charts-grid">
        {{-- Column 1: Penjualan Mingguan --}}
        <div class="glass-panel chart-panel flex-grow-12">
            <div class="panel-header-row">
                <h2 class="panel-title">
                    <i data-lucide="trending-up" class="icon text-primary"></i> Penjualan Mingguan
                </h2>
                <select id="sales-period-select" class="custom-badge-select" onchange="loadDashboardStats()">
                    <option value="7">7 Hari Terakhir</option>
                    <option value="30">30 Hari Terakhir</option>
                    <option value="90">90 Hari Terakhir</option>
                </select>
            </div>
            <div class="chart-container" style="height: 110px; position: relative; margin-top: 8px;">
                <canvas id="weekly-sales-chart"></canvas>
            </div>
            <div class="chart-footer-metrics">
                <div class="metric-box">
                    <span class="metric-lbl">Total Penjualan</span>
                    <h4 id="weekly-total-val" class="metric-val">Rp 0</h4>
                </div>
                <div class="metric-box">
                    <span class="metric-lbl">Rata-rata Per Hari</span>
                    <h4 id="weekly-avg-val" class="metric-val">Rp 0</h4>
                </div>
            </div>
        </div>

        {{-- Column 2: Status Servis --}}
        <div class="glass-panel chart-panel">
            <div class="panel-header-row">
                <h2 class="panel-title">
                    <i data-lucide="pie-chart" class="icon text-secondary"></i> Status Servis
                </h2>
            </div>
            <div class="donut-section-wrapper">
                <div class="donut-chart-box">
                    <canvas id="service-status-chart"></canvas>
                    <div class="donut-center-text">
                        <span class="donut-lbl">Total</span>
                        <h4 id="donut-total-val" class="donut-val">0</h4>
                    </div>
                </div>
                <div class="donut-labels-list">
                    <div class="donut-label-item">
                        <span class="dot bg-blue"></span>
                        <span class="lbl-name">Dikerjakan</span>
                        <span id="donut-count-dikerjakan" class="lbl-val">0 (0%)</span>
                    </div>
                    <div class="donut-label-item">
                        <span class="dot bg-orange"></span>
                        <span class="lbl-name">Menunggu</span>
                        <span id="donut-count-menunggu" class="lbl-val">0 (0%)</span>
                    </div>
                    <div class="donut-label-item">
                        <span class="dot bg-green"></span>
                        <span class="lbl-name">Selesai</span>
                        <span id="donut-count-selesai" class="lbl-val">0 (0%)</span>
                    </div>
                    <div class="donut-label-item">
                        <span class="dot bg-muted"></span>
                        <span class="lbl-name">Diambil</span>
                        <span id="donut-count-diambil" class="lbl-val">0 (0%)</span>
                    </div>
                </div>
            </div>
            <div class="panel-footer-action">
                <a href="/admin/servis" class="footer-action-link">Lihat Detail Servis <i data-lucide="arrow-right" class="icon icon-xs"></i></a>
            </div>
        </div>

        {{-- Column 3: Produk Terlaris --}}
        <div class="glass-panel chart-panel">
            <div class="panel-header-row">
                <h2 class="panel-title">
                    <i data-lucide="zap" class="icon text-warning"></i> Produk Terlaris
                </h2>
                <select id="top-products-filter-select" class="custom-badge-select" onchange="loadDashboardStats()">
                    <option value="bulan_ini">Bulan Ini</option>
                    <option value="tahun_ini">Tahun Ini</option>
                    <option value="semua">Semua</option>
                </select>
            </div>
            <div class="top-products-list" id="top-products-container">
                {{-- Dynamic list --}}
                <div class="flex-center" style="padding: 40px;"><div class="loader"></div></div>
            </div>
            <div class="panel-footer-action">
                <a href="/admin/produk" class="footer-action-link">Lihat Semua Produk <i data-lucide="arrow-right" class="icon icon-xs"></i></a>
            </div>
        </div>
    </div>

    {{-- Row 3: Tables & Actions (3 Columns) --}}
    <div class="dashboard-tables-grid">
        {{-- Column 1: Pesanan Terbaru --}}
        <div class="glass-panel table-panel flex-grow-12">
            <div class="panel-header-row" style="margin-bottom: 16px;">
                <h2 class="panel-title">
                    <i data-lucide="shopping-cart" class="icon text-success"></i> Pesanan Terbaru
                </h2>
                <a href="/admin/pesanan" class="header-action-link">Lihat Semua <i data-lucide="arrow-right" class="icon icon-xs"></i></a>
            </div>
            <div class="table-responsive" id="pesanan-list-container">
                {{-- Table loaded via JS --}}
                <div class="flex-center" style="padding: 40px;"><div class="loader"></div></div>
            </div>
        </div>

        {{-- Column 2: Servis Terbaru --}}
        <div class="glass-panel table-panel">
            <div class="panel-header-row" style="margin-bottom: 16px;">
                <h2 class="panel-title">
                    <i data-lucide="wrench" class="icon text-accent"></i> Servis Terbaru
                </h2>
                <a href="/admin/servis" class="header-action-link">Lihat Semua <i data-lucide="arrow-right" class="icon icon-xs"></i></a>
            </div>
            <div class="table-responsive" id="servis-list-container">
                {{-- Table loaded via JS --}}
                <div class="flex-center" style="padding: 40px;"><div class="loader"></div></div>
            </div>
        </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Dashboard wrapper and typography overrides */
    .dashboard-wrapper {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    .dashboard-header-row {
        margin-bottom: 4px;
    }
    .dashboard-main-title {
        font-size: 24px;
        font-weight: 800;
        letter-spacing: -0.03em;
        color: var(--text-main);
        margin: 0 0 4px 0;
    }
    .dashboard-subtitle {
        font-size: 13.5px;
        color: var(--text-muted);
        margin: 0;
    }

    /* Stats Grid (6 columns) */
    .dashboard-stats-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 12px;
    }
    .dashboard-stat-card {
        background: var(--bg-surface);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-md);
        padding: 8px 12px;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        gap: 6px;
        transition: var(--transition);
        box-shadow: var(--shadow-xs);
    }
    .dashboard-stat-card:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
        border-color: rgba(37, 99, 235, 0.2);
    }
    .stat-icon-container {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .stat-icon-container .icon {
        width: 15px;
        height: 15px;
    }
    .stat-value {
        font-size: 18px;
        font-weight: 800;
        letter-spacing: -0.03em;
        color: var(--text-main);
        margin: 0;
        line-height: 1.1;
    }
    .stat-value.val-revenue {
        font-size: 13.5px;
    }
    .stat-label {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 600;
        display: block;
        margin-top: 1px;
    }
    .stat-trend {
        font-size: 11px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }
    .trend-up {
        color: var(--success);
    }
    .trend-down {
        color: var(--danger);
    }
    .trend-muted {
        color: var(--text-subtle);
    }

    /* Soft Icon backgrounds */
    .bg-light-blue { background-color: rgba(37, 99, 235, 0.08); }
    .bg-light-purple { background-color: rgba(124, 58, 237, 0.08); }
    .bg-light-green { background-color: rgba(16, 185, 129, 0.08); }
    .bg-light-orange { background-color: rgba(245, 158, 11, 0.08); }
    .bg-light-cyan { background-color: rgba(6, 182, 212, 0.08); }
    .bg-light-success { background-color: rgba(5, 150, 105, 0.08); }

    /* Rows layouts */
    .dashboard-charts-grid {
        display: grid;
        grid-template-columns: 1.3fr 1fr 1fr;
        gap: 20px;
        width: 100%;
    }
    .dashboard-tables-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        width: 100%;
    }
    .flex-grow-12 {
        grid-column: span 1;
        min-width: 0;
    }

    /* Glass Panels */
    .glass-panel {
        background: var(--bg-surface);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-md);
        padding: 12px 14px;
        box-shadow: var(--shadow-xs);
        display: flex;
        flex-direction: column;
        min-width: 0; /* Prevents layout expansion from Chart.js canvas */
    }
    .chart-panel, .table-panel {
        min-height: 236px;
        height: 236px;
    }
    .panel-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .panel-title {
        font-size: 13px;
        font-weight: 750;
        color: var(--text-main);
        margin: 0;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .panel-title .icon {
        width: 18px;
        height: 18px;
    }

    select.custom-badge-select {
        appearance: none;
        -webkit-appearance: none;
        background: var(--bg-surface-alt);
        border: 1px solid var(--glass-border);
        padding: 4px 24px 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        cursor: pointer;
        outline: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b' stroke-width='3'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 8px center;
        background-size: 9px;
    }
    select.custom-badge-select::-ms-expand {
        display: none;
    }

    /* Metrics for line chart */
    .chart-footer-metrics {
        display: flex;
        gap: 16px;
        margin-top: 8px;
        border-top: 1px solid var(--glass-border);
        padding-top: 6px;
    }
    .metric-box {
        flex: 1;
    }
    .metric-lbl {
        font-size: 10px;
        color: var(--text-muted);
        font-weight: 500;
        display: block;
        margin-bottom: 0px;
    }
    .metric-val {
        font-size: 13px;
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
    }

    /* Donut chart layout */
    .donut-section-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 8px;
        flex: 1;
    }
    .donut-chart-box {
        position: relative;
        width: 90px;
        height: 90px;
        flex-shrink: 0;
    }
    .donut-center-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        pointer-events: none;
    }
    .donut-lbl {
        font-size: 9px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-subtle);
        font-weight: 700;
        display: block;
    }
    .donut-val {
        font-size: 15px;
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
        line-height: 1;
    }
    .donut-labels-list {
        display: flex;
        flex-direction: column;
        gap: 4px;
        flex: 1;
    }
    .donut-label-item {
        display: flex;
        align-items: center;
        font-size: 11.5px;
        font-weight: 600;
        color: var(--text-muted);
    }
    .donut-label-item .dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        margin-right: 8px;
        flex-shrink: 0;
    }
    .donut-label-item .dot.bg-blue { background-color: #2563eb; }
    .donut-label-item .dot.bg-orange { background-color: #f59e0b; }
    .donut-label-item .dot.bg-green { background-color: #10b981; }
    .donut-label-item .dot.bg-muted { background-color: #94a3b8; }
    .donut-label-item .lbl-name {
        flex: 1;
    }
    .donut-label-item .lbl-val {
        font-weight: 700;
        color: var(--text-main);
        margin-left: 8px;
    }

    .panel-footer-action {
        margin-top: auto;
        padding-top: 12px;
        border-top: 1px dashed var(--glass-border);
        display: flex;
        justify-content: flex-end;
    }
    .footer-action-link {
        font-size: 11.5px;
        font-weight: 700;
        color: var(--primary);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: var(--transition);
    }
    .footer-action-link:hover {
        color: var(--primary-hover);
        transform: translateX(2px);
    }

    /* Top Products Item list */
    .top-products-list {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-top: 8px;
        flex: 1;
    }
    .top-product-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .top-product-rank {
        font-size: 11.5px;
        font-weight: 800;
        color: var(--text-muted);
        width: 12px;
        text-align: center;
    }
    .top-product-img {
        width: 26px;
        height: 26px;
        border-radius: var(--radius-sm);
        object-fit: cover;
        border: 1px solid var(--glass-border);
        background: var(--bg-surface-alt);
    }
    .top-product-info {
        flex: 1;
        min-width: 0;
    }
    .top-product-name {
        font-size: 11.5px;
        font-weight: 750;
        color: var(--text-main);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin: 0;
    }
    .top-product-sold {
        font-size: 10.5px;
        font-weight: 700;
        color: var(--text-muted);
        white-space: nowrap;
        margin-left: 8px;
    }

    /* Tables styling */
    .dashboard-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    .dashboard-table th {
        font-size: 10px;
        font-weight: 750;
        text-transform: uppercase;
        color: var(--text-subtle);
        letter-spacing: 0.5px;
        padding: 5px 6px;
        border-bottom: 1px solid var(--glass-border);
    }
    .dashboard-table td {
        font-size: 11px;
        padding: 6px 6px;
        border-bottom: 1px solid var(--glass-border);
        color: var(--text-muted);
        white-space: nowrap;
    }
    .dashboard-table tr:last-child td {
        border-bottom: none;
    }
    .dashboard-table td.table-id {
        font-weight: 750;
        color: var(--primary);
    }
    .status-badge {
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 700;
        text-transform: capitalize;
        display: inline-block;
    }
    .status-pending { background: rgba(245, 158, 11, 0.08); color: var(--warning); border: 1px solid rgba(245, 158, 11, 0.15); }
    .status-diproses { background: rgba(37, 99, 235, 0.08); color: var(--primary); border: 1px solid rgba(37, 99, 235, 0.15); }
    .status-dikirim { background: rgba(6, 182, 212, 0.08); color: var(--accent); border: 1px solid rgba(6, 182, 212, 0.15); }
    .status-selesai { background: rgba(16, 185, 129, 0.08); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.15); }
    .status-dibatalkan { background: rgba(220, 38, 38, 0.08); color: var(--danger); border: 1px solid rgba(220, 38, 38, 0.15); }
    .status-menunggu { background: rgba(245, 158, 11, 0.08); color: var(--warning); border: 1px solid rgba(245, 158, 11, 0.15); }
    .status-diperiksa { background: rgba(6, 182, 212, 0.08); color: var(--accent); border: 1px solid rgba(6, 182, 212, 0.15); }
    .status-dikerjakan { background: rgba(124, 58, 237, 0.08); color: var(--secondary); border: 1px solid rgba(124, 58, 237, 0.15); }
    .status-diambil { background: rgba(148, 163, 184, 0.08); color: var(--text-subtle); border: 1px solid rgba(148, 163, 184, 0.15); }

    .header-action-link {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        transition: var(--transition);
    }
    .header-action-link:hover {
        color: var(--primary);
    }

    /* Actions and reminders */
    .dashboard-side-col {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .action-panel, .reminder-panel {
        flex: 1;
    }
    .actions-buttons-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    .action-btn-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: 8px 10px;
        border-radius: var(--radius-sm);
        text-decoration: none;
        font-size: 11px;
        font-weight: 700;
        border: 1px solid transparent;
        transition: var(--transition);
        text-align: center;
    }
    .action-btn-item .icon {
        width: 16px;
        height: 16px;
    }
    
    .action-btn-item.brand-blue {
        border-color: rgba(37, 99, 235, 0.15);
        color: var(--primary);
        background: rgba(37, 99, 235, 0.02);
    }
    .action-btn-item.brand-blue:hover {
        background: rgba(37, 99, 235, 0.08);
        border-color: var(--primary);
    }

    .action-btn-item.brand-green {
        border-color: rgba(16, 185, 129, 0.15);
        color: var(--success);
        background: rgba(16, 185, 129, 0.02);
    }
    .action-btn-item.brand-green:hover {
        background: rgba(16, 185, 129, 0.08);
        border-color: var(--success);
    }

    .action-btn-item.brand-orange {
        border-color: rgba(245, 158, 11, 0.15);
        color: var(--warning);
        background: rgba(245, 158, 11, 0.02);
    }
    .action-btn-item.brand-orange:hover {
        background: rgba(245, 158, 11, 0.08);
        border-color: var(--warning);
    }

    .action-btn-item.brand-purple {
        border-color: rgba(124, 58, 237, 0.15);
        color: var(--secondary);
        background: rgba(124, 58, 237, 0.02);
    }
    .action-btn-item.brand-purple:hover {
        background: rgba(124, 58, 237, 0.08);
        border-color: var(--secondary);
    }

    /* Reminder Items */
    .reminder-items-list {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .reminder-item-card {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 10px;
        border-radius: var(--radius-sm);
        text-decoration: none;
        color: inherit;
        border: 1px solid var(--glass-border);
        background: var(--bg-surface-alt);
        transition: var(--transition);
    }
    .reminder-item-card:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }
    .reminder-item-card.border-orange {
        border-left: 4px solid var(--warning);
    }
    .reminder-item-card.border-blue {
        border-left: 4px solid var(--primary);
    }
    .reminder-icon {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .reminder-icon .icon {
        width: 13px;
        height: 13px;
    }
    .reminder-content {
        flex: 1;
        min-width: 0;
    }
    .reminder-title {
        font-size: 11.5px;
        font-weight: 750;
        color: var(--text-main);
        margin: 0 0 1px 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .reminder-desc {
        font-size: 10px;
        color: var(--text-muted);
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Screen responsives */
    @media (max-width: 1200px) {
        .dashboard-stats-grid {
            grid-template-columns: repeat(3, 1fr);
        }
        .dashboard-charts-grid {
            grid-template-columns: 1fr;
        }
        .dashboard-charts-grid .flex-grow-12 {
            grid-column: span 1;
        }
        .dashboard-tables-grid {
            grid-template-columns: 1fr;
        }
        .dashboard-tables-grid .flex-grow-12 {
            grid-column: span 1;
        }
    }
    @media (max-width: 768px) {
        .dashboard-stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 480px) {
        .dashboard-stats-grid {
            grid-template-columns: 1fr;
        }
    }       }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let weeklyChartInstance = null;
let donutChartInstance = null;

document.addEventListener('DOMContentLoaded', () => {
    if (!isLoggedIn() || !isAdmin()) {
        window.location.href = '/';
        return;
    }
    loadDashboardStats();
    loadPesananTerbaru();
    loadServisTerbaru();
});

async function loadDashboardStats() {
    try {
        const salesFilter = document.getElementById('sales-period-select')?.value || '7';
        const topFilter = document.getElementById('top-products-filter-select')?.value || 'bulan_ini';
        
        const res = await apiFetch(`/admin/dashboard-stats?sales_filter=${salesFilter}&top_products_filter=${topFilter}`);
        if (res.status === 'success' && res.data) {
            const data = res.data;

            // 1. Update Core Stats values & growth labels
            updateStatCard('stat-produk-val', 'stat-produk-trend', data.stats.produk);
            updateStatCard('stat-pengguna-val', 'stat-pengguna-trend', data.stats.pengguna);
            updateStatCard('stat-pesanan-val', 'stat-pesanan-trend', data.stats.pesanan);
            updateStatCard('stat-servis-val', 'stat-servis-trend', data.stats.servis, 'dari kemarin');
            
            // Format revenue rupiah
            const revVal = document.getElementById('stat-revenue-val');
            if (revVal) revVal.textContent = formatRupiah(data.stats.revenue.value);
            updateTrendLabel('stat-revenue-trend', data.stats.revenue);

            // Verifikasi badge
            const verifVal = document.getElementById('stat-pending-val');
            if (verifVal) verifVal.textContent = data.stats.verifikasi.value;
            const verifTrend = document.getElementById('stat-pending-trend');
            if (verifTrend) {
                if (data.stats.verifikasi.value > 0) {
                    verifTrend.className = 'stat-trend trend-down';
                    verifTrend.innerHTML = `<i data-lucide="alert-circle" class="icon icon-xs"></i> Perlu verifikasi`;
                } else {
                    verifTrend.className = 'stat-trend trend-muted';
                    verifTrend.textContent = 'Semua aman';
                }
            }

            // 2. Initialize Line Chart (Penjualan Mingguan)
            renderWeeklySalesChart(data.weekly_sales);

            // 3. Initialize Donut Chart (Status Servis)
            renderServiceStatusChart(data.service_breakdown);

            // 4. Load Top Products
            renderTopProducts(data.top_products);

            // 5. Update Reminders
            updateReminders(data.reminders);

            // Refresh lucide icons
            lucide.createIcons();
        }
    } catch (e) {
        console.error('Gagal memuat statistik dashboard:', e);
    }
}

function updateStatCard(valId, trendId, statData, customText = 'dari bulan lalu') {
    const valEl = document.getElementById(valId);
    if (valEl) valEl.textContent = statData.value;
    updateTrendLabel(trendId, statData, customText);
}

function updateTrendLabel(trendId, statData, customText = 'dari bulan lalu') {
    const trendEl = document.getElementById(trendId);
    if (!trendEl) return;

    const change = statData.change || 0;
    if (change > 0) {
        trendEl.className = 'stat-trend trend-up';
        trendEl.innerHTML = `<i data-lucide="arrow-up" class="icon icon-xs"></i> +${change}% ${customText}`;
    } else if (change < 0) {
        trendEl.className = 'stat-trend trend-down';
        trendEl.innerHTML = `<i data-lucide="arrow-down" class="icon icon-xs"></i> ${change}% ${customText}`;
    } else {
        trendEl.className = 'stat-trend trend-muted';
        if (customText === 'dari kemarin') {
            trendEl.innerHTML = `Sama seperti kemarin`;
        } else {
            trendEl.innerHTML = `Sama dengan bulan lalu`;
        }
    }
}

function renderWeeklySalesChart(salesData) {
    const ctx = document.getElementById('weekly-sales-chart').getContext('2d');
    
    // Set total and daily average text
    document.getElementById('weekly-total-val').textContent = formatRupiah(salesData.total);
    document.getElementById('weekly-avg-val').textContent = formatRupiah(salesData.average);

    const labels = salesData.items.map(item => item.label);
    const dataPoints = salesData.items.map(item => item.revenue);

    const gradient = ctx.createLinearGradient(0, 0, 0, 180);
    gradient.addColorStop(0, 'rgba(37, 99, 235, 0.15)');
    gradient.addColorStop(1, 'rgba(37, 99, 235, 0.00)');

    if (weeklyChartInstance) {
        weeklyChartInstance.destroy();
    }

    weeklyChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                data: dataPoints,
                borderColor: '#2563eb',
                borderWidth: 2.5,
                backgroundColor: gradient,
                fill: true,
                tension: 0.38,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#2563eb',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#2563eb',
                pointHoverBorderColor: '#ffffff',
                pointHoverBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    padding: 10,
                    borderRadius: 6,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return formatRupiah(context.raw);
                        }
                    }
                }
            },
            scales: {
                y: {
                    grid: { borderDash: [4, 4], color: 'rgba(30, 41, 59, 0.06)', drawBorder: false },
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) return (value/1000000) + 'jt';
                            if (value >= 1000) return (value/1000) + 'rb';
                            return value;
                        },
                        color: '#94a3b8',
                        font: { size: 9.5, weight: '600' }
                    }
                },
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: { color: '#94a3b8', font: { size: 9.5, weight: '600' } }
                }
            }
        }
    });
}

function renderServiceStatusChart(breakdownData) {
    const ctx = document.getElementById('service-status-chart').getContext('2d');
    const items = breakdownData.items;

    document.getElementById('donut-total-val').textContent = breakdownData.total;

    // Set side counts
    const totalVal = breakdownData.total || 1;
    const pct = (val) => `${val} (${Math.round((val / totalVal) * 100)}%)`;
    
    document.getElementById('donut-count-dikerjakan').textContent = pct(items.dikerjakan);
    document.getElementById('donut-count-menunggu').textContent = pct(items.menunggu);
    document.getElementById('donut-count-selesai').textContent = pct(items.selesai);
    document.getElementById('donut-count-diambil').textContent = pct(items.diambil);

    if (donutChartInstance) {
        donutChartInstance.destroy();
    }

    donutChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Dikerjakan', 'Menunggu', 'Selesai', 'Diambil'],
            datasets: [{
                data: [items.dikerjakan, items.menunggu, items.selesai, items.diambil],
                backgroundColor: ['#2563eb', '#f59e0b', '#10b981', '#94a3b8'],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 8,
                    borderRadius: 6,
                    callbacks: {
                        label: function(context) {
                            const val = context.raw;
                            const share = Math.round((val / totalVal) * 100);
                            return ` ${context.label}: ${val} unit (${share}%)`;
                        }
                    }
                }
            }
        }
    });
}

function renderTopProducts(products) {
    const container = document.getElementById('top-products-container');
    if (!products || products.length === 0) {
        container.innerHTML = '<p class="text-muted" style="text-align:center; padding: 20px; font-size: 13px;">Belum ada data penjualan.</p>';
        return;
    }

    container.innerHTML = products.slice(0, 3).map((p, idx) => {
        const rank = idx + 1;
        const foto = p.foto_url || 'https://via.placeholder.com/60?text=Laptop';
        return `
            <div class="top-product-item">
                <span class="top-product-rank">${rank}</span>
                <img src="${foto}" class="top-product-img" alt="${p.nama_produk}">
                <div class="top-product-info">
                    <h4 class="top-product-name" title="${p.nama_produk}">${p.nama_produk}</h4>
                </div>
                <span class="top-product-sold">${p.total_qty} unit</span>
            </div>
        `;
    }).join('');
}

function updateReminders(reminders) {
    // Servis Menunggu
    const sCount = reminders.servis_menunggu || 0;
    const sTitle = document.getElementById('reminder-servis-title');
    if (sTitle) {
        sTitle.textContent = `${sCount} servis menunggu dikerjakan`;
    }

    // Pesanan Pending
    const pCount = reminders.pesanan_pending || 0;
    const pTitle = document.getElementById('reminder-pesanan-title');
    if (pTitle) {
        pTitle.textContent = `${pCount} pesanan perlu verifikasi`;
    }
}

async function loadPesananTerbaru() {
    const container = document.getElementById('pesanan-list-container');
    try {
        const res = await apiFetch('/admin/pesanan-terbaru');
        const pesanan = res.data?.data || res.data || [];

        if (pesanan.length === 0) {
            container.innerHTML = '<p class="text-muted" style="text-align:center; padding: 40px; font-size: 13px;">Belum ada pesanan.</p>';
            return;
        }

        container.innerHTML = `
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    ${pesanan.slice(0, 3).map(p => {
                        const date = new Date(p.created_at).toLocaleDateString('id-ID', {day:'numeric', month:'short'});
                        const pelanggan = p.pengguna?.nama || 'Guest';
                        
                        // Formatting custom ID like #PS-250602-01 or Fallback
                        const pad = (n) => String(n).padStart(2, '0');
                        const dObj = new Date(p.created_at);
                        const yy = String(dObj.getFullYear()).slice(-2);
                        const mm = pad(dObj.getMonth() + 1);
                        const dd = pad(dObj.getDate());
                        const formattedId = `#PP-${yy}${mm}${dd}-${p.id_pesanan}`;

                        return `
                            <tr>
                                <td class="table-id">${formattedId}</td>
                                <td style="max-width: 100px; overflow: hidden; text-overflow: ellipsis;" title="${pelanggan}">${pelanggan}</td>
                                <td>${date}</td>
                                <td style="font-weight:700; color:var(--text-main);">${formatRupiah(p.total_harga)}</td>
                                <td><span class="status-badge status-${p.status}">${p.status}</span></td>
                            </tr>
                        `;
                    }).join('')}
                </tbody>
            </table>
        `;
    } catch (e) {
        container.innerHTML = '<p class="text-muted" style="text-align:center; padding: 40px;">Gagal memuat data.</p>';
    }
}

async function loadServisTerbaru() {
    const container = document.getElementById('servis-list-container');
    try {
        const res = await apiFetch('/admin/servis-terbaru');
        const servis = res.data || [];

        if (servis.length === 0) {
            container.innerHTML = '<p class="text-muted" style="text-align:center; padding: 40px; font-size: 13px;">Belum ada servis baru.</p>';
            return;
        }

        container.innerHTML = `
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>ID Servis</th>
                        <th>Produk</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    ${servis.slice(0, 3).map(s => {
                        const date = new Date(s.created_at).toLocaleDateString('id-ID', {day:'numeric', month:'short'});
                        return `
                            <tr>
                                <td class="table-id">${s.kode_servis}</td>
                                <td style="max-width: 110px; overflow: hidden; text-overflow: ellipsis;" title="${s.merek_laptop}">${s.merek_laptop}</td>
                                <td>${date}</td>
                                <td><span class="status-badge status-${s.status}">${s.status}</span></td>
                            </tr>
                        `;
                    }).join('')}
                </tbody>
            </table>
        `;
    } catch (e) {
        container.innerHTML = '<p class="text-muted" style="text-align:center; padding: 40px;">Gagal memuat data.</p>';
    }
}
</script>
@endpush
