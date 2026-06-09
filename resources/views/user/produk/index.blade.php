@extends('layouts.app')

@section('title', 'Katalog Produk — Defkan Computer')

@section('content')
    <div class="container page-container catalog-page-modern">
        <div class="catalog-hero-modern">
            <div class="catalog-hero-content">
                <span class="catalog-eyebrow">Laptop Second Berkualitas</span>
                <h1 class="catalog-title">Katalog <span class="text-primary-gradient">Produk</span></h1>
                <p class="text-muted catalog-subtitle">Temukan laptop bekas siap pakai, bergaransi, dan sesuai kebutuhan
                    Anda.</p>
            </div>
        </div>

        {{-- Trust Info --}}
        <div class="catalog-trust-strip">
            <div class="trust-item"><i data-lucide="shield-check" class="icon icon-sm"></i> Garansi 1 Bulan</div>
            <div class="trust-item"><i data-lucide="check-circle" class="icon icon-sm"></i> Laptop Siap Pakai</div>
            <div class="trust-item"><i data-lucide="messages-square" class="icon icon-sm"></i> Bisa Konsultasi</div>
            <div class="trust-item"><i data-lucide="credit-card" class="icon icon-sm"></i> QRIS / Transfer</div>
        </div>

        {{-- Filter & Search --}}
        <div class="catalog-sticky-tools">
            <div class="glass catalog-filter-container catalog-filter-modern">
                <div class="catalog-search-wrap">
                    <i data-lucide="search" class="catalog-search-icon"></i>
                    <input type="text" id="search-input" class="form-control catalog-search-input"
                        placeholder="Cari nama laptop, merek, atau spesifikasi...">
                </div>

                <select id="kategori-select" class="form-control catalog-kategori-select">
                    <option value="">Semua Kategori</option>
                    <option value="Laptop Pelajar / Mahasiswa">Laptop Pelajar / Mahasiswa</option>
                    <option value="Laptop Kantor / Kerja">Laptop Kantor / Kerja</option>
                    <option value="Laptop Gaming">Laptop Gaming</option>
                    <option value="Laptop Desain & Editing">Laptop Desain & Editing</option>
                    <option value="Laptop Programming">Laptop Programming</option>
                    <option value="Laptop Tipis & Ringan">Laptop Tipis & Ringan</option>
                    <option value="Laptop Budget / Hemat">Laptop Budget / Hemat</option>
                </select>

                <div class="catalog-sort-wrap">
                    <span class="text-muted catalog-sort-label">Urutkan:</span>
                    <select id="sort-select" class="form-control catalog-kategori-select">
                        <option value="terbaru">Terbaru</option>
                        <option value="nama_asc">Nama A-Z</option>
                        <option value="nama_desc">Nama Z-A</option>
                        <option value="harga_terendah">Harga: Rendah ke Tinggi</option>
                        <option value="harga_tertinggi">Harga: Tinggi ke Rendah</option>
                    </select>
                </div>

                <label class="catalog-stock-pill">
                    <input type="checkbox" id="tersedia-check" class="catalog-stok-checkbox">
                    <span><i data-lucide="check" class="icon icon-xs"></i> Tersedia</span>
                </label>

                <button id="btn-filter" class="btn btn-primary catalog-filter-btn">
                    <i data-lucide="sliders-horizontal" class="icon icon-sm"></i>
                    Terapkan
                </button>
            </div>
        </div>

        <div id="loading-indicator" class="catalog-loading" style="display: none;">
            <div class="loader catalog-loading-loader"></div>
            <p class="text-muted catalog-loading-text">Memuat katalog...</p>
        </div>

        <div id="produk-grid" class="grid catalog-product-grid"></div>

        <div id="empty-state" class="catalog-empty" style="display: none;">
            <div class="catalog-empty-icon">
                <i data-lucide="package-x" style="width: 64px; height: 64px; stroke-width: 1.25;"></i>
            </div>
            <h3 class="text-muted">Produk tidak ditemukan</h3>
            <p class="text-muted" style="font-size: 14px; margin-top: 8px;">Coba ubah filter pencarian Anda.</p>
        </div>

        {{-- Pagination --}}
        <div id="catalog-pagination" class="catalog-pagination" style="display: none;"></div>
    </div>

    {{-- Product Detail Modal --}}
    <div id="detail-modal" class="modal-overlay" style="display: none;" onclick="closeDetailModal(event)">
        <div class="glass-panel modal-panel product-detail-modal-panel" onclick="event.stopPropagation()">
            <button onclick="closeDetailModal()" class="product-detail-close-btn"><i data-lucide="x"
                    class="icon"></i></button>
            <div id="modal-content">
                <div class="flex-center" style="padding: 60px;">
                    <div class="loader"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .catalog-page-modern {
            padding-top: 26px;
        }

        .catalog-hero-modern {
            text-align: center;
            padding: 26px 16px 18px;
            margin-bottom: 10px;
        }

        .catalog-eyebrow {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(37, 99, 235, 0.08);
            border: 1px solid rgba(37, 99, 235, 0.12);
            color: var(--primary);
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .catalog-title {
            margin: 0;
            font-size: clamp(30px, 4vw, 46px);
            line-height: 1.05;
            font-weight: 900;
            letter-spacing: -0.04em;
        }

        .catalog-subtitle {
            margin: 10px auto 0;
            font-size: 15px;
            max-width: 620px;
            line-height: 1.55;
        }

        .catalog-trust-strip {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            margin: 0 0 14px;
        }

        .trust-item {
            height: 42px;
            border-radius: 14px;
            border: 1px solid var(--glass-border);
            background: var(--bg-surface, #fff);
            color: var(--text-main);
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
            white-space: nowrap;
        }

        .trust-item svg {
            color: var(--primary);
        }

        .catalog-filter-modern {
            position: sticky;
            top: 80px;
            z-index: 20;
            display: grid;
            grid-template-columns: minmax(260px, 1.35fr) minmax(190px, .75fr) minmax(220px, .95fr) auto auto;
            align-items: center;
            gap: 10px;
            padding: 12px;
            border-radius: 18px;
            margin-bottom: 12px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
            backdrop-filter: blur(14px);
        }

        .catalog-search-wrap {
            position: relative;
            min-width: 0;
        }

        .catalog-search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 17px;
            height: 17px;
            color: var(--text-muted);
            pointer-events: none;
        }

        .catalog-search-input {
            padding-left: 42px !important;
        }

        .catalog-filter-modern .form-control {
            height: 42px;
            border-radius: 13px;
            font-size: 13px;
        }

        .catalog-sort-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }

        .catalog-sort-label {
            font-size: 13px;
            white-space: nowrap;
            font-weight: 700;
        }

        .catalog-stock-pill {
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0 13px;
            border-radius: 13px;
            border: 1px solid var(--glass-border);
            background: var(--bg-surface, #fff);
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            user-select: none;
            white-space: nowrap;
            transition: .2s ease;
        }

        .catalog-stock-pill input {
            display: none;
        }

        .catalog-stock-pill:has(input:checked) {
            background: rgba(16, 185, 129, 0.11);
            color: var(--success, #10b981);
            border-color: rgba(16, 185, 129, 0.28);
        }

        .catalog-filter-btn {
            height: 42px;
            padding: 0 18px;
            border-radius: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 800;
            white-space: nowrap;
        }

        .catalog-category-pills {
            display: flex;
            align-items: center;
            gap: 8px;
            overflow-x: auto;
            padding: 2px 2px 16px;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .catalog-category-pills::-webkit-scrollbar {
            display: none;
        }

        .catalog-pill {
            height: 34px;
            padding: 0 14px;
            border-radius: 999px;
            border: 1px solid var(--glass-border);
            background: var(--bg-surface, #fff);
            color: var(--text-muted);
            font-size: 12.5px;
            font-weight: 800;
            cursor: pointer;
            white-space: nowrap;
            transition: .2s ease;
        }

        .catalog-pill:hover,
        .catalog-pill.active {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.18);
        }

        .catalog-product-grid.grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }

        .catalog-card-modern.card {
            height: 100%;
            min-height: 420px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border-radius: 18px;
            border: 1px solid var(--glass-border);
            background: var(--bg-surface, #fff);
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
            transition: .24s ease;
        }

        .catalog-card-modern.card:hover {
            transform: translateY(-4px);
            border-color: rgba(37, 99, 235, 0.24);
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.10);
        }

        .catalog-img-wrap {
            position: relative;
            height: 206px;
            background: #f8fafc;
            overflow: hidden;
            cursor: pointer;
        }

        .catalog-card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform .28s ease;
        }

        .catalog-card-modern:hover .catalog-card-img {
            transform: scale(1.035);
        }

        .catalog-top-badges {
            position: absolute;
            inset: 12px 12px auto 12px;
            display: flex;
            justify-content: space-between;
            gap: 8px;
            align-items: flex-start;
            pointer-events: none;
        }

        .catalog-badge-category,
        .catalog-badge-stock {
            min-height: 28px;
            display: inline-flex;
            align-items: center;
            padding: 0 10px;
            border-radius: 999px;
            font-size: 11.5px;
            font-weight: 900;
            max-width: 70%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            backdrop-filter: blur(8px);
        }

        .catalog-badge-category {
            background: rgba(255, 255, 255, .88);
            color: var(--text-muted);
            border: 1px solid rgba(226, 232, 240, .85);
        }

        .catalog-badge-stock.ok {
            background: rgba(16, 185, 129, .14);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, .25);
        }

        .catalog-badge-stock.empty {
            background: rgba(239, 68, 68, .14);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, .25);
        }

        .catalog-card-body {
            padding: 15px 16px 16px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .catalog-brand {
            color: var(--text-muted);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .catalog-product-title {
            margin: 0 0 9px;
            font-size: 16px;
            line-height: 1.3;
            font-weight: 900;
            color: var(--text-main);
            cursor: pointer;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 42px;
        }

        .catalog-product-title:hover {
            color: var(--primary);
        }

        .catalog-spec-list {
            display: flex;
            gap: 6px;
            margin-bottom: 10px;
            flex-wrap: wrap;
            min-height: 24px;
        }

        .catalog-spec-chip {
            height: 23px;
            display: inline-flex;
            align-items: center;
            padding: 0 7px;
            border-radius: 7px;
            font-size: 10.5px;
            font-weight: 800;
            max-width: 112px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .chip-ram {
            background: rgba(14, 165, 233, 0.1);
            border: 1px solid rgba(14, 165, 233, 0.2);
            color: var(--primary);
        }

        .chip-storage {
            background: rgba(3, 105, 161, 0.1);
            border: 1px solid rgba(3, 105, 161, 0.2);
            color: var(--accent);
        }

        .chip-vga {
            background: rgba(124, 58, 237, 0.1);
            border: 1px solid rgba(124, 58, 237, 0.2);
            color: var(--secondary);
        }

        .catalog-benefit {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11.5px;
            font-weight: 800;
            color: var(--success, #059669);
            margin-bottom: 10px;
        }

        .catalog-card-bottom {
            margin-top: auto;
        }

        .catalog-price-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 10px;
        }

        .catalog-card-price {
            font-size: 18px;
            line-height: 1.1;
            font-weight: 950;
            color: var(--text-main);
            letter-spacing: -0.025em;
        }

        .marketplace-mini {
            display: flex;
            gap: 6px;
            align-items: center;
            flex-shrink: 0;
        }

        .marketplace-btn,
        .marketplace-img-link {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all .2s ease;
        }

        .marketplace-btn:hover,
        .marketplace-img-link:hover {
            transform: translateY(-1px);
            opacity: .86;
        }

        .catalog-action-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1.35fr;
            gap: 7px;
        }

        .catalog-action-row .btn {
            height: 36px;
            padding: 0 8px !important;
            font-size: 12px !important;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-weight: 800;
        }

        .catalog-action-row .btn-primary {
            box-shadow: 0 8px 18px rgba(37, 99, 235, .18);
        }

        .catalog-pagination {
            margin-top: 22px;
            padding: 12px 14px;
            border-radius: 16px;
            background: var(--bg-surface, #fff);
            border: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
        }

        .cat-pag-info {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 700;
        }

        .cat-pag-controls {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .cat-pag-btn {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            border: 1px solid var(--glass-border);
            background: #fff;
            color: var(--text-main);
            font-size: 12px;
            font-weight: 900;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .cat-pag-btn.active {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }

        .cat-pag-btn.disabled {
            opacity: .45;
            cursor: not-allowed;
        }

        .cat-pag-dots {
            color: var(--text-muted);
            font-size: 12px;
            padding: 0 4px;
        }

        @media (max-width: 1200px) {
            .catalog-product-grid.grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .catalog-filter-modern {
                grid-template-columns: 1fr 220px;
            }
        }

        @media (max-width: 768px) {
            .catalog-page-modern {
                padding-top: 14px;
            }

            .catalog-hero-modern {
                padding: 16px 10px 12px;
            }

            .catalog-trust-strip {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .trust-item {
                height: 38px;
                font-size: 12px;
            }

            .catalog-filter-modern {
                position: static;
                grid-template-columns: 1fr;
            }

            .catalog-sort-wrap {
                display: grid;
                grid-template-columns: auto 1fr;
            }

            .catalog-filter-btn,
            .catalog-stock-pill {
                width: 100%;
            }

            .catalog-product-grid.grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px;
            }

            .catalog-img-wrap {
                height: 160px;
            }

            .catalog-card-modern.card {
                min-height: 390px;
            }

            .catalog-card-price {
                font-size: 15px;
            }

            .catalog-action-row {
                grid-template-columns: 1fr 1fr;
            }

            .catalog-action-row .btn-primary {
                grid-column: span 2;
            }

            .catalog-pagination {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 480px) {
            .catalog-product-grid.grid {
                grid-template-columns: 1fr;
            }

            .catalog-trust-strip {
                grid-template-columns: 1fr;
            }
        }


        /* ===== Final Compact Hero Fix ===== */
        .container.page-container.catalog-page-modern,
        .page-container.catalog-page-modern,
        .catalog-page-modern {
            padding-top: 0 !important;
            margin-top: 0 !important;
        }

        .catalog-hero-modern {
            padding: 10px 16px 12px !important;
            margin: 0 0 8px !important;
        }

        .catalog-eyebrow {
            padding: 4px 11px !important;
            font-size: 11.5px !important;
            margin-bottom: 7px !important;
        }

        .catalog-title {
            font-size: clamp(28px, 3.2vw, 40px) !important;
            line-height: 1.02 !important;
        }

        .catalog-subtitle {
            margin-top: 7px !important;
            font-size: 13.5px !important;
            line-height: 1.35 !important;
        }

        .catalog-trust-strip {
            gap: 8px !important;
            margin: 0 0 10px !important;
        }

        .trust-item {
            height: 36px !important;
            border-radius: 12px !important;
            font-size: 12.5px !important;
            gap: 7px !important;
        }

        .catalog-filter-modern {
            top: 72px !important;
            padding: 10px !important;
            border-radius: 16px !important;
            margin-bottom: 10px !important;
            gap: 8px !important;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.065) !important;
        }

        .catalog-filter-modern .form-control,
        .catalog-stock-pill,
        .catalog-filter-btn {
            height: 38px !important;
            border-radius: 12px !important;
            font-size: 12.5px !important;
        }

        .catalog-search-input {
            padding-left: 38px !important;
        }

        .catalog-search-icon {
            left: 13px !important;
            width: 16px !important;
            height: 16px !important;
        }

        .catalog-filter-btn {
            padding: 0 15px !important;
        }

        .catalog-stock-pill {
            padding: 0 12px !important;
        }

        .catalog-category-pills {
            padding: 0 2px 12px !important;
            gap: 7px !important;
        }

        .catalog-pill {
            height: 31px !important;
            padding: 0 13px !important;
            font-size: 12px !important;
        }

        @media (max-width: 768px) {
            .catalog-hero-modern {
                padding: 8px 10px 10px !important;
            }

            .catalog-trust-strip {
                margin-bottom: 8px !important;
            }

            .trust-item {
                height: 34px !important;
            }
        }


        /* ===== Final User-Friendly Fix: Hero Naik, Filter Sticky, Foto Tidak Crop ===== */
        :root {
            --catalog-nav-offset: 78px;
        }

        .container.page-container.catalog-page-modern,
        .page-container.catalog-page-modern,
        .catalog-page-modern {
            padding-top: 0 !important;
            margin-top: -58px !important;
        }

        .catalog-hero-modern {
            padding: 8px 16px 8px !important;
            margin: 0 0 8px !important;
        }

        .catalog-eyebrow {
            padding: 4px 11px !important;
            font-size: 11px !important;
            margin-bottom: 6px !important;
        }

        .catalog-title {
            font-size: clamp(28px, 3vw, 38px) !important;
            line-height: 1.02 !important;
        }

        .catalog-subtitle {
            margin-top: 6px !important;
            font-size: 13.2px !important;
            line-height: 1.35 !important;
        }

        .catalog-trust-strip {
            gap: 8px !important;
            margin: 0 0 8px !important;
        }

        .trust-item {
            height: 34px !important;
            border-radius: 12px !important;
            font-size: 12px !important;
            gap: 6px !important;
        }

        .trust-item svg {
            width: 15px !important;
            height: 15px !important;
        }

        .catalog-sticky-tools {
            position: sticky;
            top: var(--catalog-nav-offset);
            z-index: 60;
            padding: 0 0 8px;
            margin-bottom: 12px;
            background: linear-gradient(180deg, rgba(248, 250, 252, 0.96) 0%, rgba(248, 250, 252, 0.84) 100%);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        .catalog-filter-modern {
            position: relative !important;
            top: auto !important;
            z-index: 1 !important;
            padding: 9px 10px !important;
            border-radius: 16px !important;
            margin-bottom: 8px !important;
            gap: 8px !important;
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.075) !important;
        }

        .catalog-filter-modern .form-control,
        .catalog-stock-pill,
        .catalog-filter-btn {
            height: 36px !important;
            border-radius: 11px !important;
            font-size: 12px !important;
        }

        .catalog-search-input {
            padding-left: 36px !important;
        }

        .catalog-search-icon {
            left: 12px !important;
            width: 15px !important;
            height: 15px !important;
        }

        .catalog-filter-btn {
            padding: 0 14px !important;
            min-width: 126px;
        }

        .catalog-stock-pill {
            padding: 0 12px !important;
        }

        .catalog-category-pills {
            padding: 0 2px !important;
            gap: 7px !important;
            margin: 0 !important;
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .catalog-category-pills::-webkit-scrollbar {
            display: none;
        }

        .catalog-pill {
            height: 30px !important;
            padding: 0 13px !important;
            font-size: 11.8px !important;
            flex-shrink: 0;
        }

        .catalog-product-grid.grid {
            margin-top: 8px !important;
        }

        .catalog-img-wrap {
            height: 214px !important;
            padding: 14px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: linear-gradient(135deg, #f8fafc 0%, #eef4ff 100%) !important;
            overflow: hidden !important;
        }

        .catalog-card-img {
            width: 100% !important;
            height: 100% !important;
            max-width: 100% !important;
            max-height: 100% !important;
            object-fit: contain !important;
            object-position: center !important;
        }

        .catalog-card-modern:hover .catalog-card-img {
            transform: scale(1.018) !important;
        }

        .product-detail-img {
            object-fit: contain !important;
            object-position: center !important;
            background: #f8fafc !important;
            padding: 10px !important;
        }

        .product-gallery-thumbnails img {
            object-fit: contain !important;
            background: #f8fafc !important;
            padding: 4px !important;
        }

        @media (max-width: 1200px) {
            .catalog-sticky-tools {
                top: 72px;
            }
        }

        @media (max-width: 768px) {

            .container.page-container.catalog-page-modern,
            .page-container.catalog-page-modern,
            .catalog-page-modern {
                margin-top: -28px !important;
            }

            .catalog-sticky-tools {
                position: sticky;
                top: 66px;
                padding-bottom: 7px;
            }

            .catalog-filter-modern {
                grid-template-columns: 1fr !important;
                padding: 8px !important;
            }

            .catalog-img-wrap {
                height: 170px !important;
                padding: 10px !important;
            }
        }

        @media (max-width: 480px) {

            .container.page-container.catalog-page-modern,
            .page-container.catalog-page-modern,
            .catalog-page-modern {
                margin-top: -16px !important;
            }

            .catalog-sticky-tools {
                top: 60px;
            }
        }



        /* ===== FINAL FIX: Hero lebih naik + Filter/Search & Quick Category benar-benar sticky ===== */
        .catalog-page-modern {
            padding-top: 0 !important;
            margin-top: -118px !important;
        }

        .catalog-hero-modern {
            padding-top: 0 !important;
            padding-bottom: 8px !important;
            margin-bottom: 8px !important;
        }

        .catalog-sticky-tools {
            position: sticky !important;
            top: 76px !important;
            z-index: 999 !important;
            margin-bottom: 12px !important;
            padding: 8px 0 10px !important;
            background: linear-gradient(180deg, rgba(248, 250, 252, 0.98) 0%, rgba(248, 250, 252, 0.92) 100%) !important;
            backdrop-filter: blur(18px) !important;
            -webkit-backdrop-filter: blur(18px) !important;
        }

        .catalog-sticky-tools.is-stuck {
            position: fixed !important;
            top: 76px !important;
            z-index: 9999 !important;
            margin: 0 !important;
            border-radius: 0 0 18px 18px;
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.10);
        }

        .catalog-sticky-tools.is-stuck .catalog-filter-modern {
            margin-bottom: 7px !important;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08) !important;
        }

        .catalog-sticky-placeholder {
            display: none;
        }

        .catalog-sticky-placeholder.active {
            display: block;
        }

        .catalog-filter-modern {
            grid-template-columns: minmax(260px, 1.45fr) minmax(180px, .75fr) minmax(210px, .9fr) auto auto !important;
        }

        .catalog-filter-modern .form-control,
        .catalog-stock-pill,
        .catalog-filter-btn {
            height: 34px !important;
        }

        .catalog-category-pills {
            padding-bottom: 1px !important;
        }

        .catalog-product-grid {
            scroll-margin-top: 190px;
        }

        /* Foto produk tidak crop, tetap proporsional */
        .catalog-img-wrap {
            height: 208px !important;
            padding: 16px !important;
            background: #f8fafc !important;
        }

        .catalog-card-img,
        #main-detail-img,
        .product-detail-img,
        .product-gallery-thumbnails img,
        .gallery-thumbnail-img {
            object-fit: contain !important;
            object-position: center !important;
        }

        .gallery-thumbnail-img {
            background: #f8fafc !important;
            padding: 4px !important;
        }

        @media (max-width: 1200px) {
            .catalog-page-modern {
                margin-top: -88px !important;
            }

            .catalog-sticky-tools,
            .catalog-sticky-tools.is-stuck {
                top: 70px !important;
            }
        }

        @media (max-width: 768px) {
            .catalog-page-modern {
                margin-top: -36px !important;
            }

            .catalog-sticky-tools,
            .catalog-sticky-tools.is-stuck {
                top: 62px !important;
            }

            .catalog-sticky-tools.is-stuck {
                left: 12px !important;
                right: 12px !important;
                width: auto !important;
            }

            .catalog-img-wrap {
                height: 176px !important;
                padding: 12px !important;
            }
        }


        /* ===== FINAL ADJUSTMENT: Ukuran Katalog Seperti Tampilan Awal + Garansi di Card ===== */
        .catalog-page-modern {
            padding-top: 42px !important;
            margin-top: 0 !important;
        }

        .catalog-hero-modern {
            padding: 24px 16px 18px !important;
            margin: 0 0 14px !important;
        }

        .catalog-eyebrow,
        .catalog-trust-strip {
            display: none !important;
        }

        .catalog-title {
            font-size: clamp(30px, 3vw, 40px) !important;
            line-height: 1.05 !important;
            letter-spacing: -0.035em !important;
        }

        .catalog-subtitle {
            margin-top: 10px !important;
            font-size: 14px !important;
            line-height: 1.45 !important;
        }

        .catalog-sticky-tools {
            position: sticky !important;
            top: 72px !important;
            z-index: 999 !important;
            padding: 8px 0 12px !important;
            margin-bottom: 14px !important;
            background: linear-gradient(180deg, rgba(248, 250, 252, 0.98) 0%, rgba(248, 250, 252, 0.92) 100%) !important;
            backdrop-filter: blur(16px) !important;
            -webkit-backdrop-filter: blur(16px) !important;
        }

        .catalog-sticky-tools.is-stuck {
            top: 72px !important;
            border-radius: 0 0 18px 18px !important;
        }

        .catalog-filter-modern {
            max-width: 1120px !important;
            margin: 0 auto 12px !important;
            grid-template-columns: minmax(260px, 1.4fr) minmax(200px, .9fr) minmax(250px, 1fr) auto auto !important;
            padding: 12px 14px !important;
            gap: 12px !important;
            border-radius: 18px !important;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08) !important;
        }

        .catalog-filter-modern .form-control,
        .catalog-stock-pill,
        .catalog-filter-btn {
            height: 42px !important;
            border-radius: 12px !important;
            font-size: 13px !important;
        }

        .catalog-filter-btn {
            min-width: 118px !important;
            padding: 0 18px !important;
        }

        .catalog-category-pills {
            max-width: 1120px !important;
            margin: 0 auto !important;
            padding: 0 0 2px !important;
            gap: 10px !important;
        }

        .catalog-pill {
            height: 36px !important;
            padding: 0 18px !important;
            font-size: 13px !important;
            border-radius: 999px !important;
        }

        .catalog-product-grid.grid {
            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
            gap: 18px !important;
            margin-top: 8px !important;
        }

        .catalog-card-modern.card {
            min-height: 420px !important;
            border-radius: 16px !important;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.065) !important;
        }

        .catalog-img-wrap {
            height: 210px !important;
            padding: 14px !important;
            background: #f8fafc !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .catalog-card-img {
            width: 100% !important;
            height: 100% !important;
            object-fit: contain !important;
            object-position: center !important;
        }

        .catalog-card-body {
            padding: 14px 16px 15px !important;
        }

        .catalog-brand {
            font-size: 10.5px !important;
            margin-bottom: 4px !important;
        }

        .catalog-product-title {
            font-size: 15.5px !important;
            min-height: 40px !important;
            margin-bottom: 8px !important;
        }

        .catalog-spec-list {
            margin-bottom: 8px !important;
            min-height: 23px !important;
        }

        .catalog-warranty-row {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            min-height: 24px;
            margin: 0 0 10px;
        }

        .catalog-warranty-chip {
            height: 23px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 0 8px;
            border-radius: 8px;
            border: 1px solid rgba(16, 185, 129, 0.22);
            background: rgba(16, 185, 129, 0.10);
            color: #059669;
            font-size: 10.5px;
            font-weight: 900;
            white-space: nowrap;
        }

        .catalog-warranty-chip.ready {
            border-color: rgba(37, 99, 235, 0.18);
            background: rgba(37, 99, 235, 0.08);
            color: var(--primary, #2563eb);
        }

        .catalog-warranty-chip svg {
            width: 12px !important;
            height: 12px !important;
        }

        .catalog-card-price {
            font-size: 18px !important;
            color: #0369a1 !important;
        }

        .marketplace-btn,
        .marketplace-img-link,
        .marketplace-img-link img {
            width: 27px !important;
            height: 27px !important;
        }

        .catalog-action-row .btn {
            height: 35px !important;
            border-radius: 10px !important;
            font-size: 12px !important;
        }

        .product-detail-warranty-box {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin: 12px 0 14px;
        }

        .product-detail-warranty-box span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            height: 30px;
            padding: 0 10px;
            border-radius: 999px;
            background: rgba(16, 185, 129, 0.10);
            border: 1px solid rgba(16, 185, 129, 0.22);
            color: #059669;
            font-size: 12px;
            font-weight: 800;
        }

        .product-detail-warranty-box span:last-child {
            background: rgba(37, 99, 235, 0.08);
            border-color: rgba(37, 99, 235, 0.18);
            color: var(--primary, #2563eb);
        }

        @media (max-width: 1200px) {
            .catalog-product-grid.grid {
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            }

            .catalog-filter-modern {
                grid-template-columns: 1fr 220px !important;
            }
        }

        @media (max-width: 768px) {
            .catalog-page-modern {
                padding-top: 20px !important;
            }

            .catalog-hero-modern {
                padding: 18px 10px 12px !important;
            }

            .catalog-sticky-tools,
            .catalog-sticky-tools.is-stuck {
                top: 62px !important;
            }

            .catalog-filter-modern {
                grid-template-columns: 1fr !important;
                padding: 10px !important;
            }

            .catalog-product-grid.grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 12px !important;
            }

            .catalog-img-wrap {
                height: 170px !important;
                padding: 10px !important;
            }
        }

        @media (max-width: 480px) {
            .catalog-product-grid.grid {
                grid-template-columns: 1fr !important;
            }
        }

        /* ===== FINAL FIX REQUEST: Normal Scroll, Tidak Sticky, Tidak Kepotong ===== */
        html,
        body {
            overflow-x: hidden !important;
        }

        .container.page-container.catalog-page-modern,
        .page-container.catalog-page-modern,
        .catalog-page-modern {
            padding-top: 18px !important;
            margin-top: 0 !important;
            overflow: visible !important;
        }

        .catalog-hero-modern {
            padding: 22px 16px 16px !important;
            margin: 0 0 12px !important;
            overflow: visible !important;
        }

        .catalog-title {
            font-size: clamp(30px, 3vw, 40px) !important;
            line-height: 1.12 !important;
            margin: 0 !important;
            overflow: visible !important;
        }

        .catalog-subtitle {
            margin-top: 8px !important;
            font-size: 14px !important;
            line-height: 1.45 !important;
        }

        .catalog-sticky-tools,
        .catalog-sticky-tools.is-stuck {
            position: static !important;
            top: auto !important;
            left: auto !important;
            right: auto !important;
            width: auto !important;
            z-index: auto !important;
            margin: 0 0 14px !important;
            padding: 0 !important;
            background: transparent !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            transform: none !important;
        }

        .catalog-sticky-placeholder,
        .catalog-sticky-placeholder.active {
            display: none !important;
            height: 0 !important;
            min-height: 0 !important;
        }

        .catalog-filter-modern {
            position: static !important;
            top: auto !important;
            max-width: 1120px !important;
            margin: 0 auto 12px !important;
            grid-template-columns: minmax(260px, 1.4fr) minmax(200px, .9fr) minmax(250px, 1fr) auto auto !important;
            padding: 12px 14px !important;
            gap: 12px !important;
            border-radius: 18px !important;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08) !important;
        }

        .catalog-category-pills {
            max-width: 1120px !important;
            margin: 0 auto 10px !important;
            padding: 0 !important;
            gap: 10px !important;
            overflow-x: auto !important;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .catalog-category-pills::-webkit-scrollbar {
            display: none;
        }

        .catalog-product-grid.grid {
            margin-top: 0 !important;
            overflow: visible !important;
        }

        .catalog-img-wrap {
            height: 210px !important;
            padding: 14px !important;
            overflow: hidden !important;
            background: #f8fafc !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .catalog-card-img {
            width: 100% !important;
            height: 100% !important;
            max-width: 100% !important;
            max-height: 100% !important;
            object-fit: contain !important;
            object-position: center !important;
        }

        .catalog-card-modern.card {
            overflow: hidden !important;
        }

        @media (max-width: 1200px) {
            .catalog-filter-modern {
                grid-template-columns: 1fr 220px !important;
            }
        }

        @media (max-width: 768px) {
            .catalog-page-modern {
                padding-top: 14px !important;
            }

            .catalog-hero-modern {
                padding: 16px 10px 12px !important;
            }

            .catalog-filter-modern {
                grid-template-columns: 1fr !important;
                padding: 10px !important;
            }

            .catalog-img-wrap {
                height: 170px !important;
                padding: 10px !important;
            }
        }


        /* ===== Detail Produk: Foto Slider Tidak Crop + Tombol Geser ===== */
        .product-detail-media-card {
            position: relative;
            width: 100%;
            height: 360px;
            border-radius: 18px;
            border: 1px solid var(--glass-border, #e5e7eb);
            background: linear-gradient(135deg, #f8fafc 0%, #eef4ff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 16px;
        }

        .product-detail-media-card #main-detail-img,
        .product-detail-media-card .product-detail-img {
            width: 100% !important;
            height: 100% !important;
            max-width: 100% !important;
            max-height: 100% !important;
            object-fit: contain !important;
            object-position: center !important;
            background: transparent !important;
            padding: 0 !important;
            display: block;
        }

        .product-detail-slider-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 38px;
            height: 38px;
            border-radius: 999px;
            border: 1px solid rgba(226, 232, 240, 0.9);
            background: rgba(255, 255, 255, 0.92);
            color: var(--text-main, #0f172a);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 3;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.14);
            transition: 0.2s ease;
        }

        .product-detail-slider-btn:hover {
            background: var(--primary, #2563eb);
            border-color: var(--primary, #2563eb);
            color: #ffffff;
            transform: translateY(-50%) scale(1.04);
        }

        .product-detail-slider-btn.prev {
            left: 14px;
        }

        .product-detail-slider-btn.next {
            right: 14px;
        }

        .product-detail-slider-btn.hidden {
            display: none !important;
        }

        .product-detail-photo-counter {
            position: absolute;
            right: 14px;
            bottom: 12px;
            min-width: 46px;
            height: 24px;
            padding: 0 9px;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.72);
            color: #ffffff;
            font-size: 11px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            z-index: 3;
            backdrop-filter: blur(8px);
        }

        .product-gallery-thumbnails {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding: 8px 2px 6px;
            scrollbar-width: thin;
        }

        .gallery-thumbnail-img {
            width: 64px !important;
            height: 64px !important;
            object-fit: contain !important;
            object-position: center !important;
            border-radius: 12px !important;
            border: 2px solid var(--glass-border, #e5e7eb) !important;
            background: #f8fafc !important;
            padding: 4px !important;
            cursor: pointer;
            transition: 0.2s ease;
            flex: 0 0 auto;
        }

        .gallery-thumbnail-img.active {
            border-color: var(--primary, #2563eb) !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .gallery-thumbnail-img:hover {
            transform: translateY(-1px);
            border-color: var(--primary, #2563eb) !important;
        }

        @media (max-width: 768px) {
            .product-detail-media-card {
                height: 260px;
                border-radius: 16px;
                padding: 12px;
            }

            .product-detail-slider-btn {
                width: 34px;
                height: 34px;
            }

            .product-detail-slider-btn.prev {
                left: 10px;
            }

            .product-detail-slider-btn.next {
                right: 10px;
            }

            .gallery-thumbnail-img {
                width: 56px !important;
                height: 56px !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        let currentProdukList = [];
        let catalogPage = 1;
        const catalogPerPage = 8;
        let detailGalleryPhotos = [];
        let detailGalleryIndex = 0;

        document.addEventListener('DOMContentLoaded', () => {
            loadProduk();

            const urlParams = new URLSearchParams(window.location.search);
            const productId = urlParams.get('id');
            if (productId) {
                showDetailModal(productId);
            }

            document.getElementById('btn-filter').addEventListener('click', applyFilter);
            document.getElementById('search-input').addEventListener('keydown', (e) => {
                if (e.key === 'Enter') applyFilter();
            });
            document.getElementById('kategori-select').addEventListener('change', syncQuickKategoriActive);
            document.getElementById('sort-select').addEventListener('change', applyFilter);
            document.getElementById('tersedia-check').addEventListener('change', applyFilter);
        });

        function applyFilter() {
            catalogPage = 1;
            syncQuickKategoriActive();
            loadProduk();
        }

        function setQuickKategori(button, kategori) {
            document.getElementById('kategori-select').value = kategori;
            document.querySelectorAll('.catalog-pill').forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            catalogPage = 1;
            loadProduk();
        }

        function syncQuickKategoriActive() {
            const kategori = document.getElementById('kategori-select').value;
            document.querySelectorAll('.catalog-pill').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.kategori === kategori);
            });
        }

        function goToPage(page) {
            catalogPage = page;
            loadProduk();
            document.getElementById('produk-grid').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        async function loadProduk() {
            const grid = document.getElementById('produk-grid');
            const loader = document.getElementById('loading-indicator');
            const emptyState = document.getElementById('empty-state');
            const pagination = document.getElementById('catalog-pagination');

            const search = document.getElementById('search-input').value;
            const kategori = document.getElementById('kategori-select').value;
            const sort = document.getElementById('sort-select').value;
            const tersedia = document.getElementById('tersedia-check').checked;

            let qs = `?page=${catalogPage}&per_page=${catalogPerPage}`;
            if (search) qs += `&search=${encodeURIComponent(search)}`;
            if (kategori) qs += `&kategori=${encodeURIComponent(kategori)}`;
            if (sort) qs += `&sort=${encodeURIComponent(sort)}`;
            if (tersedia) qs += `&tersedia=1`;

            grid.innerHTML = '';
            emptyState.style.display = 'none';
            pagination.style.display = 'none';
            loader.style.display = 'block';

            try {
                const response = await apiFetch(`/produk${qs}`);
                const paginationData = response.data;
                currentProdukList = paginationData.data || [];

                loader.style.display = 'none';

                if (currentProdukList.length === 0) {
                    emptyState.style.display = 'block';
                    return;
                }

                currentProdukList.forEach((produk, idx) => {
                    const fotoUrl = produk.foto_url || `https://placehold.co/400x300/EFF8FF/0EA5E9?text=${encodeURIComponent(produk.merek || 'Laptop')}`;
                    const stokBadge = produk.stok > 0
                        ? `<span class="catalog-badge-stock ok">Stok: ${produk.stok}</span>`
                        : `<span class="catalog-badge-stock empty">Habis</span>`;
                    const kategoriBadge = produk.kategori ? `<span class="catalog-badge-category" title="${escapeHtml(produk.kategori)}">${escapeHtml(produk.kategori)}</span>` : `<span></span>`;
                    const disabledAttr = Number(produk.stok || 0) === 0 ? 'disabled' : '';
                    const productName = escapeHtml(produk.nama_produk || 'Produk');
                    const merek = escapeHtml(produk.merek || '');

                    const card = document.createElement('div');
                    card.className = 'card catalog-card-modern';
                    card.style.animationDelay = `${idx * 0.05}s`;
                    card.innerHTML = `
                                    <div class="catalog-img-wrap" onclick="showDetailModal(${produk.id_produk})">
                                        <img src="${fotoUrl}" alt="${productName}" class="catalog-card-img" onerror="this.src='https://placehold.co/400x300/EFF8FF/0EA5E9?text=Laptop'">
                                        <div class="catalog-top-badges">
                                            ${kategoriBadge}
                                            ${stokBadge}
                                        </div>
                                    </div>
                                    <div class="catalog-card-body">
                                        <span class="catalog-brand">${merek}</span>
                                        <h3 class="catalog-product-title" title="${productName}" onclick="showDetailModal(${produk.id_produk})">${productName}</h3>

                                        <div class="catalog-spec-list">
                                            ${produk.ram ? `<span class="catalog-spec-chip chip-ram">RAM ${produk.ram}GB</span>` : ''}
                                            ${produk.storage ? `<span class="catalog-spec-chip chip-storage">${produk.storage}GB</span>` : ''}
                                            ${produk.vga ? `<span class="catalog-spec-chip chip-vga" title="${escapeHtml(produk.vga)}">${escapeHtml(produk.vga)}</span>` : ''}
                                        </div>

                                        <div class="catalog-warranty-row" aria-label="Informasi garansi dan kondisi">
                                            <span class="catalog-warranty-chip">
                                                <i data-lucide="shield-check" class="icon icon-xs"></i>
                                                Garansi 1 Bulan
                                            </span>
                                            <span class="catalog-warranty-chip ready">
                                                <i data-lucide="check-circle" class="icon icon-xs"></i>
                                                Siap Pakai
                                            </span>
                                        </div>

                                        <div class="catalog-card-bottom">
                                            <div class="catalog-price-row">
                                                <div class="catalog-card-price">${formatRupiah(produk.harga)}</div>
                                                <div class="marketplace-mini" title="Tersedia juga di Marketplace">
                                                    <a href="#" onclick="event.stopPropagation(); window.open('https://shopee.co.id', '_blank')" class="marketplace-btn" style="background: #ee4d2d;">
                                                        <svg fill="#ffffff" viewBox="0 0 24 24" width="15" height="15"><path d="M15.9414 17.9633c.229-1.879-.981-3.077-4.1758-4.0969-1.548-.528-2.277-1.22-2.26-2.1719.065-1.056 1.048-1.825 2.352-1.85a5.2898 5.2898 0 0 1 2.8838.89c.116.072.197.06.263-.039.09-.145.315-.494.39-.62.051-.081.061-.187-.068-.281-.185-.1369-.704-.4149-.983-.5319a6.4697 6.4697 0 0 0-2.5118-.514c-1.909.008-3.4129 1.215-3.5389 2.826-.082 1.1629.494 2.1078 1.73 2.8278.262.152 1.6799.716 2.2438.892 1.774.552 2.695 1.5419 2.478 2.6969-.197 1.047-1.299 1.7239-2.818 1.7439-1.2039-.046-2.2878-.537-3.1278-1.19l-.141-.11c-.104-.08-.218-.075-.287.03-.05.077-.376.547-.458.67-.077.108-.035.168.045.234.35.293.817.613 1.134.775a6.7097 6.7097 0 0 0 2.8289.727 4.9048 4.9048 0 0 0 2.0759-.354c1.095-.465 1.8029-1.394 1.9449-2.554zM11.9986 1.4009c-2.068 0-3.7539 1.95-3.8329 4.3899h7.6657c-.08-2.44-1.765-4.3899-3.8328-4.3899zm7.8516 22.5981-.08.001-15.7843-.002c-1.074-.04-1.863-.91-1.971-1.991l-.01-.195L1.298 6.2858a.459.459 0 0 1 .45-.494h4.9748C6.8448 2.568 9.1607 0 11.9996 0c2.8388 0 5.1537 2.5689 5.2757 5.7898h4.9678a.459.459 0 0 1 .458.483l-.773 15.5883-.007.131c-.094 1.094-.979 1.9769-2.0709 2.0059z"/></svg>
                                                    </a>
                                                    <a href="#" onclick="event.stopPropagation(); window.open('https://tokopedia.com', '_blank');" class="marketplace-img-link">
                                                        <img src="/img/tokped.png" alt="Tokopedia" style="width: 28px; height: 28px; object-fit: contain;">
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="catalog-action-row">
                                                <button onclick="showDetailModal(${produk.id_produk})" class="btn btn-outline" title="Lihat Detail">
                                                    <i data-lucide="eye" class="icon icon-sm"></i> Detail
                                                </button>
                                                <button onclick="event.stopPropagation(); tambahKeKeranjang(${produk.id_produk}, this)" class="btn btn-outline" ${disabledAttr} title="Tambah ke Keranjang">
                                                    <i data-lucide="shopping-cart" class="icon icon-sm"></i>
                                                </button>
                                                <button onclick="event.stopPropagation(); beliLangsung(${produk.id_produk}, this)" class="btn ${produk.stok > 0 ? 'btn-primary' : 'btn-outline'}" ${disabledAttr}>
                                                    ${produk.stok > 0 ? 'Beli' : 'Habis'}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                `;
                    grid.appendChild(card);
                });
                lucide.createIcons();
                renderCatalogPagination(paginationData);
            } catch (error) {
                loader.style.display = 'none';
                emptyState.style.display = 'block';
                showToast('Gagal memuat produk: ' + error.message, 'error');
            }
        }

        function renderCatalogPagination(data) {
            const wrapper = document.getElementById('catalog-pagination');
            if (!data || !data.total || data.total === 0) {
                wrapper.style.display = 'none';
                return;
            }

            const currentPage = data.current_page;
            const lastPage = data.last_page;
            const total = data.total;
            const from = (currentPage - 1) * catalogPerPage + 1;
            const to = Math.min(currentPage * catalogPerPage, total);

            let pageButtons = '';
            pageButtons += `<button class="cat-pag-btn ${currentPage <= 1 ? 'disabled' : ''}" ${currentPage > 1 ? `onclick="goToPage(${currentPage - 1})"` : ''} aria-label="Previous">
                                <i data-lucide="chevron-left" style="width:16px;height:16px;"></i>
                            </button>`;

            for (let i = 1; i <= lastPage; i++) {
                if (i === 1 || i === lastPage || (i >= currentPage - 2 && i <= currentPage + 2)) {
                    pageButtons += `<button class="cat-pag-btn ${currentPage === i ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
                } else if (i === 2 || i === lastPage - 1) {
                    pageButtons += `<span class="cat-pag-dots">...</span>`;
                }
            }

            pageButtons += `<button class="cat-pag-btn ${currentPage >= lastPage ? 'disabled' : ''}" ${currentPage < lastPage ? `onclick="goToPage(${currentPage + 1})"` : ''} aria-label="Next">
                                <i data-lucide="chevron-right" style="width:16px;height:16px;"></i>
                            </button>`;

            wrapper.innerHTML = `
                                <span class="cat-pag-info">Menampilkan ${from} - ${to} dari ${total} produk</span>
                                <div class="cat-pag-controls">${pageButtons}</div>
                            `;
            wrapper.style.display = 'flex';
            lucide.createIcons();
        }

        async function showDetailModal(idProduk) {
            const modal = document.getElementById('detail-modal');
            const content = document.getElementById('modal-content');

            content.innerHTML = '<div class="flex-center" style="padding: 60px;"><div class="loader"></div></div>';
            modal.style.display = 'flex';

            try {
                const res = await apiFetch(`/produk/${idProduk}`);
                const produk = res.data;
                const fotoUrl = produk.foto_url || `https://placehold.co/600x400/EFF8FF/0EA5E9?text=${encodeURIComponent(produk.merek || 'Laptop')}`;

                const gallerySources = Array.isArray(produk.galeri_foto_urls) ? produk.galeri_foto_urls : [];
                const allPhotos = [fotoUrl, ...gallerySources]
                    .filter(Boolean)
                    .filter((photo, index, arr) => arr.indexOf(photo) === index);

                detailGalleryPhotos = allPhotos.length > 0 ? allPhotos : [fotoUrl];
                detailGalleryIndex = 0;

                const hasMultiplePhotos = detailGalleryPhotos.length > 1;
                const galleryHtml = hasMultiplePhotos ? `
                                    <div class="product-gallery-container" style="margin-top: 12px; margin-bottom: 8px;">
                                        <div class="product-gallery-thumbnails">
                                            ${detailGalleryPhotos.map((photo, i) => `
                                                <img src="${photo}" 
                                                     class="gallery-thumbnail-img ${i === 0 ? 'active' : ''}" 
                                                     data-index="${i}"
                                                     onclick="changeDetailImageByIndex(${i})"
                                                     onerror="this.src='https://placehold.co/100x100/EFF8FF/0EA5E9?text=Laptop'">
                                            `).join('')}
                                        </div>
                                    </div>
                                ` : '';

                content.innerHTML = `
                                <div class="product-detail-grid">
                                    <div>
                                        <div class="product-detail-media-card">
                                            <button type="button" class="product-detail-slider-btn prev ${hasMultiplePhotos ? '' : 'hidden'}" onclick="slideDetailImage(-1)" aria-label="Foto sebelumnya">
                                                <i data-lucide="chevron-left" class="icon"></i>
                                            </button>
                                            <img id="main-detail-img" src="${detailGalleryPhotos[0]}" alt="${escapeHtml(produk.nama_produk)}" class="product-detail-img" onerror="this.src='https://placehold.co/600x400/EFF8FF/0EA5E9?text=Laptop'">
                                            <button type="button" class="product-detail-slider-btn next ${hasMultiplePhotos ? '' : 'hidden'}" onclick="slideDetailImage(1)" aria-label="Foto berikutnya">
                                                <i data-lucide="chevron-right" class="icon"></i>
                                            </button>
                                            <span class="product-detail-photo-counter ${hasMultiplePhotos ? '' : 'hidden'}" id="detail-photo-counter">1/${detailGalleryPhotos.length}</span>
                                        </div>
                                        ${galleryHtml}
                                        ${produk.kategori ? `<div style="margin-top: 12px;"><span class="badge badge-primary">${escapeHtml(produk.kategori)}</span></div>` : ''}
                                    </div>
                                    <div>
                                        <p class="product-detail-brand">${escapeHtml(produk.merek || '')}</p>
                                        <h2 class="product-detail-title">${escapeHtml(produk.nama_produk)}</h2>

                                        <div class="product-detail-price">${formatRupiah(produk.harga)}</div>

                                        ${produk.stok > 0
                        ? `<p class="product-detail-stok-success"><i data-lucide="check-circle" class="icon icon-sm"></i> Stok tersedia: ${produk.stok} unit</p>`
                        : `<p class="product-detail-stok-danger"><i data-lucide="x-circle" class="icon icon-sm"></i> Stok habis</p>`
                    }

                                        <div class="product-detail-warranty-box">
                                            <span><i data-lucide="shield-check" class="icon icon-sm"></i> Garansi Toko 1 Bulan</span>
                                            <span><i data-lucide="check-circle" class="icon icon-sm"></i> Laptop Siap Pakai</span>
                                        </div>

                                        <div class="spec-grid">
                                            ${produk.cpu ? `<div class="spec-item"><div class="spec-label">Processor</div><div class="spec-val">${escapeHtml(produk.cpu)}</div></div>` : ''}
                                            ${produk.ram ? `<div class="spec-item"><div class="spec-label">RAM</div><div class="spec-val">${produk.ram} GB</div></div>` : ''}
                                            ${produk.storage ? `<div class="spec-item"><div class="spec-label">Storage</div><div class="spec-val">${produk.storage} GB</div></div>` : ''}
                                            ${produk.vga ? `<div class="spec-item"><div class="spec-label">GPU / VGA</div><div class="spec-val">${escapeHtml(produk.vga)}</div></div>` : ''}
                                        </div>

                                        <div style="display: flex; gap: 12px; margin-top: 20px;">
                                            <button onclick="tambahKeKeranjang(${produk.id_produk}, this); closeDetailModal()" 
                                                class="btn btn-outline" 
                                                style="flex: 1; padding: 14px;" ${produk.stok === 0 ? 'disabled' : ''} title="Tambah ke Keranjang">
                                                <i data-lucide="shopping-cart" class="icon"></i>
                                            </button>
                                            <button onclick="beliLangsung(${produk.id_produk}, this); closeDetailModal()" 
                                                class="btn ${produk.stok > 0 ? 'btn-primary' : 'btn-outline'}" 
                                                style="flex: 2; padding: 14px;" ${produk.stok === 0 ? 'disabled' : ''}>
                                                Beli Sekarang
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                ${produk.deskripsi ? `
                                <div style="background: var(--bg-surface-alt); border: 1px solid var(--glass-border); border-radius: var(--radius-sm); padding: 18px; margin-top: 24px;">
                                    <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 10px; color: var(--text-main); display: flex; align-items: center; gap: 6px;"><i data-lucide="file-text" class="icon icon-sm text-primary"></i> Detail Produk </h4>
                                    <p style="font-size: 13px; color: var(--text-muted); line-height: 1.75; white-space: pre-line;">${escapeHtml(produk.deskripsi)}</p>
                                </div>
                                ` : ''}
                            `;
                lucide.createIcons();
            } catch (err) {
                content.innerHTML = `<p style="text-align: center; color: var(--danger); padding: 40px;">Gagal memuat detail: ${err.message}</p>`;
            }
        }

        function closeDetailModal(e) {
            if (!e || e.target === document.getElementById('detail-modal')) {
                document.getElementById('detail-modal').style.display = 'none';
            }
        }

        function updateDetailImage(index) {
            if (!detailGalleryPhotos || detailGalleryPhotos.length === 0) return;

            const total = detailGalleryPhotos.length;
            detailGalleryIndex = ((index % total) + total) % total;

            const mainImg = document.getElementById('main-detail-img');
            if (mainImg) mainImg.src = detailGalleryPhotos[detailGalleryIndex];

            document.querySelectorAll('.gallery-thumbnail-img').forEach((img, i) => {
                img.classList.toggle('active', i === detailGalleryIndex);
            });

            const counter = document.getElementById('detail-photo-counter');
            if (counter) counter.textContent = `${detailGalleryIndex + 1}/${total}`;
        }

        function slideDetailImage(direction) {
            updateDetailImage(detailGalleryIndex + direction);
        }

        function changeDetailImageByIndex(index) {
            updateDetailImage(index);
        }

        function changeDetailImage(url, el) {
            const index = detailGalleryPhotos.indexOf(url);
            updateDetailImage(index >= 0 ? index : 0);
            if (el) {
                document.querySelectorAll('.gallery-thumbnail-img').forEach(img => img.classList.remove('active'));
                el.classList.add('active');
            }
        }

        async function tambahKeKeranjang(id_produk, btn) {
            if (!isLoggedIn()) {
                window.location.href = '/login';
                return;
            }

            const origText = btn ? btn.innerHTML : '';
            if (btn) { btn.innerHTML = '<div class="loader" style="width:16px;height:16px;border-width:2px;"></div>'; btn.disabled = true; }

            try {
                await apiFetch('/keranjang', {
                    method: 'POST',
                    body: { id_produk, jumlah: 1 }
                });
                showToast('Berhasil ditambahkan ke keranjang!');
            } catch (error) {
                showToast(error.message, 'error');
            } finally {
                if (btn) { btn.innerHTML = origText; btn.disabled = false; }
            }
        }

        async function beliLangsung(id_produk, btn) {
            if (!isLoggedIn()) {
                window.location.href = '/login';
                return;
            }

            const origText = btn ? btn.innerHTML : '';
            if (btn) { btn.innerHTML = '<div class="loader" style="width:16px;height:16px;border-width:2px;"></div>'; btn.disabled = true; }

            try {
                await apiFetch('/keranjang', {
                    method: 'POST',
                    body: { id_produk, jumlah: 1 }
                });
                showToast('Menuju halaman checkout...');
                setTimeout(() => {
                    window.location.href = '/keranjang';
                }, 500);
            } catch (error) {
                showToast(error.message, 'error');
                if (btn) { btn.innerHTML = origText; btn.disabled = false; }
            }
        }


        // Sticky dinonaktifkan: filter dan produk sekarang mengikuti scroll normal halaman.
        // Dengan scroll normal, produk yang berada di atas akan hilang saat user scroll ke bawah,
        // dan muncul kembali saat user scroll ke atas.
        function initCatalogStickyTools() {
            const sticky = document.querySelector('.catalog-sticky-tools');
            const placeholder = document.querySelector('.catalog-sticky-placeholder');
            if (sticky) {
                sticky.classList.remove('is-stuck');
                sticky.removeAttribute('style');
            }
            if (placeholder) placeholder.remove();
        }

        document.addEventListener('DOMContentLoaded', initCatalogStickyTools);
    </script>
@endpush