@extends('layouts.admin')

@section('title', 'Data Pelanggan — Defkan Computer')
@section('admin-title', 'Pelanggan')
@section('tab-pelanggan', 'active')

@section('content')
    <div class="pelanggan-layout">
        {{-- Stats Row --}}
        <div class="customer-stats-grid">
            <div class="customer-stat-card">
                <div class="customer-stat-icon icon-blue">
                    <i data-lucide="users"></i>
                </div>
                <div class="customer-stat-info">
                    <span class="customer-stat-label">Total Pelanggan</span>
                    <h3 id="stat-total" class="customer-stat-value">—</h3>
                    <span class="customer-stat-subtext" id="stat-total-sub">Semua pelanggan</span>
                </div>
            </div>

            <div class="customer-stat-card">
                <div class="customer-stat-icon icon-green">
                    <i data-lucide="user-check"></i>
                </div>
                <div class="customer-stat-info">
                    <span class="customer-stat-label">Pelanggan Aktif</span>
                    <h3 id="stat-aktif" class="customer-stat-value">—</h3>
                    <span class="customer-stat-subtext">Memiliki aktivitas</span>
                </div>
            </div>

            <div class="customer-stat-card">
                <div class="customer-stat-icon icon-purple">
                    <i data-lucide="user-plus"></i>
                </div>
                <div class="customer-stat-info">
                    <span class="customer-stat-label">Pelanggan Baru Bulan Ini</span>
                    <h3 id="stat-baru" class="customer-stat-value">—</h3>
                    <span class="customer-stat-subtext">Terdaftar bulan ini</span>
                </div>
            </div>

            <div class="customer-stat-card">
                <div class="customer-stat-icon icon-indigo">
                    <i data-lucide="shopping-bag"></i>
                </div>
                <div class="customer-stat-info">
                    <span class="customer-stat-label">Pernah Belanja</span>
                    <h3 id="stat-pembeli" class="customer-stat-value">—</h3>
                    <span class="customer-stat-subtext">Pelanggan transaksi</span>
                </div>
            </div>

            <div class="customer-stat-card">
                <div class="customer-stat-icon icon-orange">
                    <i data-lucide="banknote"></i>
                </div>
                <div class="customer-stat-info">
                    <span class="customer-stat-label">Total Nilai Belanja</span>
                    <h3 id="stat-belanja" class="customer-stat-value stat-money">Rp 0</h3>
                    <span class="customer-stat-subtext">Akumulasi belanja</span>
                </div>
            </div>
        </div>

        {{-- Filter Toolbar --}}
        <div class="glass-panel customer-filter-card">
            <div class="customer-filter-grid">
                <div class="customer-search-wrap">
                    <i data-lucide="search" class="customer-search-icon"></i>
                    <input type="text" id="search-input" class="customer-filter-input"
                        placeholder="Cari nama, email, atau No. HP pelanggan..." oninput="debounceSearch()">
                </div>

                <div class="filter-field">
                    <label>Status Pelanggan</label>
                    <select id="filter-status" class="customer-filter-input customer-select" onchange="applyFilters()">
                        <option value="">Semua</option>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>

                <div class="filter-field">
                    <label>Tanggal Daftar</label>
                    <select id="filter-periode" class="customer-filter-input customer-select" onchange="applyFilters()">
                        <option value="">Semua Periode</option>
                        <option value="today">Hari Ini</option>
                        <option value="week">Minggu Ini</option>
                        <option value="month">Bulan Ini</option>
                        <option value="year">Tahun Ini</option>
                    </select>
                </div>

                <div class="filter-field">
                    <label>Aktivitas Belanja</label>
                    <select id="filter-aktivitas" class="customer-filter-input customer-select" onchange="applyFilters()">
                        <option value="">Semua</option>
                        <option value="pernah-belanja">Pernah Belanja</option>
                        <option value="pernah-servis">Pernah Servis</option>
                        <option value="belum-aktivitas">Belum Ada Aktivitas</option>
                    </select>
                </div>

                <button type="button" class="btn-filter-reset" onclick="resetFilters()">
                    <i data-lucide="rotate-ccw" class="icon-xs"></i>
                    Reset Filter
                </button>

                <button type="button" class="btn-add-customer" onclick="openAddPelangganModal()">
                    <i data-lucide="plus" class="icon-xs"></i>
                    Tambah Pelanggan
                </button>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="glass-panel customer-table-card">
            <div class="customer-table-header">
                <h2>
                    <i data-lucide="users" class="icon-sm"></i>
                    Daftar Pelanggan
                </h2>
            </div>

            {{-- Loading --}}
            <div id="loading-state" class="flex-center customer-loading">
                <div class="loader"></div>
            </div>

            {{-- Empty --}}
            <div id="empty-state" class="customer-empty" style="display: none;">
                <i data-lucide="user-x"></i>
                <p>Tidak ada pelanggan ditemukan.</p>
            </div>

            {{-- Table --}}
            <div id="table-wrapper" class="customer-table-wrapper" style="display: none;">
                <table class="customer-table">
                    <thead>
                        <tr>
                            <th style="width: 4%;">No</th>
                            <th style="width: 18%;">Pelanggan</th>
                            <th style="width: 17%;">Email</th>
                            <th style="width: 13%;">No. HP</th>
                            <th style="width: 10%; text-align:center;">Total Pesanan</th>
                            <th style="width: 10%; text-align:center;">Total Servis</th>
                            <th style="width: 12%; text-align:right;">Total Belanja</th>
                            <th style="width: 8%; text-align:center;">Status</th>
                            <th style="width: 10%;">Tanggal Daftar</th>
                            <th style="width: 13%; text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="pelanggan-tbody"></tbody>
                </table>
            </div>

            <div id="pagination-wrapper" class="customer-pagination-wrapper"></div>
        </div>
    </div>

    {{-- Modal Pelanggan --}}
    <div id="customer-modal" class="customer-modal" onclick="closeCustomerModal(event)">
        <div class="glass-panel customer-modal-box" onclick="event.stopPropagation()">
            <div id="customer-modal-content"></div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .pelanggan-layout {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding-bottom: 12px;
        }

        .glass-panel {
            background: var(--bg-surface, #ffffff);
            border: 1px solid var(--glass-border, #e5eaf3);
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.045);
        }

        /* ===== Stats Cards ===== */
        .customer-stats-grid {
            display: flex;
            align-items: stretch;
            gap: 10px;
            width: 100%;
            overflow-x: auto;
            padding-bottom: 2px;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .customer-stats-grid::-webkit-scrollbar {
            display: none;
        }

        .customer-stat-card {
            flex: 1 1 0;
            min-width: 0;
            min-height: 78px;
            padding: 12px 14px;
            border-radius: 14px;
            background: var(--bg-surface, #ffffff);
            border: 1px solid var(--glass-border, #e5eaf3);
            display: flex;
            align-items: center;
            gap: 11px;
            box-shadow: 0 5px 16px rgba(15, 23, 42, 0.035);
            overflow: hidden;
        }

        .customer-stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .customer-stat-icon svg {
            width: 18px;
            height: 18px;
        }

        .icon-blue {
            background: rgba(37, 99, 235, 0.11);
            color: var(--primary, #2563eb);
        }

        .icon-green {
            background: rgba(16, 185, 129, 0.12);
            color: var(--success, #10b981);
        }

        .icon-purple {
            background: rgba(139, 92, 246, 0.12);
            color: #8b5cf6;
        }

        .icon-indigo {
            background: rgba(79, 70, 229, 0.11);
            color: #4f46e5;
        }

        .icon-orange {
            background: rgba(249, 115, 22, 0.12);
            color: #f97316;
        }

        .customer-stat-info {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 2px;
            flex: 1;
        }

        .customer-stat-label {
            font-size: 10px;
            line-height: 1.2;
            font-weight: 800;
            color: var(--text-muted, #64748b);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .customer-stat-value {
            margin: 0;
            font-size: 21px;
            line-height: 1.08;
            font-weight: 900;
            letter-spacing: -0.02em;
            color: var(--text-main, #0f172a);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .customer-stat-value.stat-money {
            font-size: 17px;
        }

        .customer-stat-subtext {
            font-size: 10px;
            line-height: 1.2;
            color: var(--text-muted, #94a3b8);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .customer-stat-subtext.positive {
            color: var(--success, #10b981);
            font-weight: 700;
        }

        /* ===== Filter ===== */
        .customer-filter-card {
            border-radius: 14px;
            padding: 11px 14px;
        }

        .customer-filter-grid {
            display: grid;
            grid-template-columns: minmax(260px, 1.65fr) 150px 160px 160px 112px 132px;
            gap: 10px;
            align-items: end;
        }

        .customer-search-wrap {
            position: relative;
            display: flex;
            align-items: center;
            min-width: 0;
        }

        .customer-search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 14px;
            height: 14px;
            color: var(--text-muted, #64748b);
            pointer-events: none;
            z-index: 2;
        }

        .filter-field {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
        }

        .filter-field label {
            font-size: 9.8px;
            font-weight: 800;
            color: var(--text-muted, #64748b);
            white-space: nowrap;
        }

        .customer-filter-input {
            width: 100%;
            height: 33px;
            border: 1px solid var(--glass-border, #dbe3ef);
            border-radius: 9px;
            background: #ffffff;
            color: var(--text-main, #0f172a);
            font-size: 12px;
            font-family: inherit;
            outline: none;
            transition: 0.22s ease;
            line-height: 33px;
            padding: 0 11px;
        }

        .customer-search-wrap .customer-filter-input {
            padding-left: 34px;
        }

        .customer-filter-input:hover {
            border-color: #cbd5e1;
        }

        .customer-filter-input:focus {
            border-color: var(--primary, #2563eb);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.09);
        }

        .customer-select {
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

        .btn-filter-reset,
        .btn-add-customer {
            height: 33px;
            border-radius: 9px;
            font-size: 11.5px;
            font-weight: 800;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            cursor: pointer;
            transition: 0.2s ease;
            white-space: nowrap;
            padding: 0 12px;
        }

        .btn-filter-reset {
            border: 1px solid var(--glass-border, #dbe3ef);
            background: #ffffff;
            color: var(--text-muted, #64748b);
        }

        .btn-filter-reset:hover {
            background: #f8fafc;
            color: var(--primary, #2563eb);
            border-color: #bfdbfe;
        }

        .btn-add-customer {
            border: 1px solid var(--primary, #2563eb);
            background: var(--primary, #2563eb);
            color: #ffffff;
            box-shadow: 0 5px 14px rgba(37, 99, 235, 0.16);
        }

        .btn-add-customer:hover {
            transform: translateY(-1px);
            box-shadow: 0 7px 18px rgba(37, 99, 235, 0.22);
        }

        /* ===== Table ===== */
        .customer-table-card {
            border-radius: 14px;
            overflow: visible;
            padding: 0;
        }

        .customer-table-header {
            min-height: 46px;
            padding: 13px 16px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .customer-table-header h2 {
            margin: 0;
            font-size: 14px;
            font-weight: 900;
            color: var(--text-main, #0f172a);
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .customer-table-header h2 svg {
            color: var(--primary, #2563eb);
        }

        .customer-loading {
            padding: 46px;
        }

        .customer-empty {
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

        .customer-empty i {
            width: 42px;
            height: 42px;
            opacity: 0.45;
        }

        .customer-table-wrapper {
            width: 100%;
            overflow: visible;
        }

        .customer-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            font-size: 11.8px;
        }

        .customer-table thead th {
            padding: 8px 10px;
            text-align: left;
            color: var(--text-muted, #64748b);
            font-size: 9.8px;
            line-height: 1.2;
            text-transform: uppercase;
            font-weight: 900;
            border-top: 1px solid var(--glass-border, #e5eaf3);
            border-bottom: 1px solid var(--glass-border, #e5eaf3);
            white-space: nowrap;
            background: #ffffff;
        }

        .customer-table tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid rgba(15, 23, 42, 0.06);
            vertical-align: middle;
            color: var(--text-main, #0f172a);
            box-sizing: border-box;
        }

        .customer-table tbody tr:hover {
            background: #f8fbff;
        }

        .row-number {
            color: var(--text-muted, #64748b);
            font-weight: 800;
            text-align: center;
        }

        .customer-name-cell {
            display: flex;
            align-items: center;
            gap: 9px;
            min-width: 0;
        }

        .customer-avatar {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 10px;
            color: #ffffff;
            flex-shrink: 0;
        }

        .customer-name {
            font-weight: 800;
            color: var(--text-main, #0f172a);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .customer-name-stack {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .customer-role-badge {
            width: fit-content;
            max-width: 100%;
            height: 16px;
            padding: 0 7px;
            border-radius: 999px;
            background: rgba(37, 99, 235, 0.10);
            color: var(--primary, #2563eb);
            border: 1px solid rgba(37, 99, 235, 0.18);
            font-size: 8.6px;
            font-weight: 900;
            line-height: 15px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .customer-role-badge.admin {
            background: rgba(124, 58, 237, 0.10);
            color: #7c3aed;
            border-color: rgba(124, 58, 237, 0.20);
        }

        .customer-muted {
            color: var(--text-muted, #64748b);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
        }

        .badge-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 23px;
            height: 20px;
            padding: 0 7px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 900;
        }

        .badge-order {
            background: rgba(37, 99, 235, 0.10);
            color: var(--primary, #2563eb);
        }

        .badge-service {
            background: rgba(245, 158, 11, 0.13);
            color: var(--warning, #f59e0b);
        }

        .money-text {
            font-weight: 900;
            color: var(--success, #10b981);
            white-space: nowrap;
            text-align: right;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            height: 20px;
            padding: 0 8px;
            border-radius: 999px;
            font-size: 9.8px;
            font-weight: 900;
            white-space: nowrap;
        }

        .status-pill::before {
            content: '';
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: currentColor;
        }

        .status-active {
            background: rgba(16, 185, 129, 0.12);
            color: var(--success, #10b981);
        }

        .status-inactive {
            background: rgba(100, 116, 139, 0.12);
            color: #64748b;
        }

        .action-buttons {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            flex-wrap: nowrap;
        }

        .btn-action-customer {
            height: 25px;
            border-radius: 7px;
            border: 1px solid var(--glass-border, #dbe3ef);
            background: #ffffff;
            font-size: 10px;
            font-weight: 800;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            cursor: pointer;
            transition: 0.2s ease;
            text-decoration: none;
            padding: 0 7px;
            white-space: nowrap;
        }

        .btn-action-customer svg {
            width: 12px;
            height: 12px;
        }

        .btn-detail {
            color: var(--primary, #2563eb);
            border-color: #bfdbfe;
            background: #f8fbff;
        }

        .btn-chat {
            color: var(--success, #10b981);
            border-color: rgba(16, 185, 129, 0.25);
            background: #f0fdf4;
        }

        .btn-history {
            color: #64748b;
            border-color: #dbe3ef;
            background: #f8fafc;
        }

        .btn-action-customer:hover {
            transform: translateY(-1px);
        }

        /* ===== Pagination ===== */
        .customer-pagination-wrapper {
            min-height: 44px;
            padding: 8px 14px;
            border-top: 1px solid var(--glass-border, #e5eaf3);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            background: #ffffff;
            border-radius: 0 0 14px 14px;
        }

        .customer-page-info {
            font-size: 11.5px;
            color: var(--text-muted, #64748b);
        }

        .customer-pagination-actions {
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .customer-page-buttons {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .page-btn {
            width: 26px;
            height: 26px;
            border-radius: 7px;
            border: 1px solid var(--glass-border, #dbe3ef);
            background: #ffffff;
            color: var(--text-main, #334155);
            font-size: 11px;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .page-btn:hover:not(:disabled) {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: var(--primary, #2563eb);
        }

        .page-btn.active {
            background: var(--primary, #2563eb);
            color: #ffffff;
            border-color: var(--primary, #2563eb);
        }

        .page-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .page-ellipsis {
            color: var(--text-muted, #94a3b8);
            font-size: 12px;
            padding: 0 2px;
        }

        .rows-per-page {
            width: 104px;
            height: 28px;
            border-radius: 8px;
            border: 1px solid var(--glass-border, #dbe3ef);
            background-color: #ffffff;
            padding: 0 28px 0 9px;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-main, #334155);
            outline: none;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 8px center;
            background-size: 12px;
            cursor: pointer;
        }

        /* ===== Modal ===== */
        .customer-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.72);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(6px);
        }

        .customer-modal-box {
            width: min(94vw, 720px);
            max-width: 720px;
            border-radius: 16px;
            overflow: hidden;
        }

        .customer-modal-box.compact-modal {
            width: min(94vw, 480px);
            max-width: 480px;
            max-height: min(90vh, 600px);
            display: flex;
            flex-direction: column;
        }

        .customer-modal-box.compact-modal > div {
            display: flex;
            flex-direction: column;
            min-height: 0;
            flex: 1;
        }

        .customer-modal-box.compact-modal .customer-modal-body {
            flex: 1;
            overflow-y: auto;
            scrollbar-width: thin;
        }

        .customer-modal-body {
            padding: 16px;
        }

        .customer-modal-header {
            padding: 14px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--glass-border, #e5eaf3);
            background: #f8fafc;
            flex-shrink: 0;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }

        .customer-modal-header h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 900;
            color: var(--text-main, #0f172a);
        }

        .customer-modal-header p {
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

        .customer-modal-body {
            padding: 16px;
            background: #ffffff;
        }

        .customer-detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .detail-card {
            border: 1px solid var(--glass-border, #e5eaf3);
            border-radius: 12px;
            padding: 10px 12px;
            background: #ffffff;
        }

        .detail-card h4 {
            margin: 0 0 8px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.35px;
            color: var(--text-muted, #64748b);
        }

        .detail-card p {
            margin: 4px 0;
            font-size: 12px;
            color: var(--text-main, #0f172a);
        }

        /* ===== Enhanced Detail Modal ===== */
        .detail-hero {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 16px 12px;
            border-bottom: 1px solid var(--glass-border, #e5eaf3);
            background: linear-gradient(135deg,#f8fbff 0%,#eef4ff 100%);
            flex-shrink: 0;
        }
        .detail-hero-avatar {
            width: 52px; height: 52px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; font-weight: 900; color: #fff;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(37,99,235,0.22);
        }
        .detail-hero-info { min-width:0; flex:1; }
        .detail-hero-name {
            font-size: 15px; font-weight: 900;
            color: var(--text-main,#0f172a);
            white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
            margin: 0 0 3px;
        }
        .detail-hero-sub { font-size:11.5px; color:var(--text-muted,#64748b); margin:0; }
        .detail-hero-badges { display:flex; gap:5px; margin-top:5px; flex-wrap:wrap; }
        .detail-mini-stats {
            display: grid;
            grid-template-columns: repeat(3,1fr);
            gap: 8px;
            padding: 12px 16px;
            background: #f8fafc;
            border-bottom: 1px solid var(--glass-border,#e5eaf3);
            flex-shrink: 0;
        }
        .detail-mini-stat {
            background: #fff;
            border: 1px solid var(--glass-border,#e5eaf3);
            border-radius: 10px;
            padding: 8px 10px;
            text-align: center;
        }
        .detail-mini-stat-val {
            font-size: 18px; font-weight: 900; line-height:1.1;
            color: var(--text-main,#0f172a);
        }
        .detail-mini-stat-val.money { font-size:13px; color: var(--success,#10b981); }
        .detail-mini-stat-lbl {
            font-size: 9.5px; font-weight:800; color:var(--text-muted,#64748b);
            text-transform:uppercase; letter-spacing:0.4px; margin-top:2px;
        }
        .detail-info-section { 
            padding: 14px 16px; 
            background: #ffffff;
            border: 1px solid var(--glass-border,#e5eaf3);
            border-radius: 12px;
        }
        .detail-info-section-title {
            font-size:10px; font-weight:900; text-transform:uppercase;
            letter-spacing:0.5px; color:var(--text-muted,#64748b);
            margin: 0 0 8px; display:flex; align-items:center; gap:5px;
        }
        .detail-info-rows { display:flex; flex-direction:column; gap:5px; }
        .detail-info-row {
            display:flex; align-items:flex-start; gap:8px;
            font-size:12px; color:var(--text-main,#0f172a);
        }
        .detail-info-row-icon {
            width:16px; height:16px; color:var(--text-muted,#64748b);
            flex-shrink:0; margin-top:1px;
        }
        .detail-info-row-label { color:var(--text-muted,#64748b); min-width:80px; flex-shrink:0; }
        .detail-info-row-val { font-weight:700; word-break:break-word; }
        .detail-divider { height:1px; background:var(--glass-border,#e5eaf3); margin:0 16px; }
        /* history timeline */
        .hist-stat-row {
            display:grid; grid-template-columns:1fr 1fr;
            gap:8px; padding:12px 16px;
            border-bottom:1px solid var(--glass-border,#e5eaf3);
            flex-shrink: 0;
        }
        .hist-stat-box {
            border-radius:10px; padding:10px 12px;
            display:flex; align-items:center; gap:10px;
        }
        .hist-stat-box.orders { background:rgba(37,99,235,0.07); border:1px solid rgba(37,99,235,0.14); }
        .hist-stat-box.servis { background:rgba(245,158,11,0.08); border:1px solid rgba(245,158,11,0.18); }
        .hist-stat-box.belanja { background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.18); }
        .hist-stat-icon {
            width:34px; height:34px; border-radius:9px;
            display:flex; align-items:center; justify-content:center; flex-shrink:0;
        }
        .hist-stat-icon svg { width:16px; height:16px; }
        .hist-stat-icon.blue { background:rgba(37,99,235,0.12); color:var(--primary,#2563eb); }
        .hist-stat-icon.amber { background:rgba(245,158,11,0.14); color:#f59e0b; }
        .hist-stat-icon.green { background:rgba(16,185,129,0.14); color:var(--success,#10b981); }
        .hist-stat-val { font-size:18px; font-weight:900; line-height:1.1; color:var(--text-main,#0f172a); }
        .hist-stat-val.money { font-size:13px; color:var(--success,#10b981); }
        .hist-stat-lbl { font-size:9.5px; font-weight:800; color:var(--text-muted,#64748b); text-transform:uppercase; letter-spacing:0.4px; }
        .hist-breakdown {
            padding: 14px 16px;
            background: #ffffff;
            border: 1px solid var(--glass-border,#e5eaf3);
            border-radius: 12px;
            display:flex; flex-direction:column; gap:6px;
        }
        .hist-breakdown-row {
            display:flex; align-items:center; justify-content:space-between;
            font-size:12px; color:var(--text-main,#0f172a);
            padding:6px 10px; border-radius:8px;
            border:1px solid var(--glass-border,#e5eaf3);
            background:#f8fafc;
        }
        .hist-breakdown-label { display:flex; align-items:center; gap:6px; color:var(--text-muted,#64748b); font-weight:700; }
        .hist-breakdown-val { font-weight:900; }

        .customer-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .form-group-modern {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .form-group-modern.full {
            grid-column: 1 / -1;
        }

        .form-group-modern label {
            font-size: 11px;
            font-weight: 800;
            color: var(--text-muted, #64748b);
        }

        .form-control-modern {
            height: 36px;
            border: 1px solid var(--glass-border, #dbe3ef);
            border-radius: 10px;
            background: #ffffff;
            color: var(--text-main, #0f172a);
            padding: 0 12px;
            font-size: 12.5px;
            outline: none;
            font-family: inherit;
        }

        textarea.form-control-modern {
            height: 72px;
            padding-top: 10px;
            resize: vertical;
        }

        .customer-modal-actions {
            padding: 12px 16px;
            border-top: 1px solid var(--glass-border, #e5eaf3);
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            background: #f8fafc;
            flex-shrink: 0;
            border-bottom-left-radius: 16px;
            border-bottom-right-radius: 16px;
        }

        .btn-modal-action {
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

        .btn-modal-action.primary {
            background: var(--primary, #2563eb);
            border-color: var(--primary, #2563eb);
            color: #ffffff;
        }

        @media (max-width: 1280px) {
            .customer-stat-card {
                min-width: 175px;
            }

            .customer-filter-grid {
                grid-template-columns: minmax(240px, 1.4fr) 140px 150px 150px 108px 128px;
            }

            .btn-action-customer span {
                display: none;
            }
        }

        @media (max-width: 992px) {
            .customer-filter-grid {
                grid-template-columns: 1fr 1fr;
            }

            .customer-table-wrapper {
                overflow-x: auto;
            }

            .customer-table {
                min-width: 1080px;
            }
        }

        @media (max-width: 768px) {
            .customer-stats-grid {
                gap: 8px;
            }

            .customer-stat-card {
                min-width: 160px;
                min-height: 70px;
                padding: 10px 12px;
            }

            .customer-stat-icon {
                width: 34px;
                height: 34px;
            }

            .customer-stat-value {
                font-size: 17px;
            }

            .customer-stat-value.stat-money {
                font-size: 13px;
            }

            .customer-filter-grid,
            .customer-detail-grid,
            .customer-form-grid {
                grid-template-columns: 1fr;
            }

            .customer-pagination-wrapper {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        let allPelanggan = [];
        let filteredPelanggan = [];
        let searchTimeout = null;
        let currentPage = 1;
        let perPage = 7;
        let pelangganCache = {};
        let databaseActivitySummary = {
            loaded: false,
            ordersTotal: 0,
            servicesTotal: 0,
            totalBelanja: 0,
            ordersByUser: new Map(),
            servicesByUser: new Map()
        };

        const avatarColors = [
            '#2563eb', '#7c3aed', '#0891b2', '#059669', '#d97706', '#dc2626', '#6366f1', '#0d9488', '#ea580c', '#db2777'
        ];

        document.addEventListener('DOMContentLoaded', () => {
            if (!isLoggedIn() || !isAdmin()) {
                window.location.href = '/';
                return;
            }
            loadPelanggan();
        });

        function debounceSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applyFilters();
            }, 350);
        }

        function getSafeUserFromLocalStorage() {
            const keys = ['user', 'auth_user', 'current_user', 'pengguna'];
            for (const key of keys) {
                try {
                    const raw = localStorage.getItem(key);
                    if (!raw) continue;
                    const parsed = JSON.parse(raw);
                    if (parsed && typeof parsed === 'object') return parsed;
                } catch (e) {
                    // Abaikan localStorage yang tidak valid.
                }
            }
            return null;
        }

        function getRoleValue(p = {}) {
            return String(p.role || p.level || p.tipe_user || p.user_role || '').toLowerCase().trim();
        }

        function isAdminCustomer(p = {}) {
            const role = getRoleValue(p);
            return role === 'admin' || role === 'administrator' || role === 'superadmin' || p.__is_admin_defkan === true;
        }

        function normalizePelangganRecord(raw = {}, options = {}) {
            const role = String(raw.role || options.role || 'user').toLowerCase();
            const isAdmin = role === 'admin' || role === 'administrator' || role === 'superadmin' || options.isAdmin === true;

            return {
                ...raw,
                id_pengguna: raw.id_pengguna || raw.id_user || raw.id || raw.uid || raw.user_id,
                nama: raw.nama || raw.name || raw.full_name || (isAdmin ? 'Admin Defkan' : 'Pelanggan'),
                email: raw.email || raw.email_pengguna || '',
                no_hp: raw.no_hp || raw.telepon || raw.phone || raw.nomor_hp || '',
                alamat: raw.alamat || raw.address || raw.alamat_lengkap || '',
                role: isAdmin ? 'admin' : role,
                status: raw.status ?? raw.status_pelanggan ?? 1,
                pesanan_count: Number(raw.pesanan_count || raw.total_pesanan || 0),
                servis_count: Number(raw.servis_count || raw.total_servis || 0),
                total_belanja: Number(raw.total_belanja || 0),
                created_at: raw.created_at || raw.tanggal_daftar || raw.createdAt || raw.created || '',
                __is_admin_defkan: isAdmin
            };
        }

        async function getAdminDefkanFromDatabase() {
            let admin = null;

            try {
                const res = await apiFetch('/me');
                admin = res?.data || res?.user || res?.pengguna || res;
            } catch (e) {
                admin = getSafeUserFromLocalStorage();
            }

            if (!admin || typeof admin !== 'object') return null;

            const role = String(admin.role || '').toLowerCase();
            if (!role.includes('admin')) {
                admin.role = 'admin';
            }

            return normalizePelangganRecord(admin, { role: 'admin', isAdmin: true });
        }

        function mergePelangganWithAdmin(pelanggan = [], admin = null) {
            const map = new Map();

            pelanggan.forEach(item => {
                const normalized = normalizePelangganRecord(item);
                const key = getPelangganId(normalized);
                if (key) map.set(key, normalized);
            });

            if (admin) {
                const adminKey = getPelangganId(admin);
                const existingByEmailKey = Array.from(map.keys()).find(key => {
                    const item = map.get(key);
                    return item?.email && admin.email && String(item.email).toLowerCase() === String(admin.email).toLowerCase();
                });

                if (existingByEmailKey) {
                    const existing = map.get(existingByEmailKey) || {};
                    map.set(existingByEmailKey, normalizePelangganRecord({
                        ...existing,
                        ...admin,
                        // Jangan biarkan data /me yang biasanya kosong menimpa data database.
                        pesanan_count: Number(existing.pesanan_count || existing.total_pesanan || 0),
                        total_pesanan: Number(existing.pesanan_count || existing.total_pesanan || 0),
                        servis_count: Number(existing.servis_count || existing.total_servis || 0),
                        total_servis: Number(existing.servis_count || existing.total_servis || 0),
                        total_belanja: Number(existing.total_belanja || 0),
                        __is_admin_defkan: true,
                        role: 'admin'
                    }, { role: 'admin', isAdmin: true }));
                } else if (adminKey) {
                    map.set(adminKey, admin);
                }
            }

            return Array.from(map.values());
        }

        function extractApiList(res) {
            const payload = res?.data ?? res;
            if (Array.isArray(payload)) return payload;
            if (Array.isArray(payload?.data)) return payload.data;
            if (Array.isArray(payload?.items)) return payload.items;
            if (Array.isArray(payload?.rows)) return payload.rows;
            return [];
        }

        function extractApiTotal(res, fallbackList = []) {
            const payload = res?.data ?? res;
            return Number(payload?.total ?? payload?.total_data ?? payload?.count ?? fallbackList.length ?? 0);
        }

        function getDatabaseUserKey(row = {}) {
            const id = row.id_pengguna
                ?? row.pengguna_id
                ?? row.user_id
                ?? row.id_user
                ?? row.pelanggan_id
                ?? row.customer_id
                ?? row.pengguna?.id_pengguna
                ?? row.pengguna?.id
                ?? row.user?.id_pengguna
                ?? row.user?.id
                ?? row.customer?.id_pengguna
                ?? row.customer?.id;

            if (id === undefined || id === null || String(id).trim() === '') return '';
            return String(id);
        }

        function getPelangganDatabaseKey(p = {}) {
            const id = p.id_pengguna ?? p.pengguna_id ?? p.id_user ?? p.user_id ?? p.id ?? p.uid;
            if (id === undefined || id === null || String(id).trim() === '') return '';
            return String(id);
        }

        function getOrderTotal(row = {}) {
            return Number(row.total_harga ?? row.grand_total ?? row.total ?? row.total_pembayaran ?? row.subtotal ?? 0);
        }

        function isBelanjaStatusCounted(status = '') {
            return isOrderRevenueStatusCounted(status);
        }

        function isOrderRevenueStatusCounted(status = '') {
            // Disamakan dengan card Total Pendapatan di Kelola Pesanan:
            // pendapatan dihitung dari pesanan yang sudah selesai/valid bayar.
            const value = String(status || '').toLowerCase().trim().replace(/\s+/g, '_').replace(/-/g, '_');
            return ['selesai', 'dibayar', 'lunas', 'paid', 'success', 'settlement', 'sukses'].includes(value);
        }

        function normalizeServiceStatusForCustomer(status = '') {
            const value = String(status || '').toLowerCase().trim().replace(/\s+/g, '_').replace(/-/g, '_');
            const aliases = {
                menunggu_konfirmasi: 'menunggu',
                belum_dikonfirmasi: 'menunggu',
                sedang_diperiksa: 'diperiksa',
                dalam_pengecekan: 'diperiksa',
                pengecekan: 'diperiksa',
                dicek: 'diperiksa',
                sedang_dikerjakan: 'dikerjakan',
                proses: 'dikerjakan',
                diproses: 'dikerjakan',
                siap_diambil: 'diambil',
                sudah_diambil: 'diambil'
            };
            return aliases[value] || value;
        }

        function isServiceRevenueStatusCounted(status = '') {
            // Disamakan dengan card Total Pendapatan di Kelola Servis:
            // pendapatan servis dihitung dari status selesai.
            return normalizeServiceStatusForCustomer(status) === 'selesai';
        }

        function getServiceTotalForCustomer(row = {}) {
            return Number(
                row.total_biaya
                ?? row.biaya_total
                ?? row.biaya_akhir
                ?? row.estimasi_biaya
                ?? row.estimasi
                ?? row.biaya_estimasi
                ?? row.total_pembayaran
                ?? 0
            );
        }

        function getApiStatsPayload(res = {}) {
            return res?.stats || res?.data?.stats || res?.data?.summary || res?.summary || {};
        }

        async function loadDatabaseActivitySummary() {
            const summary = {
                loaded: false,
                ordersTotal: 0,
                servicesTotal: 0,
                ordersRevenue: 0,
                servicesRevenue: 0,
                totalBelanja: 0,
                ordersByUser: new Map(),
                servicesByUser: new Map()
            };

            try {
                const [pesananRes, servisRes] = await Promise.all([
                    apiFetch('/pesanan?page=1&per_page=10000'),
                    apiFetch('/servis?page=1&per_page=10000')
                ]);

                const orders = extractApiList(pesananRes);
                const services = extractApiList(servisRes);
                const pesananStats = getApiStatsPayload(pesananRes);

                // Total pesanan disamakan dengan card Total Pesanan di Kelola Pesanan.
                summary.ordersTotal = Number(pesananStats.total ?? pesananStats.total_pesanan ?? extractApiTotal(pesananRes, orders) ?? 0);

                // Total servis disamakan dengan card Total Servis di Kelola Servis.
                summary.servicesTotal = extractApiTotal(servisRes, services);

                orders.forEach(order => {
                    const userKey = getDatabaseUserKey(order);
                    const total = getOrderTotal(order);
                    const status = order.status || order.status_pesanan || '';

                    if (userKey) {
                        const current = summary.ordersByUser.get(userKey) || { count: 0, totalBelanja: 0 };
                        current.count += 1;
                        if (isOrderRevenueStatusCounted(status)) {
                            current.totalBelanja += total;
                        }
                        summary.ordersByUser.set(userKey, current);
                    }

                    if (isOrderRevenueStatusCounted(status)) {
                        summary.ordersRevenue += total;
                    }
                });

                // Jika API Kelola Pesanan sudah mengirim stats.total_pendapatan,
                // pakai itu agar benar-benar sinkron dengan card Kelola Pesanan.
                if (pesananStats.total_pendapatan !== undefined || pesananStats.total_revenue !== undefined || pesananStats.pendapatan !== undefined) {
                    summary.ordersRevenue = Number(pesananStats.total_pendapatan ?? pesananStats.total_revenue ?? pesananStats.pendapatan ?? summary.ordersRevenue ?? 0);
                }

                services.forEach(service => {
                    const userKey = getDatabaseUserKey(service);
                    const total = getServiceTotalForCustomer(service);
                    const status = service.status || service.status_servis || '';

                    if (userKey) {
                        const current = summary.servicesByUser.get(userKey) || { count: 0, totalBelanja: 0 };
                        current.count += 1;
                        if (isServiceRevenueStatusCounted(status)) {
                            current.totalBelanja += total;
                        }
                        summary.servicesByUser.set(userKey, current);
                    }

                    if (isServiceRevenueStatusCounted(status)) {
                        summary.servicesRevenue += total;
                    }
                });

                // Total belanja Admin Defkan = pendapatan Kelola Pesanan + pendapatan Kelola Servis selesai.
                summary.totalBelanja = summary.ordersRevenue + summary.servicesRevenue;
                summary.loaded = true;
            } catch (e) {
                console.warn('Gagal mengambil aktivitas pesanan/servis untuk pelanggan:', e);
            }

            return summary;
        }

        function enrichPelangganWithDatabaseActivity(data = [], summary = databaseActivitySummary) {
            if (!summary || !summary.loaded) return data;

            return data.map(item => {
                const p = normalizePelangganRecord(item, { isAdmin: isAdminCustomer(item), role: isAdminCustomer(item) ? 'admin' : item.role });
                const key = getPelangganDatabaseKey(p);
                const orderStats = key ? summary.ordersByUser.get(key) : null;
                const serviceStats = key ? summary.servicesByUser.get(key) : null;

                // Admin Defkan Computer: tampilkan hanya pesanan & servis atas nama akun admin tersebut (pakai ID-nya).
                // Bukan total keseluruhan pesanan/servis sistem.
                const fallbackOrderCount = Number(p.pesanan_count || p.total_pesanan || 0);
                const fallbackServiceCount = Number(p.servis_count || p.total_servis || 0);
                const orderBelanja = orderStats ? Number(orderStats.totalBelanja || 0) : Number(p.total_belanja_produk || p.total_belanja || 0);
                const serviceBelanja = serviceStats ? Number(serviceStats.totalBelanja || 0) : Number(p.total_belanja_servis || 0);

                return {
                    ...p,
                    pesanan_count: orderStats ? orderStats.count : fallbackOrderCount,
                    total_pesanan: orderStats ? orderStats.count : fallbackOrderCount,
                    servis_count: serviceStats ? serviceStats.count : fallbackServiceCount,
                    total_servis: serviceStats ? serviceStats.count : fallbackServiceCount,
                    total_belanja_produk: orderBelanja,
                    total_belanja_servis: serviceBelanja,
                    total_belanja: orderBelanja + serviceBelanja
                };
            });
        }

        function getRegistrationTime(p = {}) {
            const date = new Date(p.created_at || p.tanggal_daftar || p.createdAt || p.created || '');
            return isNaN(date.getTime()) ? Number.MAX_SAFE_INTEGER : date.getTime();
        }

        function sortByFirstRegistered(data = []) {
            return [...data].sort((a, b) => {
                const dateDiff = getRegistrationTime(a) - getRegistrationTime(b);
                if (dateDiff !== 0) return dateDiff;

                const idA = Number(a.id_pengguna || a.id_user || a.id || 0);
                const idB = Number(b.id_pengguna || b.id_user || b.id || 0);
                if (idA && idB && idA !== idB) return idA - idB;

                return String(a.email || a.nama || '').localeCompare(String(b.email || b.nama || ''));
            });
        }

        function assignCustomerRanks(data = []) {
            data.forEach((item, index) => {
                item.__customer_rank = index + 1;
            });
            return data;
        }

        async function loadPelanggan() {
            const loading = document.getElementById('loading-state');
            const empty = document.getElementById('empty-state');
            const table = document.getElementById('table-wrapper');
            const pagination = document.getElementById('pagination-wrapper');

            loading.style.display = 'flex';
            empty.style.display = 'none';
            table.style.display = 'none';
            pagination.innerHTML = '';

            try {
                const [res, adminRecord] = await Promise.all([
                    apiFetch('/admin/pelanggan'),
                    getAdminDefkanFromDatabase()
                ]);

                // Hanya pelanggan biasa (bukan admin) dari API
                const pelangganFromApi = (Array.isArray(res.data) ? res.data : [])
                    .map(item => normalizePelangganRecord(item))
                    .filter(item => !isAdminCustomer(item));

                databaseActivitySummary = await loadDatabaseActivitySummary();

                const enrichedPelanggan = enrichPelangganWithDatabaseActivity(
                    pelangganFromApi,
                    databaseActivitySummary
                );

                // Buat entri Admin Defkan Computer dengan data aktivitas berdasarkan ID akun admin.
                // Hanya pesanan & servis yang tercatat atas nama akun admin (bukan total keseluruhan sistem).
                let adminEntry = null;
                if (adminRecord) {
                    const enrichedAdmin = enrichPelangganWithDatabaseActivity([adminRecord], databaseActivitySummary)[0];
                    adminEntry = {
                        ...enrichedAdmin,
                        nama: adminRecord.nama || 'Admin Defkan Computer',
                        __is_admin_defkan: true,
                        role: 'admin',
                        status: 1
                    };
                }

                const sortedPelanggan = assignCustomerRanks(
                    sortByFirstRegistered(enrichedPelanggan)
                );

                // Admin selalu di baris pertama (rank 0 / No. 1)
                if (adminEntry) {
                    adminEntry.__customer_rank = 1;
                    sortedPelanggan.forEach(p => p.__customer_rank = (p.__customer_rank || 0) + 1);
                    allPelanggan = [adminEntry, ...sortedPelanggan];
                } else {
                    allPelanggan = sortedPelanggan;
                }

                pelangganCache = {};
                allPelanggan.forEach(p => {
                    pelangganCache[getPelangganId(p)] = p;
                });

                loading.style.display = 'none';
                updateStats(allPelanggan);
                applyFilters(false);
            } catch (err) {
                loading.style.display = 'none';
                empty.style.display = 'flex';
                if (typeof showToast === 'function') showToast(err.message, 'error');
            }
        }

        function applyFilters(resetPage = true) {
            const search = document.getElementById('search-input').value.trim().toLowerCase();
            const status = document.getElementById('filter-status').value;
            const periode = document.getElementById('filter-periode').value;
            const aktivitas = document.getElementById('filter-aktivitas').value;

            if (resetPage) currentPage = 1;

            filteredPelanggan = allPelanggan.filter(p => {
                const searchable = [
                    p.nama,
                    p.email,
                    p.no_hp,
                    p.telepon,
                    p.phone,
                    p.role,
                    isAdminCustomer(p) ? 'admin defkan administrator' : 'pelanggan'
                ]
                    .map(v => String(v || '').toLowerCase())
                    .join(' ');

                if (search && !searchable.includes(search)) return false;
                if (status && getCustomerStatus(p).value !== status) return false;
                if (periode && !isInPeriod(p.created_at, periode)) return false;

                const pesanan = Number(p.pesanan_count || p.total_pesanan || 0);
                const servis = Number(p.servis_count || p.total_servis || 0);
                const belanja = Number(p.total_belanja || 0);

                if (aktivitas === 'pernah-belanja' && pesanan <= 0) return false;
                if (aktivitas === 'pernah-servis' && servis <= 0) return false;
                if (aktivitas === 'belum-aktivitas' && (pesanan > 0 || servis > 0 || belanja > 0)) return false;

                return true;
            });

            renderCurrentPage();
        }

        function resetFilters() {
            document.getElementById('search-input').value = '';
            document.getElementById('filter-status').value = '';
            document.getElementById('filter-periode').value = '';
            document.getElementById('filter-aktivitas').value = '';
            currentPage = 1;
            applyFilters(false);
        }

        function renderCurrentPage() {
            const loading = document.getElementById('loading-state');
            const empty = document.getElementById('empty-state');
            const table = document.getElementById('table-wrapper');

            loading.style.display = 'none';

            if (filteredPelanggan.length === 0) {
                table.style.display = 'none';
                empty.style.display = 'flex';
                renderPagination(0);
                if (window.lucide) lucide.createIcons();
                return;
            }

            const totalPages = Math.max(1, Math.ceil(filteredPelanggan.length / perPage));
            if (currentPage > totalPages) currentPage = totalPages;

            const start = (currentPage - 1) * perPage;
            const pageData = filteredPelanggan.slice(start, start + perPage);

            empty.style.display = 'none';
            table.style.display = 'block';
            renderTable(pageData, start);
            renderPagination(filteredPelanggan.length);
        }

        function updateStats(data) {
            // Hitung total termasuk Admin Defkan Computer
            document.getElementById('stat-total').textContent = data.length;
            document.getElementById('stat-total-sub').innerHTML = `<span class="positive">↑ ${data.length ? 'Aktif dikelola' : 'Belum ada data'}</span>`;

            // Aktif: semua yang status aktif (termasuk admin)
            const aktif = data.filter(p => getCustomerStatus(p).value === 'aktif').length;
            document.getElementById('stat-aktif').textContent = aktif;

            // Pernah belanja: yang punya pesanan > 0 (admin mengelola semua, pasti > 0)
            const pembeli = data.filter(p => Number(p.pesanan_count || p.total_pesanan || 0) > 0).length;
            document.getElementById('stat-pembeli').textContent = pembeli;

            // Pelanggan baru bulan ini: hanya pelanggan biasa (bukan admin)
            const now = new Date();
            const bulanIni = data.filter(p => {
                if (isAdminCustomer(p)) return false; // admin tidak dihitung sebagai pelanggan baru
                const d = new Date(p.created_at);
                return !isNaN(d.getTime()) && d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear();
            }).length;
            document.getElementById('stat-baru').textContent = bulanIni;

            // Total Nilai Belanja = jumlah total_belanja dari SEMUA user yang terdaftar (termasuk admin)
            const totalBelanja = data.reduce((sum, p) => sum + Number(p.total_belanja || 0), 0);
            document.getElementById('stat-belanja').textContent = formatMoney(totalBelanja);
        }

        function renderTable(data, offset = 0) {
            const tbody = document.getElementById('pelanggan-tbody');
            tbody.innerHTML = '';

            data.forEach((p, i) => {
                const displayNumber = p.__customer_rank || (offset + i + 1);
                const colorIndex = Math.max(0, displayNumber - 1);
                const date = formatDate(p.created_at);
                const displayName = p.nama || (isAdminCustomer(p) ? 'Admin Defkan' : '-');
                const initial = (displayName || 'U').charAt(0).toUpperCase();
                const color = avatarColors[colorIndex % avatarColors.length];
                const belanja = Number(p.total_belanja || 0);
                const pesanan = Number(p.pesanan_count || p.total_pesanan || 0);
                const servis = Number(p.servis_count || p.total_servis || 0);
                const status = getCustomerStatus(p);
                const id = getPelangganId(p);
                const isAdmin = isAdminCustomer(p);

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="row-number">${displayNumber}</td>
                    <td>
                        <div class="customer-name-cell">
                            <div class="customer-avatar" style="background:${color};">${escapeHtml(initial)}</div>
                            <div class="customer-name-stack">
                                <span class="customer-name" title="${escapeHtml(displayName)}">${escapeHtml(displayName)}</span>
                                ${isAdmin ? '<span class="customer-role-badge admin">Admin Defkan</span>' : ''}
                            </div>
                        </div>
                    </td>
                    <td><span class="customer-muted" title="${escapeHtml(p.email || '-')}">${escapeHtml(p.email || '-')}</span></td>
                    <td><span class="customer-muted">${escapeHtml(p.no_hp || p.telepon || p.phone || '-')}</span></td>
                    <td style="text-align:center;"><span class="badge-count badge-order">${pesanan}</span></td>
                    <td style="text-align:center;"><span class="badge-count badge-service">${servis}</span></td>
                    <td class="money-text">${formatMoney(belanja)}</td>
                    <td style="text-align:center;"><span class="status-pill ${status.className}">${status.label}</span></td>
                    <td><span class="customer-muted">${date}</span></td>
                    <td>
                        <div class="action-buttons">
                            <button type="button" class="btn-action-customer btn-detail" onclick="showCustomerDetail('${id}')" title="Detail">
                                <i data-lucide="eye"></i><span>Detail</span>
                            </button>
                            <button type="button" class="btn-action-customer btn-chat" onclick="chatCustomer('${id}')" title="Chat">
                                <i data-lucide="message-circle"></i><span>Chat</span>
                            </button>
                            <button type="button" class="btn-action-customer btn-history" onclick="showCustomerHistory('${id}')" title="Riwayat">
                                <i data-lucide="history"></i><span>Riwayat</span>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            if (window.lucide) lucide.createIcons();
        }

        function renderPagination(total) {
            const wrapper = document.getElementById('pagination-wrapper');
            if (total === 0) {
                wrapper.innerHTML = '';
                return;
            }

            const totalPages = Math.max(1, Math.ceil(total / perPage));
            const startItem = ((currentPage - 1) * perPage) + 1;
            const endItem = Math.min(currentPage * perPage, total);

            let buttons = '';
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || Math.abs(i - currentPage) <= 1) {
                    buttons += `<button class="page-btn ${i === currentPage ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
                } else if (i === currentPage - 2 || i === currentPage + 2) {
                    buttons += `<span class="page-ellipsis">...</span>`;
                }
            }

            wrapper.innerHTML = `
                <span class="customer-page-info">Menampilkan ${startItem} - ${endItem} dari ${total} pelanggan</span>
                <div class="customer-pagination-actions">
                    <div class="customer-page-buttons">
                        <button class="page-btn" onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>
                            <i data-lucide="chevron-left" class="icon-xs"></i>
                        </button>
                        ${buttons}
                        <button class="page-btn" onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>
                            <i data-lucide="chevron-right" class="icon-xs"></i>
                        </button>
                    </div>
                    <select class="rows-per-page" onchange="changePerPage(this.value)">
                        <option value="5" ${perPage == 5 ? 'selected' : ''}>5 / halaman</option>
                        <option value="10" ${perPage == 10 ? 'selected' : ''}>10 / halaman</option>
                        <option value="25" ${perPage == 25 ? 'selected' : ''}>25 / halaman</option>
                        <option value="50" ${perPage == 50 ? 'selected' : ''}>50 / halaman</option>
                    </select>
                </div>
            `;

            if (window.lucide) lucide.createIcons();
        }

        function goToPage(page) {
            const totalPages = Math.max(1, Math.ceil(filteredPelanggan.length / perPage));
            if (page < 1 || page > totalPages) return;
            currentPage = page;
            renderCurrentPage();
        }

        function changePerPage(value) {
            perPage = parseInt(value, 10) || 7;
            currentPage = 1;
            renderCurrentPage();
        }

        function getPelangganId(p = {}) {
            return String(p.id_pengguna || p.id_user || p.id || p.uid || p.email || p.no_hp || Math.random());
        }

        function getCustomerAddress(p = {}) {
            return String(
                p.alamat ||
                p.alamat_lengkap ||
                p.address ||
                p.detail_alamat ||
                p.alamat_pengguna ||
                p.pengguna?.alamat ||
                p.pengguna?.alamat_lengkap ||
                p.pengguna?.address ||
                ''
            ).trim() || 'Belum ada alamat.';
        }

        function getCustomerStatus(p = {}) {
            if (isAdminCustomer(p)) {
                return { value: 'aktif', label: 'Aktif', className: 'status-active' };
            }

            const rawStatus = String(p.status || p.status_pelanggan || '').toLowerCase();
            const pesanan = Number(p.pesanan_count || p.total_pesanan || 0);
            const servis = Number(p.servis_count || p.total_servis || 0);
            const belanja = Number(p.total_belanja || 0);

            if (['nonaktif', 'inactive', 'blokir', 'blocked', '0'].includes(rawStatus)) {
                return { value: 'nonaktif', label: 'Nonaktif', className: 'status-inactive' };
            }

            if (['aktif', 'active', '1'].includes(rawStatus) || pesanan > 0 || servis > 0 || belanja > 0) {
                return { value: 'aktif', label: 'Aktif', className: 'status-active' };
            }

            return { value: 'nonaktif', label: 'Nonaktif', className: 'status-inactive' };
        }

        function isInPeriod(dateValue, type) {
            const date = new Date(dateValue);
            if (isNaN(date.getTime())) return false;

            const now = new Date();
            const startOfToday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            const target = new Date(date.getFullYear(), date.getMonth(), date.getDate());

            if (type === 'today') {
                return target.getTime() === startOfToday.getTime();
            }

            if (type === 'week') {
                const day = now.getDay() || 7;
                const startWeek = new Date(now.getFullYear(), now.getMonth(), now.getDate() - day + 1);
                const endWeek = new Date(startWeek.getFullYear(), startWeek.getMonth(), startWeek.getDate() + 6);
                return target >= startWeek && target <= endWeek;
            }

            if (type === 'month') {
                return date.getMonth() === now.getMonth() && date.getFullYear() === now.getFullYear();
            }

            if (type === 'year') {
                return date.getFullYear() === now.getFullYear();
            }

            return true;
        }

        function formatDate(value) {
            if (!value) return '—';
            const date = new Date(value);
            if (isNaN(date.getTime())) return '—';
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
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

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function openModal(html, isCompact = false) {
            const modalBox = document.querySelector('.customer-modal-box');
            if (modalBox) {
                if (isCompact) {
                    modalBox.classList.add('compact-modal');
                } else {
                    modalBox.classList.remove('compact-modal');
                }
            }
            document.getElementById('customer-modal-content').innerHTML = html;
            document.getElementById('customer-modal').style.display = 'flex';
            if (window.lucide) lucide.createIcons();
        }

        function closeCustomerModal(e) {
            if (!e || e.target === document.getElementById('customer-modal')) {
                document.getElementById('customer-modal').style.display = 'none';
                document.getElementById('customer-modal-content').innerHTML = '';
            }
        }

        function showCustomerDetail(id) {
            const p = pelangganCache[id];
            if (!p) { if (typeof showToast === 'function') showToast('Data tidak ditemukan.', 'error'); return; }
            const status = getCustomerStatus(p);
            const isAdmin = isAdminCustomer(p);
            const name = p.nama || (isAdmin ? 'Admin Defkan Computer' : 'Pelanggan');
            const initial = (name).charAt(0).toUpperCase();
            const colors = ['#2563eb','#7c3aed','#0891b2','#059669','#d97706','#dc2626'];
            const color = colors[(p.__customer_rank || 1) % colors.length];
            const pesanan = Number(p.pesanan_count || p.total_pesanan || 0);
            const servis = Number(p.servis_count || p.total_servis || 0);
            const belanja = Number(p.total_belanja || 0);
            const belanjaOrder = Number(p.total_belanja_produk || 0);
            const belanjaServis = Number(p.total_belanja_servis || 0);
            const phone = p.no_hp || p.telepon || p.phone || '-';
            const address = getCustomerAddress(p) || 'Alamat belum diisi';
            const html = `
                    <div class="customer-modal-header">
                        <div><h3>Detail Pelanggan</h3><p>Informasi lengkap akun pelanggan</p></div>
                        <button type="button" class="btn-modal-close" onclick="closeCustomerModal()"><i data-lucide="x" class="icon-sm"></i></button>
                    </div>
                    <div class="detail-hero">
                        <div class="detail-hero-avatar" style="background:${color}">${escapeHtml(initial)}</div>
                        <div class="detail-hero-info">
                            <h4 class="detail-hero-name">${escapeHtml(name)}</h4>
                            <p class="detail-hero-sub">${escapeHtml(p.email || '-')}</p>
                            <div class="detail-hero-badges">
                                <span class="customer-role-badge ${isAdmin ? 'admin' : ''}">${isAdmin ? 'Admin Defkan' : 'Pelanggan'}</span>
                                <span class="status-pill ${status.className}">${status.label}</span>
                            </div>
                        </div>
                    </div>
                    <div class="detail-mini-stats">
                        <div class="detail-mini-stat">
                            <div class="detail-mini-stat-val">${pesanan}</div>
                            <div class="detail-mini-stat-lbl">Total Pesanan</div>
                        </div>
                        <div class="detail-mini-stat">
                            <div class="detail-mini-stat-val">${servis}</div>
                            <div class="detail-mini-stat-lbl">Total Servis</div>
                        </div>
                        <div class="detail-mini-stat">
                            <div class="detail-mini-stat-val money">${formatMoney(belanja)}</div>
                            <div class="detail-mini-stat-lbl">Total Belanja</div>
                        </div>
                    </div>
                    <div class="customer-modal-body" style="padding: 16px; display: flex; flex-direction: column; gap: 12px; background: #f8fafc;">
                        <div class="detail-info-section">
                            <div class="detail-info-section-title"><i data-lucide="user" style="width:12px;height:12px;"></i> Informasi Pribadi</div>
                            <div class="detail-info-rows">
                                <div class="detail-info-row"><i data-lucide="user" class="detail-info-row-icon"></i><span class="detail-info-row-label">Nama</span><span class="detail-info-row-val">${escapeHtml(name)}</span></div>
                                <div class="detail-info-row"><i data-lucide="mail" class="detail-info-row-icon"></i><span class="detail-info-row-label">Email</span><span class="detail-info-row-val">${escapeHtml(p.email || '-')}</span></div>
                                <div class="detail-info-row"><i data-lucide="phone" class="detail-info-row-icon"></i><span class="detail-info-row-label">No. HP</span><span class="detail-info-row-val">${escapeHtml(phone)}</span></div>
                                <div class="detail-info-row"><i data-lucide="map-pin" class="detail-info-row-icon"></i><span class="detail-info-row-label">Alamat</span><span class="detail-info-row-val">${escapeHtml(address)}</span></div>
                                <div class="detail-info-row"><i data-lucide="calendar" class="detail-info-row-icon"></i><span class="detail-info-row-label">Terdaftar</span><span class="detail-info-row-val">${formatDate(p.created_at)}</span></div>
                            </div>
                        </div>
                        <div class="detail-info-section">
                            <div class="detail-info-section-title"><i data-lucide="bar-chart-2" style="width:12px;height:12px;"></i> Rincian Belanja</div>
                            <div class="detail-info-rows">
                                <div class="detail-info-row"><i data-lucide="shopping-bag" class="detail-info-row-icon"></i><span class="detail-info-row-label">Dari Pesanan</span><span class="detail-info-row-val" style="color:var(--success,#10b981)">${formatMoney(belanjaOrder)}</span></div>
                                <div class="detail-info-row"><i data-lucide="wrench" class="detail-info-row-icon"></i><span class="detail-info-row-label">Dari Servis</span><span class="detail-info-row-val" style="color:var(--success,#10b981)">${formatMoney(belanjaServis)}</span></div>
                                <div class="detail-info-row" style="background:#f0fdf4;border-radius:8px;padding:6px 8px;border:1px solid rgba(16,185,129,0.18);"><i data-lucide="banknote" class="detail-info-row-icon" style="color:var(--success,#10b981);"></i><span class="detail-info-row-label">Total Belanja</span><span class="detail-info-row-val" style="color:var(--success,#10b981)">${formatMoney(belanja)}</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="customer-modal-actions">
                        <button type="button" class="btn-modal-action" onclick="chatCustomer('${id}'); closeCustomerModal();" style="color:var(--success,#10b981);border-color:rgba(16,185,129,0.25);background:#f0fdf4;">
                            <i data-lucide="message-circle" class="icon-sm"></i> Chat WA
                        </button>
                        <button type="button" class="btn-modal-action" onclick="closeCustomerModal(); setTimeout(()=>showCustomerHistory('${id}'),100);" style="color:var(--primary,#2563eb);border-color:#bfdbfe;background:#f8fbff;">
                            <i data-lucide="history" class="icon-sm"></i> Riwayat
                        </button>
                        <button type="button" class="btn-modal-action" onclick="closeCustomerModal()">
                            <i data-lucide="x" class="icon-sm"></i> Tutup
                        </button>
                    </div>
            `;
            openModal(html, true);
        }

        function showCustomerHistory(id) {
            const p = pelangganCache[id];
            if (!p) { if (typeof showToast === 'function') showToast('Data tidak ditemukan.', 'error'); return; }
            const isAdmin = isAdminCustomer(p);
            const name = p.nama || (isAdmin ? 'Admin Defkan Computer' : 'Pelanggan');
            const pesanan = Number(p.pesanan_count || p.total_pesanan || 0);
            const servis = Number(p.servis_count || p.total_servis || 0);
            const belanja = Number(p.total_belanja || 0);
            const belanjaOrder = Number(p.total_belanja_produk || 0);
            const belanjaServis = Number(p.total_belanja_servis || 0);
            const status = getCustomerStatus(p);
            const terdaftar = formatDate(p.created_at);
            const html = `
                    <div class="customer-modal-header">
                        <div><h3>Riwayat Aktivitas</h3><p>${escapeHtml(name)}</p></div>
                        <button type="button" class="btn-modal-close" onclick="closeCustomerModal()"><i data-lucide="x" class="icon-sm"></i></button>
                    </div>
                    <div class="hist-stat-row">
                        <div class="hist-stat-box orders">
                            <div class="hist-stat-icon blue"><i data-lucide="shopping-bag"></i></div>
                            <div><div class="hist-stat-val">${pesanan}</div><div class="hist-stat-lbl">Total Pesanan</div></div>
                        </div>
                        <div class="hist-stat-box servis">
                            <div class="hist-stat-icon amber"><i data-lucide="wrench"></i></div>
                            <div><div class="hist-stat-val">${servis}</div><div class="hist-stat-lbl">Total Servis</div></div>
                        </div>
                    </div>
                    <div class="customer-modal-body" style="padding: 16px; background: #f8fafc;">
                        <div class="hist-breakdown">
                            <div style="font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted,#64748b);margin-bottom:4px;display:flex;align-items:center;gap:5px;"><i data-lucide="banknote" style="width:12px;height:12px;"></i> Rincian Belanja</div>
                            <div class="hist-breakdown-row">
                                <span class="hist-breakdown-label"><i data-lucide="shopping-bag" style="width:13px;height:13px;color:var(--primary,#2563eb);"></i> Dari Pesanan (selesai)</span>
                                <span class="hist-breakdown-val" style="color:var(--primary,#2563eb)">${formatMoney(belanjaOrder)}</span>
                            </div>
                            <div class="hist-breakdown-row">
                                <span class="hist-breakdown-label"><i data-lucide="wrench" style="width:13px;height:13px;color:#f59e0b;"></i> Dari Servis (selesai)</span>
                                <span class="hist-breakdown-val" style="color:#f59e0b">${formatMoney(belanjaServis)}</span>
                            </div>
                            <div class="hist-breakdown-row" style="background:#f0fdf4;border-radius:8px;padding:6px 8px;border:1px solid rgba(16,185,129,0.18);"><i data-lucide="banknote" class="detail-info-row-icon" style="color:var(--success,#10b981);"></i><span class="detail-info-row-label">Total Belanja</span><span class="detail-info-row-val" style="color:var(--success,#10b981)">${formatMoney(belanja)}</span></div>
                            <div style="height:8px;"></div>
                            <div style="font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted,#64748b);margin-bottom:4px;display:flex;align-items:center;gap:5px;"><i data-lucide="info" style="width:12px;height:12px;"></i> Info Akun</div>
                            <div class="hist-breakdown-row">
                                <span class="hist-breakdown-label"><i data-lucide="shield" style="width:13px;height:13px;"></i> Role</span>
                                <span class="hist-breakdown-val">${isAdmin ? 'Admin Defkan' : 'Pelanggan'}</span>
                            </div>
                            <div class="hist-breakdown-row">
                                <span class="hist-breakdown-label"><i data-lucide="circle-dot" style="width:13px;height:13px;"></i> Status</span>
                                <span class="status-pill ${status.className}" style="height:18px;font-size:9px;">${status.label}</span>
                            </div>
                            <div class="hist-breakdown-row">
                                <span class="hist-breakdown-label"><i data-lucide="calendar" style="width:13px;height:13px;"></i> Terdaftar</span>
                                <span class="hist-breakdown-val">${terdaftar}</span>
                            </div>
                        </div>
                    </div>
                    <div class="customer-modal-actions">
                        <button type="button" class="btn-modal-action" onclick="closeCustomerModal(); setTimeout(()=>showCustomerDetail('${id}'),100);" style="color:var(--primary,#2563eb);border-color:#bfdbfe;background:#f8fbff;">
                            <i data-lucide="eye" class="icon-sm"></i> Detail
                        </button>
                        <button type="button" class="btn-modal-action" onclick="chatCustomer('${id}'); closeCustomerModal();" style="color:var(--success,#10b981);border-color:rgba(16,185,129,0.25);background:#f0fdf4;">
                            <i data-lucide="message-circle" class="icon-sm"></i> Chat WA
                        </button>
                        <button type="button" class="btn-modal-action" onclick="closeCustomerModal()">
                            <i data-lucide="x" class="icon-sm"></i> Tutup
                        </button>
                    </div>
            `;
            openModal(html, true);
        }

        function chatCustomer(id) {
            const p = pelangganCache[id];
            if (!p) return;

            let phone = String(p.no_hp || p.telepon || p.phone || '').replace(/[^0-9]/g, '');
            if (!phone) {
                if (typeof showToast === 'function') showToast('Nomor HP pelanggan belum tersedia.', 'error');
                return;
            }

            if (phone.startsWith('0')) phone = '62' + phone.slice(1);
            const message = encodeURIComponent(`Halo ${p.nama || ''}, kami dari Defkan Computer.`);
            window.open(`https://wa.me/${phone}?text=${message}`, '_blank');
        }

        function openAddPelangganModal() {
            const html = `
                <form onsubmit="submitTambahPelanggan(event)">
                    <div class="customer-modal-header">
                        <div>
                            <h3>Tambah Pelanggan</h3>
                            <p>Tambahkan data pelanggan baru ke sistem.</p>
                        </div>
                        <button type="button" class="btn-modal-close" onclick="closeCustomerModal()">
                            <i data-lucide="x" class="icon-sm"></i>
                        </button>
                    </div>
                    <div class="customer-modal-body">
                        <div class="customer-form-grid">
                            <div class="form-group-modern">
                                <label>Nama Pelanggan *</label>
                                <input type="text" id="add-nama" class="form-control-modern" required placeholder="Contoh: Pani Padika">
                            </div>
                            <div class="form-group-modern">
                                <label>Email *</label>
                                <input type="email" id="add-email" class="form-control-modern" required placeholder="email@gmail.com">
                            </div>
                            <div class="form-group-modern">
                                <label>No. HP</label>
                                <input type="text" id="add-nohp" class="form-control-modern" placeholder="08xxxxxxxxxx">
                            </div>
                            <div class="form-group-modern">
                                <label>Password Default</label>
                                <input type="text" id="add-password" class="form-control-modern" value="12345678">
                            </div>
                            <div class="form-group-modern full">
                                <label>Alamat *</label>
                                <textarea id="add-alamat" class="form-control-modern" placeholder="Alamat lengkap pelanggan" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="customer-modal-actions">
                        <button type="button" class="btn-modal-action" onclick="closeCustomerModal()">
                            <i data-lucide="x" class="icon-sm"></i> Batal
                        </button>
                        <button type="submit" id="btn-submit-add-customer" class="btn-modal-action primary">
                            <i data-lucide="save" class="icon-sm"></i> Simpan Pelanggan
                        </button>
                    </div>
                </form>
            `;
            openModal(html);
        }

        function getCreatePelangganPayload() {
            const nama = document.getElementById('add-nama').value.trim();
            const email = document.getElementById('add-email').value.trim();
            const noHp = document.getElementById('add-nohp').value.trim();
            const password = document.getElementById('add-password').value.trim() || '12345678';
            const alamat = document.getElementById('add-alamat').value.trim();

            return {
                nama,
                name: nama,
                email,
                no_hp: noHp,
                phone: noHp,
                telepon: noHp,
                password,
                password_confirmation: password,
                alamat,
                alamat_lengkap: alamat,
                address: alamat,
                alamat_pengguna: alamat,
                detail_alamat: alamat,
                role: 'user',
                status: 1,
                status_pelanggan: 'aktif'
            };
        }

        function setAddCustomerLoading(isLoading) {
            const btn = document.getElementById('btn-submit-add-customer');
            if (!btn) return;

            if (isLoading) {
                btn.dataset.originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<div class="loader" style="width:15px;height:15px;border-width:2px;"></div> Menyimpan...';
            } else {
                btn.disabled = false;
                btn.innerHTML = btn.dataset.originalHtml || '<i data-lucide="save" class="icon-sm"></i> Simpan Pelanggan';
                if (window.lucide) lucide.createIcons();
            }
        }

        function isRetryableEndpointError(err) {
            const message = String(err?.message || '').toLowerCase();
            return [
                'post method is not supported',
                'method is not supported',
                'method not allowed',
                'supported methods: get, head',
                '405',
                '404',
                'not found',
                'route',
                'not supported for route'
            ].some(keyword => message.includes(keyword));
        }

        async function tryApiCreatePelanggan(payload) {
            const endpoints = [
                '/admin/pelanggan',
                '/admin/pelanggan/store',
                '/admin/pelanggan/create',
                '/admin/pelanggan/tambah',
                '/pelanggan',
                '/pengguna',
                '/users',
                '/auth/register',
                '/register'
            ];

            let lastError = null;

            for (const endpoint of endpoints) {
                try {
                    return await apiFetch(endpoint, {
                        method: 'POST',
                        body: payload
                    });
                } catch (err) {
                    lastError = err;

                    if (!isRetryableEndpointError(err)) {
                        throw err;
                    }
                }
            }

            throw lastError || new Error('Endpoint tambah pelanggan belum tersedia.');
        }

        async function tryWebRegisterFallback(payload) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            if (!csrfToken) {
                throw new Error('CSRF token tidak ditemukan untuk fallback register.');
            }

            const response = await fetch('/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload)
            });

            let result = null;
            const contentType = response.headers.get('content-type') || '';
            if (contentType.includes('application/json')) {
                result = await response.json();
            }

            if (!response.ok) {
                const message = result?.message || Object.values(result?.errors || {}).flat()?.[0] || 'Gagal menambahkan pelanggan melalui route register.';
                throw new Error(message);
            }

            return result || { status: 'success' };
        }

        async function createPelangganFromExistingRoutes(payload) {
            try {
                return await tryApiCreatePelanggan(payload);
            } catch (apiError) {
                if (!isRetryableEndpointError(apiError)) {
                    throw apiError;
                }

                return await tryWebRegisterFallback(payload);
            }
        }

        async function submitTambahPelanggan(event) {
            event.preventDefault();

            const payload = getCreatePelangganPayload();

            if (!payload.nama || !payload.email) {
                if (typeof showToast === 'function') showToast('Nama dan email pelanggan wajib diisi.', 'error');
                return;
            }

            if (!payload.alamat) {
                if (typeof showToast === 'function') showToast('Alamat pelanggan wajib diisi.', 'error');
                return;
            }

            if (payload.password.length < 8) {
                if (typeof showToast === 'function') showToast('Password minimal 8 karakter.', 'error');
                return;
            }

            setAddCustomerLoading(true);

            try {
                await createPelangganFromExistingRoutes(payload);
                if (typeof showToast === 'function') showToast('Pelanggan berhasil ditambahkan.', 'success');
                closeCustomerModal();
                await loadPelanggan();
            } catch (err) {
                const message = err.message || 'Gagal menambahkan pelanggan.';
                if (typeof showToast === 'function') {
                    showToast(message, 'error');
                } else {
                    alert(message);
                }
            } finally {
                setAddCustomerLoading(false);
            }
        }
    </script>
@endpush