@extends('layouts.admin')

@section('title', 'Admin — Kelola Servis | Defkan Computer')
@section('admin-title', 'Kelola Servis')
@section('tab-servis', 'active')

@section('content')
    <div class="servis-layout servis-fit-page">
        {{-- Stats Cards --}}
        <div class="stats-grid service-summary-grid">
            <div class="stat-card clickable" onclick="setFilterStatus('')">
                <div class="stat-icon-wrapper icon-blue">
                    <i data-lucide="clipboard-list" class="stat-icon"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Total Servis</span>
                    <h3 class="stat-value" id="stat-total">0</h3>
                    <span class="stat-subtext">Semua Servis</span>
                </div>
            </div>

            <div class="stat-card clickable" onclick="setFilterStatus('menunggu')">
                <div class="stat-icon-wrapper icon-orange">
                    <i data-lucide="hourglass" class="stat-icon"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Menunggu Konfirmasi</span>
                    <h3 class="stat-value" id="stat-menunggu">0</h3>
                    <span class="stat-subtext">Perlu Dikonfirmasi</span>
                </div>
            </div>

            <div class="stat-card clickable" onclick="setFilterStatus('diperiksa')">
                <div class="stat-icon-wrapper icon-sky">
                    <i data-lucide="search-check" class="stat-icon"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Sedang Diperiksa</span>
                    <h3 class="stat-value" id="stat-diperiksa">0</h3>
                    <span class="stat-subtext">Dalam Pengecekan</span>
                </div>
            </div>

            <div class="stat-card clickable" onclick="setFilterStatus('dikerjakan')">
                <div class="stat-icon-wrapper icon-purple">
                    <i data-lucide="settings" class="stat-icon"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Dikerjakan</span>
                    <h3 class="stat-value" id="stat-dikerjakan">0</h3>
                    <span class="stat-subtext">Sedang Dikerjakan</span>
                </div>
            </div>

            <div class="stat-card clickable" onclick="setFilterStatus('selesai')">
                <div class="stat-icon-wrapper icon-green">
                    <i data-lucide="check-circle-2" class="stat-icon"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Selesai</span>
                    <h3 class="stat-value" id="stat-selesai">0</h3>
                    <span class="stat-subtext">Servis Selesai</span>
                </div>
            </div>

            <div class="stat-card clickable" onclick="setFilterStatus('selesai')" title="Klik untuk melihat servis selesai">
                <div class="stat-icon-wrapper icon-cyan">
                    <i data-lucide="wallet" class="stat-icon"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Total Pendapatan</span>
                    <h3 class="stat-value stat-money" id="stat-pendapatan-selesai">Rp 0</h3>
                    <span class="stat-subtext">Dari Servis Selesai</span>
                </div>
            </div>
        </div>

        {{-- Filter Toolbar --}}
        <div class="glass-panel service-filter-card">
            <div class="service-filter-top">
                <div class="compact-search">
                    <i data-lucide="search" class="filter-search-icon"></i>
                    <input type="text" id="filter-search" class="filter-input"
                        placeholder="Cari kode servis, nama pelanggan, atau perangkat..." oninput="debounceFilter()">
                </div>

                <select id="filter-status" class="filter-input compact-select" onchange="loadServis(1)">
                    <option value="">Semua Status</option>
                    <option value="menunggu">Menunggu</option>
                    <option value="diperiksa">Diperiksa</option>
                    <option value="dikerjakan">Dikerjakan</option>
                    <option value="selesai">Selesai</option>
                    <option value="diambil">Diambil</option>
                </select>

                <div class="date-range-filter">
                    <input type="date" id="filter-start" class="filter-input compact-date" onchange="loadServis(1)">
                    <span>-</span>
                    <input type="date" id="filter-end" class="filter-input compact-date" onchange="loadServis(1)">
                </div>

                <button class="btn-export-mini" type="button" onclick="exportToExcel()">
                    <i data-lucide="download" class="icon-sm"></i>
                    Export
                </button>
            </div>

            <div class="service-filter-bottom">
                <span class="filter-label">Filter Cepat :</span>
                <button class="quick-filter-btn" id="qf-today" onclick="setQuickDate('today')">Hari Ini</button>
                <button class="quick-filter-btn" id="qf-week" onclick="setQuickDate('week')">Minggu Ini</button>
                <button class="quick-filter-btn" id="qf-month" onclick="setQuickDate('month')">Bulan Ini</button>
            </div>
        </div>

        {{-- Table Area --}}
        <div class="glass-panel service-table-card">
            <div id="servis-table-wrapper" class="service-table-wrap">
                <div class="flex-center compact-loader">
                    <div class="loader"></div>
                </div>
            </div>

            <div id="pagination-wrapper" class="pagination-wrapper">
                <!-- Pagination -->
            </div>
        </div>
    </div>

    {{-- Modal Detail Servis --}}
    <div id="detail-modal" class="service-modal" onclick="closeDetailModal(event)">
        <div class="glass-panel service-modal-box" onclick="event.stopPropagation()">
            <div id="detail-modal-content"></div>
        </div>
    </div>

    {{-- Modal Update Catatan Teknisi --}}
    <div id="keterangan-modal" class="service-modal" onclick="closeKetModal(event)">
        <div class="glass-panel service-note-box" onclick="event.stopPropagation()">
            <div class="modal-header-compact">
                <div>
                    <h2>
                        <i data-lucide="edit-3" class="icon-sm"></i>
                        Update Catatan Teknisi
                    </h2>
                    <p>Tambahkan catatan singkat hasil pengecekan/perbaikan.</p>
                </div>
                <button class="modal-close-btn" type="button" onclick="closeKetModal()">
                    <i data-lucide="x" class="icon-sm"></i>
                </button>
            </div>

            <input type="hidden" id="ket-servis-id">
            <div class="note-form-group">
                <label for="ket-input">Catatan Singkat / Teknisi</label>
                <textarea id="ket-input" class="note-textarea" rows="4"
                    placeholder="Contoh: Pergantian keyboard sedang diproses..."></textarea>
            </div>

            <div class="modal-footer-compact">
                <button type="button" onclick="closeKetModal()" class="btn-note-secondary">Batal</button>
                <button type="button" onclick="saveKeterangan()" class="btn-note-primary">
                    <i data-lucide="save" class="icon-xs"></i>
                    Simpan
                </button>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .servis-layout {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding-bottom: 12px;
        }

        .servis-fit-page {
            min-height: calc(100vh - 120px);
        }

        .glass-panel {
            background: var(--bg-surface, #ffffff);
            border: 1px solid var(--glass-border, #e5eaf3);
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.045);
        }

        /* ===== Summary Cards ===== */
        .service-summary-grid {
            display: flex;
            align-items: stretch;
            gap: 8px;
            width: 100%;
            overflow-x: auto;
            padding-bottom: 2px;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .service-summary-grid::-webkit-scrollbar {
            display: none;
        }

        .stat-card {
            flex: 1 1 0;
            min-width: 0;
            min-height: 56px;
            padding: 7px 9px;
            border-radius: 13px;
            background: #ffffff;
            border: 1px solid var(--glass-border, #e5eaf3);
            display: flex;
            align-items: center;
            gap: 8px;
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
            background: rgba(245, 158, 11, 0.13);
            color: var(--warning, #f59e0b);
        }

        .icon-sky {
            background: rgba(14, 165, 233, 0.13);
            color: #0284c7;
        }

        .icon-purple {
            background: rgba(139, 92, 246, 0.13);
            color: #8b5cf6;
        }

        .icon-green {
            background: rgba(16, 185, 129, 0.13);
            color: var(--success, #10b981);
        }

        .icon-cyan {
            background: rgba(14, 165, 233, 0.13);
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
            font-size: 8.3px;
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

        .stat-money {
            font-size: 13.2px;
            letter-spacing: -0.03em;
        }


        .stat-subtext {
            font-size: 9px;
            line-height: 1.15;
            color: var(--text-muted, #94a3b8);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ===== Filter ===== */
        .service-filter-card {
            padding: 9px 12px;
            border-radius: 14px;
            flex-shrink: 0;
        }

        .service-filter-top {
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
            background: #ffffff;
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
        }

        .filter-input:focus {
            border-color: var(--primary, #2563eb);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.09);
        }

        select.filter-input,
        .status-select-mini,
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

        .service-filter-bottom {
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

        /* ===== Table Fixed / No Internal Scroll ===== */
        .service-table-card {
            border-radius: 14px;
            overflow: visible;
            flex: 0 0 auto;
            display: flex;
            flex-direction: column;
        }

        .service-table-wrap {
            width: 100%;
            overflow: visible;
            min-height: 316px;
        }

        .service-table-wrap.rows-4 {
            min-height: 316px;
        }

        .service-table-wrap.rows-more {
            min-height: auto;
        }

        .compact-loader {
            padding: 44px;
        }

        .service-table {
            width: 100%;
            min-width: 0;
            table-layout: fixed;
            border-collapse: collapse;
            font-size: 11.5px;
        }

        .service-table thead th {
            background: #ffffff;
            padding: 8px 9px;
            text-align: left;
            color: var(--text-muted, #64748b);
            font-size: 9.7px;
            line-height: 1.2;
            text-transform: uppercase;
            font-weight: 800;
            border-bottom: 1px solid var(--glass-border, #e5eaf3);
            white-space: nowrap;
        }

        .service-table tbody td {
            padding: 8px 9px;
            border-bottom: 1px solid rgba(15, 23, 42, 0.06);
            vertical-align: middle;
            color: var(--text-main, #0f172a);
            box-sizing: border-box;
        }

        .service-table tbody tr:hover {
            background: #f8fbff;
        }

        .service-table th:nth-child(1),
        .service-table td:nth-child(1) {
            width: 9%;
        }

        .service-table th:nth-child(2),
        .service-table td:nth-child(2) {
            width: 8%;
        }

        .service-table th:nth-child(3),
        .service-table td:nth-child(3) {
            width: 11%;
        }

        .service-table th:nth-child(4),
        .service-table td:nth-child(4) {
            width: 13%;
        }

        .service-table th:nth-child(5),
        .service-table td:nth-child(5) {
            width: 13%;
        }

        .service-table th:nth-child(6),
        .service-table td:nth-child(6) {
            width: 8%;
        }

        .service-table th:nth-child(7),
        .service-table td:nth-child(7) {
            width: 8%;
        }

        .service-table th:nth-child(8),
        .service-table td:nth-child(8) {
            width: 15%;
        }

        .service-table th:nth-child(9),
        .service-table td:nth-child(9) {
            width: 15%;
        }

        .service-code {
            color: var(--primary, #2563eb);
            font-weight: 900;
            font-size: 11px;
            word-break: break-word;
            line-height: 1.25;
        }

        .stack-sm {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
        }

        .date-main,
        .customer-name,
        .device-name,
        .issue-title,
        .estimate-price,
        .tech-name {
            font-weight: 800;
            color: var(--text-main, #0f172a);
            line-height: 1.25;
        }

        .date-sub,
        .customer-phone,
        .device-sn,
        .issue-desc,
        .tech-note,
        .muted-small {
            color: var(--text-muted, #64748b);
            font-size: 10px;
            line-height: 1.22;
        }

        .line-clamp-1 {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
            max-width: 100%;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.28;
        }

        .estimate-price {
            white-space: nowrap;
            font-size: 11.5px;
        }

        .status-badge-modern {
            height: 21px;
            padding: 0 9px;
            border-radius: 999px;
            font-size: 9.5px;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }

        .badge-warning {
            background: rgba(245, 158, 11, 0.14);
            color: #d97706;
        }

        .badge-primary {
            background: rgba(37, 99, 235, 0.12);
            color: #2563eb;
        }

        .badge-accent {
            background: rgba(139, 92, 246, 0.13);
            color: #7c3aed;
        }

        .badge-success {
            background: rgba(16, 185, 129, 0.14);
            color: #059669;
        }

        .badge-muted {
            background: rgba(100, 116, 139, 0.16);
            color: #475569;
        }

        .action-cell {
            display: grid;
            grid-template-columns: 1fr;
            gap: 5px;
            width: 146px;
            max-width: 100%;
            margin-left: auto;
        }

        .action-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5px;
        }

        .btn-detail-mini,
        .btn-invoice-mini,
        .btn-action-icon,
        .status-select-mini {
            height: 26px;
            border-radius: 8px;
            font-size: 10.5px;
            font-family: inherit;
        }

        .btn-detail-mini,
        .btn-invoice-mini,
        .btn-action-icon {
            border: 1px solid var(--glass-border, #dbe3ef);
            background: #ffffff;
            color: var(--primary, #2563eb);
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 0 7px;
            cursor: pointer;
            text-decoration: none;
            transition: 0.2s ease;
            white-space: nowrap;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .btn-detail-mini {
            border-color: #bfdbfe;
            background: #f8fbff;
            color: var(--primary, #2563eb);
        }

        .btn-invoice-mini {
            border-color: #e9d5ff;
            background: #faf5ff;
            color: #a855f7;
        }

        .btn-action-icon {
            color: #3b82f6;
            min-width: 30px;
            padding: 0;
        }

        .btn-detail-mini:hover,
        .btn-invoice-mini:hover,
        .btn-action-icon:hover {
            transform: translateY(-1px);
        }

        .btn-detail-mini:hover {
            background: #eff6ff;
        }

        .btn-invoice-mini:hover {
            background: #f3e8ff;
        }

        .status-select-mini {
            width: 100%;
            padding: 0 24px 0 8px;
            border: 1px solid var(--glass-border, #dbe3ef);
            background-color: #ffffff;
            color: var(--text-main, #334155);
            font-weight: 700;
            outline: none;
        }

        .status-select-mini:focus {
            border-color: var(--primary, #2563eb);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
        }

        .empty-state,
        .error-state {
            min-height: 250px;
            padding: 44px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: var(--text-muted, #64748b);
            text-align: center;
        }

        .empty-state h4,
        .error-state h4 {
            margin: 0;
            color: var(--text-main, #0f172a);
            font-size: 14px;
        }

        .empty-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .empty-icon {
            width: 26px;
            height: 26px;
            color: #94a3b8;
        }

        /* ===== Pagination ===== */
        .pagination-wrapper {
            min-height: 42px;
            padding: 7px 12px;
            border-top: 1px solid var(--glass-border, #e5eaf3);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
            background: #ffffff;
            overflow: visible;
        }

        .pagination-info {
            font-size: 12px;
            color: var(--text-muted, #64748b);
        }

        .pagination-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .pagination-controls {
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
            font-weight: 800;
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

        .page-btn:disabled,
        .page-btn.disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .page-ellipsis {
            color: var(--text-muted, #94a3b8);
            font-size: 12px;
            padding: 0 2px;
        }

        .rows-per-page {
            width: 112px;
            height: 28px;
            border-radius: 8px;
            border: 1px solid var(--glass-border, #dbe3ef);
            background-color: #ffffff;
            padding: 0 28px 0 9px;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-main, #334155);
            outline: none;
        }

        /* ===== Modals ===== */
        .service-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.72);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(6px);
        }

        .service-modal.show {
            display: flex;
        }

        .service-modal-box,
        .service-note-box {
            width: min(94vw, 860px);
            border-radius: 16px;
            overflow: hidden;
            background: #ffffff;
        }

        .service-note-box {
            width: min(92vw, 500px);
            padding: 16px;
        }

        .modal-header-compact {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 14px;
        }

        .modal-header-compact h2 {
            margin: 0;
            font-size: 15px;
            font-weight: 900;
            color: var(--text-main, #0f172a);
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .modal-header-compact p {
            margin: 3px 0 0;
            color: var(--text-muted, #64748b);
            font-size: 11.5px;
        }

        .modal-close-btn {
            width: 30px;
            height: 30px;
            border-radius: 9px;
            border: 1px solid var(--glass-border, #dbe3ef);
            background: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .note-form-group label {
            display: block;
            font-size: 11.5px;
            font-weight: 800;
            color: var(--text-muted, #64748b);
            margin-bottom: 7px;
        }

        .note-textarea {
            width: 100%;
            min-height: 96px;
            border: 1px solid var(--glass-border, #dbe3ef);
            border-radius: 10px;
            padding: 10px;
            font-family: inherit;
            font-size: 12.5px;
            outline: none;
            resize: vertical;
        }

        .note-textarea:focus {
            border-color: var(--primary, #2563eb);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
        }

        .modal-footer-compact {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 14px;
        }

        .btn-note-secondary,
        .btn-note-primary {
            height: 32px;
            border-radius: 9px;
            padding: 0 13px;
            font-size: 12px;
            font-weight: 800;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }

        .btn-note-secondary {
            background: #ffffff;
            border: 1px solid var(--glass-border, #dbe3ef);
            color: var(--text-main, #334155);
        }

        .btn-note-primary {
            background: var(--primary, #2563eb);
            border: 1px solid var(--primary, #2563eb);
            color: #ffffff;
        }

        .detail-modal-inner {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
        }

        .detail-topbar {
            padding: 13px 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--glass-border, #e5eaf3);
            background: #f8fafc;
        }

        .detail-topbar h3 {
            margin: 0;
            font-size: 15px;
            font-weight: 900;
            color: var(--text-main, #0f172a);
        }

        .detail-topbar p {
            margin: 2px 0 0;
            font-size: 11.5px;
            color: var(--text-muted, #64748b);
        }

        .detail-body {
            padding: 14px 15px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .detail-card {
            border: 1px solid var(--glass-border, #e5eaf3);
            border-radius: 12px;
            padding: 10px 12px;
        }

        .detail-card h4 {
            margin: 0 0 8px;
            color: var(--text-muted, #64748b);
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.35px;
        }

        .detail-card p {
            margin: 4px 0;
            color: var(--text-main, #0f172a);
            font-size: 12px;
            line-height: 1.35;
        }


        .detail-note-area {
            margin-top: 12px;
            border: 1px solid var(--glass-border, #e5eaf3);
            border-radius: 12px;
            padding: 10px 12px;
            background: #ffffff;
        }

        .detail-note-area label {
            display: block;
            margin-bottom: 7px;
            color: var(--text-muted, #64748b);
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.35px;
            font-weight: 900;
        }

        .detail-note-textarea {
            width: 100%;
            min-height: 82px;
            border: 1px solid var(--glass-border, #dbe3ef);
            border-radius: 10px;
            padding: 9px 10px;
            font-family: inherit;
            font-size: 12px;
            outline: none;
            resize: vertical;
        }

        .detail-note-textarea:focus {
            border-color: var(--primary, #2563eb);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
        }

        .detail-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            padding: 12px 15px;
            border-top: 1px solid var(--glass-border, #e5eaf3);
            background: #f8fafc;
        }

        .invoice-modal-inner {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
        }

        .invoice-body {
            padding: 14px 15px;
        }

        .invoice-brand-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .invoice-brand h2 {
            margin: 0;
            font-size: 18px;
            font-weight: 900;
            color: var(--primary, #2563eb);
            letter-spacing: -0.02em;
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
            margin-bottom: 12px;
        }

        .invoice-info-card {
            border: 1px solid var(--glass-border, #e5eaf3);
            border-radius: 12px;
            padding: 10px 12px;
            background: #ffffff;
        }

        .invoice-info-card h4 {
            margin: 0 0 8px;
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.35px;
            color: var(--text-muted, #64748b);
        }

        .invoice-info-card p {
            margin: 4px 0;
            font-size: 12px;
            color: var(--text-main, #0f172a);
            line-height: 1.35;
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
            padding: 7px 8px;
            border-bottom: 1px solid var(--glass-border, #eef2f7);
            text-align: left;
            word-break: break-word;
        }

        .invoice-items-table th {
            background: #f8fafc;
            font-size: 10.5px;
            text-transform: uppercase;
            color: var(--text-muted, #64748b);
            font-weight: 900;
        }

        .invoice-items-table tr:last-child td {
            border-bottom: none;
        }

        .invoice-warranty-note {
            margin-top: 10px;
            padding: 9px 10px;
            border-radius: 10px;
            border: 1px solid rgba(37, 99, 235, 0.18);
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 11.5px;
            line-height: 1.35;
        }

        .invoice-total-row {
            margin-top: 11px;
            display: flex;
            justify-content: flex-end;
        }

        .invoice-total-box {
            min-width: 245px;
            border: 1px solid var(--glass-border, #e5eaf3);
            border-radius: 12px;
            overflow: hidden;
        }

        .invoice-total-line {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            padding: 9px 11px;
            font-size: 12px;
            color: var(--text-main, #0f172a);
        }

        .invoice-total-line.grand {
            background: #eff6ff;
            color: var(--primary, #2563eb);
            font-weight: 900;
            font-size: 13px;
        }

        .sidebar [href*="servis"] .badge,
        .sidebar [href*="servis"] .menu-badge,
        .sidebar [href*="servis"] .count-badge,
        .sidebar .tab-servis .badge,
        .sidebar .nav-servis .badge,
        [data-menu="servis"] .badge,
        [data-menu="servis"] .menu-badge,
        [data-menu="servis"] .count-badge {
            background: #ef4444 !important;
            color: #ffffff !important;
            border-color: #ef4444 !important;
            box-shadow: 0 5px 12px rgba(239, 68, 68, 0.22) !important;
        }


        .payment-summary-cell {
            gap: 3px;
        }

        .payment-status-badge {
            width: fit-content;
            height: 18px;
            padding: 0 8px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }

        .payment-status-badge.paid {
            background: rgba(16, 185, 129, 0.13);
            color: #059669;
        }

        .payment-status-badge.pending {
            background: rgba(245, 158, 11, 0.14);
            color: #d97706;
        }

        .payment-method-text {
            color: var(--text-main, #0f172a);
            font-size: 11px;
            line-height: 1.16;
            font-weight: 800;
        }

        .payment-date-text {
            color: var(--text-muted, #64748b);
            font-size: 9.8px;
            line-height: 1.14;
        }

        .payment-proof-box {
            margin-top: 12px;
            border: 1px solid var(--glass-border, #e5eaf3);
            border-radius: 12px;
            padding: 10px 12px;
            background: #ffffff;
        }

        .payment-proof-box h4 {
            margin: 0 0 8px;
            color: var(--text-muted, #64748b);
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.35px;
        }

        .payment-proof-img {
            max-width: 100%;
            max-height: 260px;
            object-fit: contain;
            border-radius: 10px;
            border: 1px solid var(--glass-border, #e5eaf3);
            background: #f8fafc;
            display: block;
        }

        .payment-proof-link {
            margin-top: 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--primary, #2563eb);
            font-size: 12px;
            font-weight: 800;
            text-decoration: none;
        }

        @media (max-width: 1280px) {
            .stat-card {
                min-width: 158px;
            }

            .service-filter-top {
                grid-template-columns: minmax(260px, 1.25fr) 145px minmax(245px, 0.9fr) 106px;
            }
        }

        @media (max-width: 992px) {
            .service-filter-top {
                grid-template-columns: 1fr 145px;
            }

            .btn-export-mini {
                width: 100%;
            }

            .service-table {
                min-width: 980px;
            }

            .service-table-wrap {
                overflow-x: auto;
            }
        }

        @media (max-width: 768px) {
            .service-summary-grid {
                gap: 7px;
            }

            .stat-card {
                min-width: 155px;
                min-height: 54px;
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

            .stat-subtext {
                font-size: 8.8px;
            }

            .service-filter-top {
                grid-template-columns: 1fr;
                gap: 7px;
                margin-bottom: 7px;
            }

            .service-filter-card {
                padding: 8px 10px;
            }

            .filter-input,
            .btn-export-mini {
                height: 31px;
            }

            .pagination-wrapper {
                align-items: flex-start;
                flex-direction: column;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }
        }


        /* ===== FINAL COMPACT MODAL: DETAIL & INVOICE SERVIS ===== */
        /* Patch ini hanya mengatur ukuran modal/card isi Detail & Invoice Servis.
           Tidak mengubah tabel utama, filter, pagination, export, update status, atau endpoint. */
        .service-modal {
            padding: 18px !important;
            overflow: hidden !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .service-modal-box {
            width: min(90vw, 930px) !important;
            max-height: 88vh !important;
            border-radius: 22px !important;
            overflow: hidden !important;
            background: #ffffff !important;
            display: flex !important;
            flex-direction: column !important;
            box-shadow: 0 24px 70px rgba(15, 23, 42, 0.22) !important;
        }

        .service-modal-box.detail-mode {
            width: min(88vw, 790px) !important;
        }

        .service-modal-box.invoice-mode {
            width: min(90vw, 930px) !important;
        }

        #detail-modal-content {
            width: 100% !important;
            max-height: 88vh !important;
            min-height: 0 !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .detail-modal-inner,
        .invoice-modal-inner {
            width: 100% !important;
            max-height: 88vh !important;
            min-height: 0 !important;
            border-radius: 22px !important;
            overflow: hidden !important;
            background: #ffffff !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .detail-topbar {
            flex: 0 0 auto !important;
            padding: 12px 20px !important;
            background: #f8fafc !important;
            border-bottom: 1px solid var(--glass-border, #e5eaf3) !important;
        }

        .detail-topbar h3 {
            font-size: 16px !important;
            line-height: 1.2 !important;
            letter-spacing: -0.02em !important;
        }

        .detail-topbar p {
            font-size: 11.5px !important;
            line-height: 1.3 !important;
        }

        .modal-close-btn {
            width: 30px !important;
            height: 30px !important;
            border-radius: 10px !important;
            flex-shrink: 0 !important;
        }

        .detail-body,
        .invoice-body {
            flex: 1 1 auto !important;
            min-height: 0 !important;
            overflow-y: hidden !important;
            overflow-x: hidden !important;
            padding: 16px 20px !important;
        }

        .detail-grid,
        .invoice-info-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 10px !important;
            margin-bottom: 10px !important;
        }

        .detail-card,
        .invoice-info-card {
            border-radius: 14px !important;
            padding: 10px 13px !important;
            background: #ffffff !important;
        }

        .detail-card h4,
        .invoice-info-card h4,
        .payment-proof-box h4,
        .detail-note-area label {
            margin-bottom: 6px !important;
            font-size: 10px !important;
            line-height: 1.2 !important;
            letter-spacing: 0.32px !important;
        }

        .detail-card p,
        .invoice-info-card p {
            margin: 3px 0 !important;
            font-size: 11.6px !important;
            line-height: 1.32 !important;
        }

        .payment-proof-box {
            margin-top: 10px !important;
            border-radius: 14px !important;
            padding: 10px 13px !important;
        }

        .payment-proof-img {
            width: auto !important;
            max-width: 205px !important;
            max-height: 155px !important;
            object-fit: contain !important;
            border-radius: 10px !important;
        }

        .payment-proof-link {
            margin-top: 6px !important;
            font-size: 11px !important;
        }

        .detail-note-area {
            margin-top: 10px !important;
            border-radius: 14px !important;
            padding: 10px 13px !important;
        }

        .detail-note-textarea {
            min-height: 62px !important;
            padding: 8px 10px !important;
            border-radius: 10px !important;
            font-size: 11.5px !important;
            line-height: 1.35 !important;
            resize: vertical !important;
        }

        .detail-actions {
            flex: 0 0 auto !important;
            padding: 12px 20px !important;
            gap: 8px !important;
            background: #f8fafc !important;
            border-top: 1px solid var(--glass-border, #e5eaf3) !important;
        }

        .detail-actions .btn-note-secondary,
        .detail-actions .btn-note-primary {
            height: 32px !important;
            padding: 0 14px !important;
            border-radius: 10px !important;
            font-size: 12px !important;
        }

        .invoice-brand-row {
            margin-bottom: 12px !important;
            gap: 12px !important;
            align-items: flex-start !important;
        }

        .invoice-brand h2 {
            font-size: 18px !important;
            line-height: 1.2 !important;
        }

        .invoice-brand p,
        .invoice-meta p {
            font-size: 11.5px !important;
            line-height: 1.3 !important;
            margin: 3px 0 !important;
        }

        .invoice-meta strong {
            font-size: 13px !important;
            line-height: 1.25 !important;
        }

        .invoice-items-table {
            table-layout: fixed !important;
            border-radius: 12px !important;
            font-size: 11.5px !important;
        }

        .invoice-items-table th,
        .invoice-items-table td {
            padding: 7px 8px !important;
            line-height: 1.3 !important;
        }

        .invoice-items-table th {
            font-size: 10px !important;
        }

        .invoice-warranty-note {
            margin-top: 10px !important;
            padding: 9px 11px !important;
            border-radius: 12px !important;
            font-size: 11.5px !important;
            line-height: 1.35 !important;
        }

        .invoice-total-row {
            margin-top: 11px !important;
            justify-content: flex-end !important;
        }

        .invoice-total-box {
            min-width: 245px !important;
            max-width: 290px !important;
            border-radius: 12px !important;
        }

        .invoice-total-line {
            padding: 9px 12px !important;
            font-size: 12px !important;
            line-height: 1.25 !important;
        }

        .invoice-total-line.grand {
            font-size: 13.5px !important;
        }

        @media (max-width: 768px) {
            .service-modal {
                padding: 10px !important;
            }

            .service-modal-box,
            .service-modal-box.detail-mode,
            .service-modal-box.invoice-mode {
                width: 96vw !important;
                max-height: 90vh !important;
                border-radius: 18px !important;
            }

            .detail-modal-inner,
            .invoice-modal-inner,
            #detail-modal-content {
                max-height: 90vh !important;
            }

            .detail-topbar,
            .detail-body,
            .invoice-body,
            .detail-actions {
                padding-left: 13px !important;
                padding-right: 13px !important;
            }

            .detail-grid,
            .invoice-info-grid {
                grid-template-columns: 1fr !important;
            }

            .invoice-brand-row {
                flex-direction: column !important;
            }

            .invoice-meta {
                text-align: left !important;
            }

            .invoice-total-box {
                width: 100% !important;
                max-width: none !important;
            }
        }


        /* ===== FINAL FIX: DETAIL SERVIS TETAP COMPACT WALAUPUN ADA BUKTI BAYAR ===== */
        /* Fokus patch ini: modal Detail Servis tidak melebar/meninggi full layar saat ada gambar bukti pembayaran. */
        .service-modal {
            padding: 18px !important;
            overflow: hidden !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .service-modal-box.detail-mode {
            width: min(90vw, 760px) !important;
            height: auto !important;
            max-height: min(88vh, 650px) !important;
            overflow: hidden !important;
            border-radius: 20px !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .service-modal-box.detail-mode #detail-modal-content,
        .service-modal-box.detail-mode .detail-modal-inner {
            width: 100% !important;
            height: auto !important;
            max-height: min(88vh, 650px) !important;
            overflow: hidden !important;
            display: flex !important;
            flex-direction: column !important;
            border-radius: 20px !important;
        }

        .service-modal-box.detail-mode .detail-topbar {
            padding: 10px 16px !important;
            flex: 0 0 auto !important;
        }

        .service-modal-box.detail-mode .detail-topbar h3 {
            font-size: 14.5px !important;
            line-height: 1.18 !important;
        }

        .service-modal-box.detail-mode .detail-topbar p {
            font-size: 10.5px !important;
            line-height: 1.2 !important;
            margin-top: 3px !important;
        }

        .service-modal-box.detail-mode .modal-close-btn {
            width: 28px !important;
            height: 28px !important;
            border-radius: 9px !important;
        }

        .service-modal-box.detail-mode .detail-body {
            flex: 0 1 auto !important;
            padding: 10px 16px !important;
            overflow: hidden !important;
        }

        .service-modal-box.detail-mode .detail-grid {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 7px !important;
            margin-bottom: 7px !important;
        }

        .service-modal-box.detail-mode .detail-card {
            padding: 7px 9px !important;
            border-radius: 11px !important;
        }

        .service-modal-box.detail-mode .detail-card h4,
        .service-modal-box.detail-mode .payment-proof-box h4,
        .service-modal-box.detail-mode .detail-note-area label {
            margin: 0 0 4px !important;
            font-size: 9px !important;
            line-height: 1.15 !important;
            letter-spacing: 0.24px !important;
        }

        .service-modal-box.detail-mode .detail-card p {
            margin: 2px 0 !important;
            font-size: 10.5px !important;
            line-height: 1.22 !important;
        }

        .service-modal-box.detail-mode .payment-proof-box {
            margin-top: 7px !important;
            padding: 7px 9px !important;
            border-radius: 11px !important;
            display: grid !important;
            grid-template-columns: auto 1fr !important;
            align-items: start !important;
            column-gap: 9px !important;
            row-gap: 4px !important;
            min-height: 0 !important;
            max-height: 118px !important;
            overflow: hidden !important;
        }

        .service-modal-box.detail-mode .payment-proof-box h4 {
            grid-column: 1 / -1 !important;
        }

        .service-modal-box.detail-mode .payment-proof-img {
            width: auto !important;
            max-width: 135px !important;
            max-height: 78px !important;
            object-fit: contain !important;
            border-radius: 8px !important;
            cursor: zoom-in !important;
            display: block !important;
        }

        .service-modal-box.detail-mode .payment-proof-link {
            font-size: 10px !important;
            margin-top: 0 !important;
        }

        .service-modal-box.detail-mode .payment-proof-hint {
            display: inline-flex !important;
            align-items: center !important;
            gap: 5px !important;
            align-self: center !important;
            color: var(--primary, #2563eb) !important;
            font-size: 10px !important;
            font-weight: 800 !important;
            line-height: 1.2 !important;
        }

        .service-modal-box.detail-mode .detail-note-area {
            margin-top: 7px !important;
            padding: 7px 9px !important;
            border-radius: 11px !important;
        }

        .service-modal-box.detail-mode .detail-note-textarea {
            height: 42px !important;
            min-height: 42px !important;
            max-height: 42px !important;
            resize: none !important;
            overflow: hidden !important;
            padding: 6px 8px !important;
            border-radius: 8px !important;
            font-size: 10.5px !important;
            line-height: 1.2 !important;
        }

        .service-modal-box.detail-mode .detail-actions {
            flex: 0 0 auto !important;
            padding: 8px 16px !important;
            gap: 6px !important;
        }

        .service-modal-box.detail-mode .detail-actions .btn-note-secondary,
        .service-modal-box.detail-mode .detail-actions .btn-note-primary {
            height: 28px !important;
            padding: 0 12px !important;
            border-radius: 9px !important;
            font-size: 11px !important;
        }

        /* Preview full bukti bayar dipisah dari card detail, jadi card detail tetap kecil */
        .proof-preview-modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 10050;
            background: rgba(0, 0, 0, 0.92);
            align-items: center;
            justify-content: center;
            padding: 18px 70px;
        }

        .proof-preview-modal.show {
            display: flex;
        }

        .proof-preview-modal img {
            max-width: 100%;
            max-height: 96vh;
            object-fit: contain;
            border-radius: 4px;
            background: #ffffff;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
        }

        .proof-preview-close {
            position: absolute;
            top: 18px;
            right: 22px;
            width: 42px;
            height: 42px;
            border-radius: 999px;
            border: none;
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .proof-preview-open {
            position: absolute;
            top: 18px;
            left: 22px;
            height: 36px;
            padding: 0 13px;
            border-radius: 999px;
            border: none;
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 800;
            font-family: inherit;
        }

        .proof-preview-close:hover,
        .proof-preview-open:hover {
            background: rgba(255, 255, 255, 0.22);
        }

        @media (max-width: 768px) {
            .service-modal-box.detail-mode {
                width: 94vw !important;
                max-height: 90vh !important;
            }

            .service-modal-box.detail-mode .detail-grid {
                grid-template-columns: 1fr !important;
            }

            .service-modal-box.detail-mode .payment-proof-box {
                grid-template-columns: 1fr !important;
                max-height: 142px !important;
            }

            .service-modal-box.detail-mode .payment-proof-img {
                max-width: 125px !important;
                max-height: 74px !important;
            }

            .proof-preview-modal {
                padding: 58px 12px 18px;
            }

            .proof-preview-open {
                top: 14px;
                left: 14px;
                height: 32px;
                font-size: 11px;
            }

            .proof-preview-close {
                top: 10px;
                right: 12px;
                width: 38px;
                height: 38px;
            }
        }

    </style>
@endpush

@push('scripts')
    <script>
        let currentFilterStatus = '';
        let currentPage = 1;
        let perPage = 4;
        let lastPage = 1;
        let searchDebounceTimeout = null;
        let activeQuickDate = null;
        let servisCache = {};

        document.addEventListener('DOMContentLoaded', () => {
            if (!isLoggedIn() || !isAdmin()) {
                window.location.href = '/';
                return;
            }
            lucide.createIcons();
            loadServis(1);
        });

        function setFilterStatus(status) {
            currentFilterStatus = status;
            document.getElementById('filter-status').value = status;
            currentPage = 1;
            loadServis(1);
        }

        function debounceFilter() {
            if (searchDebounceTimeout) clearTimeout(searchDebounceTimeout);
            searchDebounceTimeout = setTimeout(() => {
                currentPage = 1;
                loadServis(1);
            }, 300);
        }

        function formatDateInput(date) {
            const pad = number => String(number).padStart(2, '0');
            return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
        }

        function setQuickDate(type) {
            const startEl = document.getElementById('filter-start');
            const endEl = document.getElementById('filter-end');
            const today = new Date();

            document.querySelectorAll('.quick-filter-btn').forEach(btn => btn.classList.remove('active'));

            if (activeQuickDate === type) {
                activeQuickDate = null;
                startEl.value = '';
                endEl.value = '';
            } else {
                activeQuickDate = type;
                document.getElementById('qf-' + type).classList.add('active');

                let start = new Date(today);
                let end = new Date(today);

                if (type === 'week') {
                    const day = today.getDay() || 7;
                    start = new Date(today.getFullYear(), today.getMonth(), today.getDate() - day + 1);
                    end = new Date(today.getFullYear(), today.getMonth(), today.getDate());
                } else if (type === 'month') {
                    start = new Date(today.getFullYear(), today.getMonth(), 1);
                    end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                }

                startEl.value = formatDateInput(start);
                endEl.value = formatDateInput(end);
            }

            currentPage = 1;
            loadServis(1);
        }

        function normalizeServiceStatus(status = '') {
            const value = String(status || '').toLowerCase().trim().replace(/\s+/g, '_').replace(/-/g, '_');
            const aliases = {
                menunggu: 'menunggu',
                menunggu_konfirmasi: 'menunggu',
                menunggu_confirm: 'menunggu',
                menunggu_dikonfirmasi: 'menunggu',
                belum_dikonfirmasi: 'menunggu',

                diperiksa: 'diperiksa',
                sedang_diperiksa: 'diperiksa',
                dalam_pengecekan: 'diperiksa',
                pengecekan: 'diperiksa',
                dicek: 'diperiksa',

                dikerjakan: 'dikerjakan',
                sedang_dikerjakan: 'dikerjakan',
                proses: 'dikerjakan',
                diproses: 'dikerjakan',

                selesai: 'selesai',

                diambil: 'diambil',
                siap_diambil: 'diambil',
                sudah_diambil: 'diambil'
            };
            return aliases[value] || value;
        }

        function getApiStatusParam(status = '') {
            const normalized = normalizeServiceStatus(status);
            const params = {
                menunggu: 'menunggu',
                diperiksa: 'diperiksa',
                dikerjakan: 'dikerjakan',
                selesai: 'selesai',
                diambil: 'diambil'
            };
            return params[normalized] || status;
        }

        function isStatusMatch(actualStatus, selectedStatus) {
            if (!selectedStatus) return true;
            return normalizeServiceStatus(actualStatus) === normalizeServiceStatus(selectedStatus);
        }

        function buildServisQueryParams({ page = 1, limit = perPage, includeStatus = true } = {}) {
            const status = document.getElementById('filter-status').value;
            const search = document.getElementById('filter-search').value;
            const start = document.getElementById('filter-start').value;
            const end = document.getElementById('filter-end').value;

            const params = new URLSearchParams({ page, per_page: limit });
            if (includeStatus && status) params.append('status', getApiStatusParam(status));
            if (search) params.append('search', search);
            if (start) params.append('dari_tanggal', start);
            if (end) params.append('sampai_tanggal', end);
            return params;
        }

        async function loadServisStatusFallback(page = 1, selectedStatus = '') {
            const params = buildServisQueryParams({ page: 1, limit: 1000, includeStatus: false });
            const res = await apiFetch('/servis?' + params.toString());
            const sourceData = res.data || {};
            const sourceList = sourceData.data || [];
            const filtered = sourceList.filter(item => isStatusMatch(item.status, selectedStatus));
            const total = filtered.length;
            const startIndex = (page - 1) * perPage;
            const endIndex = startIndex + perPage;
            const pageList = filtered.slice(startIndex, endIndex);

            return {
                res,
                data: {
                    data: pageList,
                    total,
                    from: total ? startIndex + 1 : 0,
                    to: Math.min(endIndex, total),
                    current_page: page,
                    last_page: Math.max(1, Math.ceil(total / perPage))
                }
            };
        }


        async function loadStatsFromDatabase() {
            try {
                const params = buildServisQueryParams({ page: 1, limit: 1000, includeStatus: false });
                const res = await apiFetch('/servis?' + params.toString());
                const data = res.data || {};
                const list = Array.isArray(data.data) ? data.data : [];
                renderStatsFromList(list, Number(data.total || list.length || 0));
            } catch (e) {
                console.warn('Gagal menghitung statistik servis dari database:', e);
            }
        }

        function renderStatsFromList(list = [], totalFromApi = 0) {
            const counters = {
                menunggu: 0,
                diperiksa: 0,
                dikerjakan: 0,
                selesai: 0,
                diambil: 0
            };

            let totalPendapatanSelesai = 0;

            list.forEach(item => {
                const status = normalizeServiceStatus(item.status || '');
                if (Object.prototype.hasOwnProperty.call(counters, status)) {
                    counters[status] += 1;
                }

                // Pendapatan dihitung hanya dari servis yang statusnya selesai.
                // Nilai diambil dari field biaya yang sudah ada di database melalui helper getServiceAmount().
                if (status === 'selesai') {
                    totalPendapatanSelesai += getServiceAmount(item);
                }
            });

            const total = Number(totalFromApi || list.length || 0);
            document.getElementById('stat-total').textContent = total;
            document.getElementById('stat-menunggu').textContent = counters.menunggu;
            document.getElementById('stat-diperiksa').textContent = counters.diperiksa;
            document.getElementById('stat-dikerjakan').textContent = counters.dikerjakan;
            document.getElementById('stat-selesai').textContent = counters.selesai;

            const revenueEl = document.getElementById('stat-pendapatan-selesai');
            if (revenueEl) {
                revenueEl.textContent = formatRupiah(totalPendapatanSelesai);
                revenueEl.title = formatRupiah(totalPendapatanSelesai);
            }
        }

        async function loadServis(page = 1) {
            currentPage = page;

            const wrapper = document.getElementById('servis-table-wrapper');
            const status = document.getElementById('filter-status').value;

            currentFilterStatus = normalizeServiceStatus(status);
            wrapper.classList.toggle('rows-4', perPage <= 4);
            wrapper.classList.toggle('rows-more', perPage > 4);
            wrapper.innerHTML = '<div class="flex-center compact-loader"><div class="loader"></div></div>';

            try {
                let params = buildServisQueryParams({ page: currentPage, limit: perPage, includeStatus: true });
                let res = await apiFetch('/servis?' + params.toString());

                if (res.status === 'success') {
                    let data = res.data || {};
                    let list = data.data || [];

                    if (status && list.length === 0) {
                        const fallback = await loadServisStatusFallback(currentPage, status);
                        res = fallback.res;
                        data = fallback.data;
                        list = data.data || [];
                    }

                    lastPage = data.last_page || 1;
                    servisCache = {};
                    renderTable(list);
                    renderPagination(data);
                    await loadStatsFromDatabase();
                    if (typeof loadSidebarCounts === 'function') loadSidebarCounts();
                }
            } catch (err) {
                wrapper.innerHTML = `
                        <div class="error-state">
                            <div class="empty-icon-wrapper"><i data-lucide="alert-circle" class="empty-icon"></i></div>
                            <h4>Gagal Memuat Data</h4>
                            <p>${escapeHtml(err.message)}</p>
                        </div>
                    `;
            } finally {
                lucide.createIcons();
            }
        }

        function formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(Number(amount || 0));
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function firstFilledValue(values = []) {
            return values.find(value => value !== undefined && value !== null && String(value).trim() !== '');
        }

        function formatDateTime(value) {
            if (!value) return '—';
            const obj = new Date(value);
            if (isNaN(obj.getTime())) return '—';
            return obj.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) +
                ', ' + obj.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
        }

        function formatDateShort(value) {
            if (!value) return { date: '—', time: '—' };
            const obj = new Date(value);
            if (isNaN(obj.getTime())) return { date: '—', time: '—' };
            return {
                date: obj.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }),
                time: obj.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB'
            };
        }

        function getBadgeClass(status) {
            switch (normalizeServiceStatus(status)) {
                case 'menunggu': return 'badge-warning';
                case 'diperiksa': return 'badge-primary';
                case 'dikerjakan': return 'badge-accent';
                case 'selesai': return 'badge-success';
                case 'diambil': return 'badge-muted';
                default: return 'badge-muted';
            }
        }

        function getStatusLabel(status) {
            const labels = {
                menunggu: 'Menunggu',
                diperiksa: 'Diperiksa',
                dikerjakan: 'Dikerjakan',
                selesai: 'Selesai',
                diambil: 'Diambil'
            };
            const normalized = normalizeServiceStatus(status);
            return labels[normalized] || status || '—';
        }

        function getServiceCode(s = {}) {
            return firstFilledValue([s.kode_servis, s.kode, s.service_code, s.nomor_servis, `SRV-${String(s.id || '').padStart(4, '0')}`]) || '—';
        }

        function getDeviceName(s = {}) {
            return firstFilledValue([
                s.nama_perangkat,
                s.perangkat,
                s.model_laptop,
                s.tipe_laptop,
                s.laptop_model,
                s.merek_laptop,
                s.brand_laptop
            ]) || '—';
        }

        function getSerialNumber(s = {}) {
            return firstFilledValue([s.serial_number, s.no_seri, s.sn, s.kode_perangkat, s.nomor_seri]) || '';
        }

        function getIssueDescription(s = {}) {
            return firstFilledValue([s.keluhan, s.deskripsi_kerusakan, s.detail_kerusakan, s.deskripsi, s.catatan_pelanggan]) || '';
        }

        function getTechnicianName(s = {}) {
            return firstFilledValue([
                s.teknisi?.nama,
                s.teknisi?.name,
                s.nama_teknisi,
                s.technician_name,
                s.admin?.nama,
                s.admin?.name
            ]) || 'Belum ditentukan';
        }

        function getCustomerName(s = {}) {
            return firstFilledValue([s.pengguna?.nama, s.pengguna?.name, s.nama_pelanggan, s.customer_name]) || '—';
        }

        function getCustomerPhone(s = {}) {
            return firstFilledValue([s.pengguna?.no_hp, s.pengguna?.phone, s.no_hp, s.telepon, s.phone]) || '—';
        }

        function getCustomerEmail(s = {}) {
            return firstFilledValue([s.pengguna?.email, s.email, s.customer_email]) || '—';
        }

        function getCustomerAddress(s = {}) {
            return firstFilledValue([
                s.alamat,
                s.alamat_pelanggan,
                s.alamat_pengambilan,
                s.detail_alamat,
                s.pengguna?.alamat,
                s.pengguna?.alamat_lengkap,
                s.pengguna?.address
            ]) || '—';
        }

        function getServiceAmount(s = {}) {
            return Number(firstFilledValue([s.total_biaya, s.biaya_total, s.biaya_akhir, s.estimasi_biaya, s.estimasi, s.biaya_estimasi, s.total_pembayaran]) || 0);
        }

        function getPaymentMethod(s = {}) {
            return firstFilledValue([
                s.metode_pembayaran,
                s.payment_method,
                s.metode,
                s.pembayaran?.metode_pembayaran,
                s.pembayaran?.metode,
                s.payment?.method
            ]) || 'Belum dipilih';
        }

        function getPaymentDate(s = {}) {
            return firstFilledValue([
                s.waktu_pembayaran,
                s.tanggal_pembayaran,
                s.paid_at,
                s.payment_at,
                s.pembayaran?.created_at,
                s.pembayaran?.waktu_pembayaran
            ]) || '';
        }

        function getPaymentProofUrl(s = {}) {
            const raw = firstFilledValue([
                s.bukti_pembayaran_url,
                s.bukti_pembayaran,
                s.payment_proof_url,
                s.payment_proof,
                s.bukti_transfer,
                s.pembayaran?.bukti_pembayaran_url,
                s.pembayaran?.bukti_pembayaran
            ]);

            if (!raw) return '';
            const value = String(raw);
            if (value.startsWith('http') || value.startsWith('/')) return value;
            if (value.startsWith('storage/')) return '/' + value;
            return '/storage/' + value;
        }

        function getPaymentStatus(s = {}) {
            const proof = getPaymentProofUrl(s);
            const rawStatus = String(firstFilledValue([
                s.status_pembayaran,
                s.payment_status,
                s.pembayaran_status,
                s.pembayaran?.status,
                s.payment?.status
            ]) || '').toLowerCase();

            if (['dibayar', 'lunas', 'paid', 'success', 'settlement', 'sukses'].includes(rawStatus)) return 'dibayar';
            if (['pending', 'menunggu', 'belum_dibayar', 'unpaid'].includes(rawStatus)) return 'pending';
            if (proof) return 'dibayar';
            return 'pending';
        }

        function getPaymentLabel(s = {}) {
            return getPaymentStatus(s) === 'dibayar' ? 'Dibayar' : 'Belum Dibayar';
        }

        function renderTable(data = []) {
            const wrapper = document.getElementById('servis-table-wrapper');

            if (!data || data.length === 0) {
                wrapper.innerHTML = `
                        <div class="empty-state">
                            <div class="empty-icon-wrapper"><i data-lucide="inbox" class="empty-icon"></i></div>
                            <h4>Tidak Ada Servis Ditemukan</h4>
                            <p>Coba sesuaikan filter atau rentang tanggal Anda.</p>
                        </div>
                    `;
                return;
            }

            let html = `
                    <table class="service-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Perangkat</th>
                                <th>Kerusakan</th>
                                <th>Estimasi</th>
                                <th style="text-align:center;">Status</th>
                                <th>Pembayaran</th>
                                <th style="text-align:right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

            data.forEach(s => {
                servisCache[s.id] = s;

                const date = formatDateShort(s.created_at);
                const kode = getServiceCode(s);
                const namaPelanggan = getCustomerName(s);
                const noHp = getCustomerPhone(s);
                const perangkat = getDeviceName(s);
                const serial = getSerialNumber(s);
                const kerusakan = firstFilledValue([s.jenis_kerusakan, s.kerusakan, s.damage_type]) || '—';
                const keluhan = getIssueDescription(s);
                const estimasi = firstFilledValue([s.estimasi_biaya, s.estimasi, s.biaya_estimasi]) || 0;
                const status = normalizeServiceStatus(s.status || 'menunggu');
                const teknisi = getTechnicianName(s);
                const catatan = s.keterangan || s.catatan_teknisi || 'Belum ada catatan';
                const paymentStatus = getPaymentStatus(s);
                const paymentLabel = getPaymentLabel(s);
                const paymentMethod = getPaymentMethod(s);
                const paymentDate = getPaymentDate(s);
                const paymentDateLabel = paymentDate ? formatDateTime(paymentDate) : 'Menunggu pembayaran';

                html += `
                        <tr>
                            <td>
                                <span class="service-code">${escapeHtml(kode)}</span>
                            </td>
                            <td>
                                <div class="stack-sm">
                                    <span class="date-main">${date.date}</span>
                                    <span class="date-sub">${date.time}</span>
                                </div>
                            </td>
                            <td>
                                <div class="stack-sm">
                                    <span class="customer-name line-clamp-1" title="${escapeHtml(namaPelanggan)}">${escapeHtml(namaPelanggan)}</span>
                                    <span class="customer-phone line-clamp-1">${escapeHtml(noHp)}</span>
                                </div>
                            </td>
                            <td>
                                <div class="stack-sm">
                                    <span class="device-name line-clamp-1" title="${escapeHtml(perangkat)}">${escapeHtml(perangkat)}</span>
                                    ${serial ? `<span class="device-sn line-clamp-1" title="SN: ${escapeHtml(serial)}">SN: ${escapeHtml(serial)}</span>` : ''}
                                </div>
                            </td>
                            <td>
                                <div class="stack-sm">
                                    <span class="issue-title line-clamp-1" title="${escapeHtml(kerusakan)}">${escapeHtml(kerusakan)}</span>
                                    ${keluhan ? `<span class="issue-desc line-clamp-2" title="${escapeHtml(keluhan)}">${escapeHtml(keluhan)}</span>` : ''}
                                </div>
                            </td>
                            <td>
                                <span class="estimate-price">${formatRupiah(estimasi)}</span>
                            </td>
                            <td style="text-align:center;">
                                <span class="status-badge-modern ${getBadgeClass(status)}">${escapeHtml(getStatusLabel(status))}</span>
                            </td>
                            <td>
                                <div class="stack-sm payment-summary-cell">
                                    <span class="payment-status-badge ${paymentStatus === 'dibayar' ? 'paid' : 'pending'}">${escapeHtml(paymentLabel)}</span>
                                    <span class="payment-method-text line-clamp-1" title="${escapeHtml(paymentMethod)}">${escapeHtml(paymentMethod)}</span>
                                    <span class="payment-date-text line-clamp-1">${escapeHtml(paymentDateLabel)}</span>
                                </div>
                            </td>
                            <td style="text-align:right;">
                                <div class="action-cell">
                                    <select class="status-select-mini" data-current="${escapeHtml(status)}" onchange="updateStatusServis(${s.id}, this.value, this)">
                                        <option value="">Ubah Status</option>
                                        <option value="menunggu" ${status === 'menunggu' ? 'selected' : ''}>Menunggu</option>
                                        <option value="diperiksa" ${status === 'diperiksa' ? 'selected' : ''}>Diperiksa</option>
                                        <option value="dikerjakan" ${status === 'dikerjakan' ? 'selected' : ''}>Dikerjakan</option>
                                        <option value="selesai" ${status === 'selesai' ? 'selected' : ''}>Selesai</option>
                                        <option value="diambil" ${status === 'diambil' ? 'selected' : ''}>Diambil</option>
                                    </select>
                                    <div class="action-row">
                                        <button class="btn-detail-mini" type="button" title="Detail dan tambah catatan" onclick="showDetailServis(${s.id})">
                                            <i data-lucide="file-text" class="icon-xs"></i> Detail
                                        </button>
                                        <button class="btn-invoice-mini" type="button" title="Invoice servis" onclick="showInvoiceServis(${s.id})">
                                            <i data-lucide="receipt-text" class="icon-xs"></i> Invoice
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `;
            });

            html += `</tbody></table>`;
            wrapper.innerHTML = html;
        }

        function renderPagination(pageData = {}) {
            const wrapper = document.getElementById('pagination-wrapper');
            const total = Number(pageData.total || 0);

            if (total === 0) {
                wrapper.innerHTML = '';
                return;
            }

            const from = pageData.from || ((currentPage - 1) * perPage + 1);
            const to = pageData.to || Math.min(currentPage * perPage, total);
            const activePage = pageData.current_page || currentPage;
            const totalPages = pageData.last_page || lastPage || 1;
            lastPage = totalPages;

            let linksHtml = '';
            const createBtn = (page, label, isActive = false, isDisabled = false) => `
                    <button class="page-btn ${isActive ? 'active' : ''} ${isDisabled ? 'disabled' : ''}"
                        ${isDisabled ? 'disabled' : `onclick="loadServis(${page})"`}>
                        ${label}
                    </button>
                `;

            linksHtml += createBtn(activePage - 1, '<i data-lucide="chevron-left" class="icon-xs"></i>', false, activePage <= 1);

            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || Math.abs(activePage - i) <= 1) {
                    linksHtml += createBtn(i, i, i === activePage);
                } else if (Math.abs(activePage - i) === 2) {
                    linksHtml += `<span class="page-ellipsis">...</span>`;
                }
            }

            linksHtml += createBtn(activePage + 1, '<i data-lucide="chevron-right" class="icon-xs"></i>', false, activePage >= totalPages);

            wrapper.innerHTML = `
                    <div class="pagination-info">Menampilkan ${from}-${to} dari ${total} servis</div>
                    <div class="pagination-actions">
                        <div class="pagination-controls">${linksHtml}</div>
                        <select class="rows-per-page" onchange="changePerPage(this.value)">
                            <option value="4" ${perPage == 4 ? 'selected' : ''}>4 / halaman</option>
                            <option value="10" ${perPage == 10 ? 'selected' : ''}>10 / halaman</option>
                            <option value="25" ${perPage == 25 ? 'selected' : ''}>25 / halaman</option>
                            <option value="50" ${perPage == 50 ? 'selected' : ''}>50 / halaman</option>
                        </select>
                    </div>
                `;
            lucide.createIcons();
        }

        function changePerPage(value) {
            perPage = parseInt(value, 10) || 4;
            loadServis(1);
        }

        function renderStats(stats = {}, pageData = {}) {
            // Statistik sekarang dihitung dari data servis asli agar jumlah setiap status tidak dobel.
            // Function ini tetap ada untuk menjaga kompatibilitas jika ada pemanggilan lama.
            loadStatsFromDatabase();
        }

        async function loadStatusStatFallback(status, elementId) {
            // Kompatibilitas lama: ambil ulang statistik dari database, bukan dari stats backend yang bisa dobel.
            await loadStatsFromDatabase();
        }

        async function loadDiperiksaStatFallback() {
            return loadStatusStatFallback('diperiksa', 'stat-diperiksa');
        }

        async function updateStatusServis(id, status, selectEl) {
            if (!status) return;
            const oldValue = selectEl.getAttribute('data-current') || '';
            selectEl.disabled = true;

            try {
                await apiFetch(`/servis/${id}/status`, {
                    method: 'PATCH',
                    body: { status: status }
                });
                showToast('Status servis berhasil diperbarui', 'success');
                loadServis(currentPage);
            } catch (err) {
                showToast(err.message, 'error');
                selectEl.value = oldValue;
            } finally {
                selectEl.disabled = false;
            }
        }

        function openKetModal(servisId) {
            const servis = servisCache[servisId] || {};
            document.getElementById('ket-servis-id').value = servisId;
            document.getElementById('ket-input').value = servis.keterangan || servis.catatan_teknisi || '';
            const modal = document.getElementById('keterangan-modal');
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('show'), 10);
            lucide.createIcons();
        }

        async function saveKeterangan() {
            const id = document.getElementById('ket-servis-id').value;
            const ket = document.getElementById('ket-input').value;

            try {
                await apiFetch(`/servis/${id}/status`, {
                    method: 'PATCH',
                    body: { keterangan: ket }
                });
                showToast('Keterangan berhasil disimpan!', 'success');
                closeKetModal();
                loadServis(currentPage);
            } catch (e) {
                showToast(e.message, 'error');
            }
        }

        function closeKetModal(e = null) {
            const modal = document.getElementById('keterangan-modal');
            if (!e || e.target === modal) {
                modal.classList.remove('show');
                setTimeout(() => { modal.style.display = 'none'; }, 180);
            }
        }

        function showDetailServis(id) {
            const s = servisCache[id];
            if (!s) {
                showToast('Data servis tidak ditemukan. Coba muat ulang halaman.', 'error');
                return;
            }

            const kode = getServiceCode(s);
            const namaPelanggan = getCustomerName(s);
            const noHp = getCustomerPhone(s);
            const email = getCustomerEmail(s);
            const alamat = getCustomerAddress(s);
            const perangkat = getDeviceName(s);
            const serial = getSerialNumber(s) || '—';
            const kerusakan = firstFilledValue([s.jenis_kerusakan, s.kerusakan, s.damage_type]) || '—';
            const keluhan = getIssueDescription(s) || '—';
            const teknisi = getTechnicianName(s);
            const catatan = s.keterangan || s.catatan_teknisi || '';
            const status = getStatusLabel(s.status || 'menunggu');
            const estimasi = getServiceAmount(s);
            const paymentStatus = getPaymentStatus(s);
            const paymentLabel = getPaymentLabel(s);
            const paymentMethod = getPaymentMethod(s);
            const paymentDate = getPaymentDate(s);
            const paymentProofUrl = getPaymentProofUrl(s);

            const html = `
                    <div class="detail-modal-inner">
                        <div class="detail-topbar">
                            <div>
                                <h3>Detail Servis ${escapeHtml(kode)}</h3>
                                <p>Informasi servis dan catatan teknisi.</p>
                            </div>
                            <button class="modal-close-btn" type="button" onclick="closeDetailModal()">
                                <i data-lucide="x" class="icon-sm"></i>
                            </button>
                        </div>
                        <div class="detail-body">
                            <div class="detail-grid">
                                <div class="detail-card">
                                    <h4>Data Pelanggan</h4>
                                    <p><strong>${escapeHtml(namaPelanggan)}</strong></p>
                                    <p>No. HP: ${escapeHtml(noHp)}</p>
                                    <p>Email: ${escapeHtml(email)}</p>
                                </div>
                                <div class="detail-card">
                                    <h4>Data Perangkat</h4>
                                    <p><strong>${escapeHtml(perangkat)}</strong></p>
                                    <p>Kode Servis: ${escapeHtml(kode)}</p>
                                    <p>Status: <strong>${escapeHtml(status)}</strong></p>
                                    <p>Tanggal Masuk: ${formatDateTime(s.created_at)}</p>
                                </div>
                                <div class="detail-card">
                                    <h4>Kerusakan & Estimasi</h4>
                                    <p>Kerusakan: <strong>${escapeHtml(kerusakan)}</strong></p>
                                    <p>Keluhan: ${escapeHtml(keluhan)}</p>
                                    <p>Estimasi: <strong>${formatRupiah(estimasi)}</strong></p>
                                </div>
                                <div class="detail-card">
                                    <h4>Pembayaran</h4>
                                    <p>Status: <strong>${escapeHtml(paymentLabel)}</strong></p>
                                    <p>Metode: ${escapeHtml(paymentMethod)}</p>
                                    <p>Waktu: ${escapeHtml(paymentDate ? formatDateTime(paymentDate) : 'Belum ada pembayaran')}</p>
                                </div>
                            </div>

                            <div class="payment-proof-box">
                                <h4>Bukti Pembayaran dari User</h4>
                                ${paymentProofUrl ? `
                                    <img src="${escapeHtml(paymentProofUrl)}" alt="Bukti Pembayaran Servis" class="payment-proof-img" title="Klik untuk melihat bukti pembayaran full" onclick="openPaymentProofPreview('${encodeURIComponent(paymentProofUrl)}')" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                                    <a href="${escapeHtml(paymentProofUrl)}" target="_blank" class="payment-proof-link" style="display:none;">
                                        <i data-lucide="external-link" class="icon-xs"></i> Buka bukti pembayaran
                                    </a>
                                    <span class="payment-proof-hint"><i</span>
                                ` : `<p style="margin:0; color:#64748b; font-size:12px;">Belum ada bukti pembayaran yang diunggah user.</p>`}
                            </div>

                            <div class="detail-note-area">
                                <label for="detail-catatan-input">Tambah / Update Catatan</label>
                                <textarea id="detail-catatan-input" class="detail-note-textarea" placeholder="Contoh: IC power diganti, laptop sudah normal kembali...">${escapeHtml(catatan)}</textarea>
                            </div>
                        </div>
                        <div class="detail-actions">
                            <button type="button" class="btn-note-secondary" onclick="closeDetailModal()">
                                <i data-lucide="x" class="icon-xs"></i> Tutup
                            </button>
                            <button type="button" class="btn-note-primary" onclick="saveKeteranganFromDetail(${s.id})">
                                <i data-lucide="save" class="icon-xs"></i> Simpan Catatan
                            </button>
                        </div>
                    </div>
                `;

            document.getElementById('detail-modal-content').innerHTML = html;
            const modal = document.getElementById('detail-modal');
            const modalBox = modal.querySelector('.service-modal-box');
            if (modalBox) {
                modalBox.classList.remove('invoice-mode');
                modalBox.classList.add('detail-mode');
            }
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('show'), 10);
            lucide.createIcons();
        }

        async function saveKeteranganFromDetail(id) {
            const textarea = document.getElementById('detail-catatan-input');
            const ket = textarea ? textarea.value : '';

            try {
                await apiFetch(`/servis/${id}/status`, {
                    method: 'PATCH',
                    body: { keterangan: ket }
                });
                showToast('Catatan servis berhasil disimpan!', 'success');
                closeDetailModal();
                loadServis(currentPage);
            } catch (e) {
                showToast(e.message, 'error');
            }
        }

        function showInvoiceServis(id) {
            const s = servisCache[id];
            if (!s) {
                showToast('Data servis tidak ditemukan. Coba muat ulang halaman.', 'error');
                return;
            }

            const kode = getServiceCode(s);
            const namaPelanggan = getCustomerName(s);
            const noHp = getCustomerPhone(s);
            const email = getCustomerEmail(s);
            const alamat = getCustomerAddress(s);
            const perangkat = getDeviceName(s);
            const serial = getSerialNumber(s) || '—';
            const kerusakan = firstFilledValue([s.jenis_kerusakan, s.kerusakan, s.damage_type]) || '—';
            const keluhan = getIssueDescription(s) || '—';
            const teknisi = getTechnicianName(s);
            const catatan = s.keterangan || s.catatan_teknisi || 'Belum ada catatan';
            const status = getStatusLabel(s.status || 'menunggu');
            const totalBiaya = getServiceAmount(s);
            const paymentLabel = getPaymentLabel(s);
            const paymentMethod = getPaymentMethod(s);
            const paymentDate = getPaymentDate(s);

            const html = `
                    <div class="invoice-modal-inner">
                        <div class="detail-topbar">
                            <div>
                                <h3>Invoice Servis ${escapeHtml(kode)}</h3>
                                <p>Invoice layanan servis laptop pelanggan.</p>
                            </div>
                            <button class="modal-close-btn" type="button" onclick="closeDetailModal()">
                                <i data-lucide="x" class="icon-sm"></i>
                            </button>
                        </div>

                        <div class="invoice-body" id="invoice-print-area">
                            <div class="invoice-brand-row">
                                <div class="invoice-brand">
                                    <h2>Defkan Computer</h2>
                                    <p>Invoice Layanan Servis Laptop.</p>
                                </div>
                                <div class="invoice-meta">
                                    <strong>INV-SRV-${escapeHtml(kode)}</strong>
                                    <p>Tanggal: ${formatDateTime(s.created_at)}</p>
                                    <p>Status: ${escapeHtml(status)}</p>
                                </div>
                            </div>

                            <div class="invoice-info-grid">
                                <div class="invoice-info-card">
                                    <h4>Data Pelanggan</h4>
                                    <p><strong>${escapeHtml(namaPelanggan)}</strong></p>
                                    <p>No. HP: ${escapeHtml(noHp)}</p>
                                    <p>Email: ${escapeHtml(email)}</p>
                                </div>
                                <div class="invoice-info-card">
                                    <h4>Data Servis</h4>
                                    <p>Perangkat: <strong>${escapeHtml(perangkat)}</strong></p>
                                    <p>Kode Servis: ${escapeHtml(kode)}</p>
                                    <p>Pembayaran: <strong>${escapeHtml(paymentLabel)}</strong></p>
                                    <p>Metode: ${escapeHtml(paymentMethod)}</p>
                                    <p>Waktu Bayar: ${escapeHtml(paymentDate ? formatDateTime(paymentDate) : 'Belum ada pembayaran')}</p>
                                    <p>Catatan: ${escapeHtml(catatan)}</p>
                                </div>
                            </div>

                            <table class="invoice-items-table">
                                <thead>
                                    <tr>
                                        <th style="text-align:center; width:42px;">No</th>
                                        <th>Layanan</th>
                                        <th style="width:150px;">Kerusakan</th>
                                        <th style="text-align:center; width:82px;">Garansi</th>
                                        <th style="text-align:right; width:130px;">Biaya</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="text-align:center; font-weight:900;">1</td>
                                        <td>
                                            <strong>Servis ${escapeHtml(perangkat)}</strong><br>
                                            <span style="color:#64748b; font-size:11px;">${escapeHtml(keluhan)}</span>
                                        </td>
                                        <td>${escapeHtml(kerusakan)}</td>
                                        <td style="text-align:center; color:#2563eb; font-weight:900;">1 Bulan</td>
                                        <td style="text-align:right; font-weight:900; white-space:nowrap;">${formatRupiah(totalBiaya)}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="invoice-warranty-note">
                                <strong>Garansi Servis:</strong> Garansi toko selama <strong>1 bulan</strong> berlaku untuk pengerjaan servis sesuai catatan teknisi.
                            </div>

                            <div class="invoice-total-row">
                                <div class="invoice-total-box">
                                    <div class="invoice-total-line">
                                        <span>Total Servis</span>
                                        <strong>${formatRupiah(totalBiaya)}</strong>
                                    </div>
                                    <div class="invoice-total-line grand">
                                        <span>Grand Total</span>
                                        <span>${formatRupiah(totalBiaya)}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="detail-actions">
                            <button type="button" class="btn-note-secondary" onclick="closeDetailModal()">
                                <i data-lucide="x" class="icon-xs"></i> Tutup
                            </button>
                            <button type="button" class="btn-note-primary" onclick="printServiceInvoice()">
                                <i data-lucide="printer" class="icon-xs"></i> Cetak Invoice
                            </button>
                        </div>
                    </div>
                `;

            document.getElementById('detail-modal-content').innerHTML = html;
            const modal = document.getElementById('detail-modal');
            const modalBox = modal.querySelector('.service-modal-box');
            if (modalBox) {
                modalBox.classList.remove('detail-mode');
                modalBox.classList.add('invoice-mode');
            }
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('show'), 10);
            lucide.createIcons();
        }

        function printServiceInvoice() {
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
                        <title>Invoice Servis</title>
                        <style>
                            body { font-family: Arial, sans-serif; margin: 24px; color: #0f172a; }
                            h2 { margin: 0; color: #2563eb; }
                            p { margin: 4px 0; color: #475569; font-size: 12px; }
                            table { width: 100%; border-collapse: collapse; margin-top: 14px; font-size: 12px; }
                            th, td { border: 1px solid #e5e7eb; padding: 10px; text-align: left; }
                            th { background: #f8fafc; color: #475569; text-transform: uppercase; font-size: 11px; }
                            .invoice-brand-row { display: flex; justify-content: space-between; gap: 12px; margin-bottom: 14px; }
                            .invoice-meta { text-align: right; }
                            .invoice-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 14px; }
                            .invoice-info-card { border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px; }
                            .invoice-info-card h4 { margin: 0 0 8px; font-size: 11px; color: #64748b; text-transform: uppercase; }
                            .invoice-warranty-note { margin-top: 10px; padding: 10px; border: 1px solid #bfdbfe; background: #eff6ff; color: #1d4ed8; border-radius: 8px; font-size: 12px; }
                            .invoice-total-row { display: flex; justify-content: flex-end; margin-top: 14px; }
                            .invoice-total-box { min-width: 270px; border: 1px solid #e5e7eb; }
                            .invoice-total-line { display: flex; justify-content: space-between; padding: 10px 12px; }
                            .invoice-total-line.grand { background: #eff6ff; color: #2563eb; font-weight: 800; }
                        

        /* ===== PATCH FINAL: INVOICE FIXED TANPA SCROLL + CLOSE TANPA EFEK KOTAK ===== */
        .service-modal,
        .service-modal.show,
        .service-modal-box,
        .detail-modal-inner,
        .invoice-modal-inner {
            transition: none !important;
            animation: none !important;
        }

        /* Khusus modal invoice: isi dibuat fixed dan tidak scroll internal */
        .service-modal-box.invoice-mode {
            width: min(88vw, 900px) !important;
            height: auto !important;
            max-height: 86vh !important;
            overflow: hidden !important;
        }

        .service-modal-box.invoice-mode #detail-modal-content,
        .service-modal-box.invoice-mode .invoice-modal-inner {
            height: auto !important;
            max-height: 86vh !important;
            overflow: hidden !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .service-modal-box.invoice-mode .detail-topbar {
            padding: 9px 16px !important;
            flex: 0 0 auto !important;
        }

        .service-modal-box.invoice-mode .detail-topbar h3 {
            font-size: 14.5px !important;
            line-height: 1.15 !important;
        }

        .service-modal-box.invoice-mode .detail-topbar p {
            font-size: 10.5px !important;
            margin-top: 2px !important;
        }

        .service-modal-box.invoice-mode .invoice-body {
            flex: 0 0 auto !important;
            overflow: hidden !important;
            overflow-y: hidden !important;
            overflow-x: hidden !important;
            padding: 10px 16px !important;
        }

        .service-modal-box.invoice-mode .invoice-brand-row {
            margin-bottom: 8px !important;
            gap: 8px !important;
        }

        .service-modal-box.invoice-mode .invoice-brand h2 {
            font-size: 16px !important;
            line-height: 1.15 !important;
        }

        .service-modal-box.invoice-mode .invoice-brand p,
        .service-modal-box.invoice-mode .invoice-meta p {
            font-size: 10.2px !important;
            line-height: 1.2 !important;
            margin: 2px 0 !important;
        }

        .service-modal-box.invoice-mode .invoice-meta strong {
            font-size: 12px !important;
            line-height: 1.15 !important;
        }

        .service-modal-box.invoice-mode .invoice-info-grid {
            gap: 7px !important;
            margin-bottom: 8px !important;
        }

        .service-modal-box.invoice-mode .invoice-info-card {
            padding: 7px 9px !important;
            border-radius: 10px !important;
        }

        .service-modal-box.invoice-mode .invoice-info-card h4 {
            font-size: 9px !important;
            margin-bottom: 4px !important;
            line-height: 1.15 !important;
        }

        .service-modal-box.invoice-mode .invoice-info-card p {
            font-size: 10.2px !important;
            line-height: 1.2 !important;
            margin: 2px 0 !important;
        }

        .service-modal-box.invoice-mode .invoice-items-table {
            font-size: 10.4px !important;
            border-radius: 10px !important;
        }

        .service-modal-box.invoice-mode .invoice-items-table th,
        .service-modal-box.invoice-mode .invoice-items-table td {
            padding: 5px 6px !important;
            line-height: 1.2 !important;
        }

        .service-modal-box.invoice-mode .invoice-items-table th {
            font-size: 8.8px !important;
        }

        .service-modal-box.invoice-mode .invoice-warranty-note {
            margin-top: 7px !important;
            padding: 6px 8px !important;
            border-radius: 9px !important;
            font-size: 10.2px !important;
            line-height: 1.22 !important;
        }

        .service-modal-box.invoice-mode .invoice-total-row {
            margin-top: 7px !important;
        }

        .service-modal-box.invoice-mode .invoice-total-box {
            min-width: 220px !important;
            max-width: 250px !important;
            border-radius: 10px !important;
        }

        .service-modal-box.invoice-mode .invoice-total-line {
            padding: 6px 9px !important;
            font-size: 10.5px !important;
            line-height: 1.2 !important;
        }

        .service-modal-box.invoice-mode .invoice-total-line.grand {
            font-size: 12px !important;
        }

        .service-modal-box.invoice-mode .detail-actions {
            flex: 0 0 auto !important;
            padding: 8px 16px !important;
            gap: 6px !important;
        }

        .service-modal-box.invoice-mode .detail-actions .btn-note-secondary,
        .service-modal-box.invoice-mode .detail-actions .btn-note-primary {
            height: 28px !important;
            padding: 0 12px !important;
            font-size: 11px !important;
            border-radius: 9px !important;
        }

        @media (max-width: 768px) {
            .service-modal-box.invoice-mode {
                width: 95vw !important;
                max-height: 90vh !important;
            }

            .service-modal-box.invoice-mode #detail-modal-content,
            .service-modal-box.invoice-mode .invoice-modal-inner {
                max-height: 90vh !important;
            }

            .service-modal-box.invoice-mode .invoice-info-grid {
                grid-template-columns: 1fr !important;
            }
        }
</style>
                    </head>
                    <body>${printArea.innerHTML}</body>
                    </html>
                `);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
        }

        function closeDetailModal(e = null) {
            const modal = document.getElementById('detail-modal');
            if (!e || e.target === modal) {
                modal.classList.remove('show');
                modal.style.display = 'none';

                const modalBox = modal.querySelector('.service-modal-box');
                if (modalBox) modalBox.classList.remove('detail-mode', 'invoice-mode');

                const content = document.getElementById('detail-modal-content');
                if (content) content.innerHTML = '';
            }
        }


        function ensurePaymentProofPreviewModal() {
            let modal = document.getElementById('payment-proof-preview-modal');
            if (modal) return modal;

            modal = document.createElement('div');
            modal.id = 'payment-proof-preview-modal';
            modal.className = 'proof-preview-modal';
            modal.innerHTML = `
                <button type="button" class="proof-preview-open" onclick="openCurrentProofInNewTab(event)">
                    <i data-lucide="external-link" class="icon-xs"></i> Buka Tab Baru
                </button>
                <button type="button" class="proof-preview-close" onclick="closePaymentProofPreview(event)">
                    <i data-lucide="x" class="icon-sm"></i>
                </button>
                <img id="payment-proof-preview-img" src="" alt="Bukti Pembayaran Full">
            `;
            modal.addEventListener('click', function (event) {
                if (event.target === modal) closePaymentProofPreview(event);
            });
            document.body.appendChild(modal);
            return modal;
        }

        function openPaymentProofPreview(encodedUrl) {
            const url = decodeURIComponent(encodedUrl || '');
            if (!url) return;

            const modal = ensurePaymentProofPreviewModal();
            const img = modal.querySelector('#payment-proof-preview-img');
            img.src = url;
            modal.dataset.currentUrl = url;
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
            lucide.createIcons();
        }

        function closePaymentProofPreview(event = null) {
            if (event) event.stopPropagation();

            const modal = document.getElementById('payment-proof-preview-modal');
            if (!modal) return;

            modal.classList.remove('show');
            const img = modal.querySelector('#payment-proof-preview-img');
            if (img) img.src = '';
            document.body.style.overflow = '';
        }

        function openCurrentProofInNewTab(event = null) {
            if (event) event.stopPropagation();

            const modal = document.getElementById('payment-proof-preview-modal');
            const url = modal?.dataset?.currentUrl;
            if (url) window.open(url, '_blank');
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closePaymentProofPreview();
            }
        });

        async function exportToExcel() {
            showToast('Mengekspor data servis...', 'info');

            const status = document.getElementById('filter-status').value;
            const search = document.getElementById('filter-search').value;
            const start = document.getElementById('filter-start').value;
            const end = document.getElementById('filter-end').value;

            try {
                let params = new URLSearchParams();
                if (status) params.append('status', getApiStatusParam(status));
                if (search) params.append('search', search);
                if (start) params.append('dari_tanggal', start);
                if (end) params.append('sampai_tanggal', end);

                const token = localStorage.getItem('auth_token');
                const headers = {};
                if (token) headers['Authorization'] = `Bearer ${token}`;

                const response = await fetch(`/api/servis/export?${params.toString()}`, {
                    method: 'GET',
                    headers
                });

                if (!response.ok) {
                    throw new Error('Gagal mengekspor data');
                }

                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;

                const disposition = response.headers.get('content-disposition');
                let filename = `daftar_servis_${new Date().toISOString().slice(0, 10)}.csv`;
                if (disposition && disposition.includes('attachment')) {
                    const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                    const matches = filenameRegex.exec(disposition);
                    if (matches && matches[1]) filename = matches[1].replace(/['"]/g, '');
                }

                a.download = filename;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
                showToast('Ekspor data servis berhasil diunduh.', 'success');
            } catch (e) {
                console.error(e);
                showToast('Terjadi kesalahan saat mengekspor: ' + e.message, 'error');
            }
        }
    </script>
@endpush