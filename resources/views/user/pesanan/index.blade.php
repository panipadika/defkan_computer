@extends('layouts.app')

@section('title', 'Pesanan Saya — Defkan Computer')

@section('content')
    <div class="container page-container">

        {{-- Page Header --}}
        <div class="page-header" style="margin-bottom: 24px;">
            <h1 class="page-title">
                <i data-lucide="package" class="icon icon-lg text-primary page-title-icon"></i>
                <span class="text-primary-gradient">Riwayat</span> Transaksi
            </h1>
            <p class="text-muted page-subtitle">Pantau status pesanan produk dan pengajuan servis laptop Anda di sini.</p>
        </div>

        {{-- Main Switcher Tabs --}}
        <div class="main-tabs-wrapper">
            <button id="tab-orders" class="main-tab-btn active" onclick="setMainTab('orders')">
                <i data-lucide="shopping-bag" class="icon icon-sm"></i> Pembelian Produk
            </button>
            <button id="tab-services" class="main-tab-btn" onclick="setMainTab('services')">
                <i data-lucide="wrench" class="icon icon-sm"></i> Riwayat Servis
            </button>
        </div>

        {{-- Section 1: Orders --}}
        <div id="orders-section">
            {{-- Filter Tabs Modern --}}
            <div class="glass order-tabs modern-status-tabs">
                <button class="filter-tab active" data-status="" onclick="filterOrders(this, '')">
                    Semua
                </button>
                <button class="filter-tab" data-status="pending" onclick="filterOrders(this, 'pending')">
                    <i data-lucide="clock" class="icon icon-sm"></i> Pending
                </button>
                <button class="filter-tab" data-status="diproses" onclick="filterOrders(this, 'diproses')">
                    <i data-lucide="settings" class="icon icon-sm"></i> Diproses
                </button>
                <button class="filter-tab" data-status="dikirim" onclick="filterOrders(this, 'dikirim')">
                    <i data-lucide="truck" class="icon icon-sm"></i> Dikirim
                </button>
                <button class="filter-tab" data-status="selesai" onclick="filterOrders(this, 'selesai')">
                    <i data-lucide="check-circle" class="icon icon-sm"></i> Selesai
                </button>
                <button class="filter-tab" data-status="dibatalkan" onclick="filterOrders(this, 'dibatalkan')">
                    <i data-lucide="x-circle" class="icon icon-sm"></i> Dibatalkan
                </button>
            </div>

            {{-- Loading --}}
            <div id="loading-state" class="flex-center empty-state">
                <div class="loader"></div>
            </div>

            {{-- Empty State --}}
            <div id="empty-state" class="empty-state" style="display: none;">
                <div class="empty-state-icon">
                    <i data-lucide="inbox" style="width: 64px; height: 64px; stroke-width: 1.25;"></i>
                </div>
                <h3 class="text-muted empty-state-title">Belum Ada Pesanan</h3>
                <p class="text-muted empty-state-desc">Belum ada riwayat pesanan. Yuk, mulai belanja!</p>
                <a href="/produk" class="btn btn-primary">Mulai Belanja</a>
            </div>

            {{-- Orders List --}}
            <div id="orders-list" class="flex-column gap-20" style="display: none;"></div>
        </div>

        {{-- Section 2: Services --}}
        <div id="services-section" style="display: none;">
            {{-- Filter Tabs Modern --}}
            <div class="glass order-tabs modern-status-tabs service-status-tabs">
                <button class="filter-tab-service active" data-status="" onclick="filterServices(this, '')">
                    Semua
                </button>
                <button class="filter-tab-service" data-status="menunggu" onclick="filterServices(this, 'menunggu')">
                    <i data-lucide="clock" class="icon icon-sm"></i> Menunggu
                </button>
                <button class="filter-tab-service" data-status="diperiksa" onclick="filterServices(this, 'diperiksa')">
                    <i data-lucide="search" class="icon icon-sm"></i> Diperiksa
                </button>
                <button class="filter-tab-service" data-status="dikerjakan" onclick="filterServices(this, 'dikerjakan')">
                    <i data-lucide="settings" class="icon icon-sm"></i> Dikerjakan
                </button>
                <button class="filter-tab-service" data-status="selesai" onclick="filterServices(this, 'selesai')">
                    <i data-lucide="check-circle" class="icon icon-sm"></i> Selesai
                </button>
                <button class="filter-tab-service" data-status="diambil" onclick="filterServices(this, 'diambil')">
                    <i data-lucide="archive" class="icon icon-sm"></i> Diambil
                </button>
            </div>

            {{-- Loading --}}
            <div id="services-loading-state" class="flex-center empty-state" style="display: none;">
                <div class="loader"></div>
            </div>

            {{-- Empty State --}}
            <div id="services-empty-state" class="empty-state" style="display: none;">
                <div class="empty-state-icon">
                    <i data-lucide="wrench" style="width: 64px; height: 64px; stroke-width: 1.25;"></i>
                </div>
                <h3 class="text-muted empty-state-title">Belum Ada Pengajuan Servis</h3>
                <p class="text-muted empty-state-desc">Laptop Anda bermasalah? Ajukan perbaikan sekarang!</p>
                <a href="/servis" class="btn btn-primary">Ajukan Servis</a>
            </div>

            {{-- Services List --}}
            <div id="services-list" class="flex-column gap-20" style="display: none;"></div>
        </div>

        {{-- Order / Service Detail Modal --}}
        <div id="detail-modal" class="modal-overlay" style="display: none;">
            <div class="glass-panel modal-panel">
                <div class="modal-header">
                    <h2 id="detail-modal-title" class="modal-title">Detail Pesanan</h2>
                    <button onclick="closeDetailModal()" class="modal-close-btn" aria-label="Tutup">
                        <i data-lucide="x" style="width: 20px; height: 20px;"></i>
                    </button>
                </div>
                <div id="detail-content"></div>
            </div>
        </div>

        {{-- Upload Bukti Modal --}}
        <div id="upload-modal" class="modal-overlay" style="display: none;">
            <div class="glass-panel modal-panel" style="max-width: 480px;">
                <div class="modal-header">
                    <h2 class="modal-title">
                        <i data-lucide="credit-card" class="icon text-primary"></i>
                        <span id="upload-modal-title-text">Upload Bukti Pembayaran</span>
                    </h2>
                    <button onclick="closeUploadModal()" class="modal-close-btn" aria-label="Tutup">
                        <i data-lucide="x" style="width: 20px; height: 20px;"></i>
                    </button>
                </div>
                <form id="upload-form" onsubmit="handleUploadBukti(event)">
                    <input type="hidden" id="upload-target-type" value="order">
                    <input type="hidden" id="upload-pesanan-id">
                    <input type="hidden" id="upload-servis-id">

                    <div class="form-group">
                        <label class="form-label">Metode Pembayaran</label>
                        <select id="upload-metode" class="form-control" required>
                            <option value="">— Pilih Metode —</option>
                            <option value="QRIS">QRIS</option>
                            <option value="Transfer Bank SeaBank">Transfer Bank SeaBank</option>
                            <option value="Transfer Bank BCA">Transfer Bank BCA</option>
                            <option value="Transfer Bank BRI">Transfer Bank BRI</option>
                            <option value="BCA Virtual Account">BCA Virtual Account</option>
                            <option value="Mandiri Virtual Account">Mandiri Virtual Account</option>
                            <option value="BNI Virtual Account">BNI Virtual Account</option>
                            <option value="BRI Virtual Account">BRI Virtual Account</option>
                            <option value="CIMB Niaga Virtual Account">CIMB Niaga Virtual Account</option>
                            <option value="DANA">DANA</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Bukti Pembayaran (Gambar)</label>
                        <input type="file" id="upload-file" class="form-control" accept="image/*" required
                            style="padding: 10px;">
                    </div>
                    <div id="upload-preview" style="display: none; margin-bottom: 16px; text-align: center;">
                        <img id="preview-img"
                            style="max-height: 200px; border-radius: var(--radius-sm); border: 1px solid var(--glass-border);">
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <button type="submit" class="btn btn-primary" style="flex: 1;" id="btn-upload">Kirim Bukti</button>
                        <button type="button" class="btn btn-outline" style="flex: 1;"
                            onclick="closeUploadModal()">Batal</button>
                    </div>
                </form>
        </div>

        {{-- Review Modal --}}
        <div id="review-modal" class="modal-overlay" style="display: none;">
            <div class="glass-panel modal-panel" style="max-width: 480px;">
                <div class="modal-header">
                    <h2 class="modal-title">
                        <i data-lucide="star" class="icon text-primary"></i>
                        <span>Beri Ulasan</span>
                    </h2>
                    <button onclick="closeReviewModal()" class="modal-close-btn" aria-label="Tutup">
                        <i data-lucide="x" style="width: 20px; height: 20px;"></i>
                    </button>
                </div>
                <form id="review-form" onsubmit="handleReviewSubmit(event)">
                    <input type="hidden" id="review-tipe">
                    <input type="hidden" id="review-pesanan-id">
                    <input type="hidden" id="review-produk-id">
                    <input type="hidden" id="review-servis-id">

                    <div style="text-align: center; margin-bottom: 20px;">
                        <h4 id="review-item-name" style="font-size: 15px; font-weight: 600; margin-bottom: 12px; color: var(--text-main);">Nama Produk/Servis</h4>
                        <div class="rating-stars-input" style="display: flex; justify-content: center; gap: 8px;">
                            <span class="star-btn" data-value="1" onclick="setRatingValue(1)">★</span>
                            <span class="star-btn" data-value="2" onclick="setRatingValue(2)">★</span>
                            <span class="star-btn" data-value="3" onclick="setRatingValue(3)">★</span>
                            <span class="star-btn" data-value="4" onclick="setRatingValue(4)">★</span>
                            <span class="star-btn" data-value="5" onclick="setRatingValue(5)">★</span>
                        </div>
                        <input type="hidden" id="review-rating-val" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Komentar / Ulasan</label>
                        <textarea id="review-komentar" class="form-control" rows="4" placeholder="Bagikan pengalaman Anda..." style="padding: 10px; background: rgba(0, 0, 0, 0.2); color: var(--text-main); border: 1px solid var(--glass-border); border-radius: var(--radius-sm); width: 100%;" required></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Foto Bukti (Maksimal 3)</label>
                        <input type="file" id="review-foto" class="form-control" accept="image/*" multiple style="padding: 10px;">
                    </div>

                    <div style="display: flex; gap: 12px; margin-top: 20px;">
                        <button type="submit" class="btn btn-primary" style="flex: 1;" id="btn-submit-review">Kirim Ulasan</button>
                        <button type="button" class="btn btn-outline" style="flex: 1;" onclick="closeReviewModal()">Batal</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Complaint Modal --}}
        <div id="complaint-modal" class="modal-overlay" style="display: none;">
            <div class="glass-panel modal-panel" style="max-width: 520px;">
                <div class="modal-header">
                    <h2 class="modal-title">
                        <i data-lucide="alert-circle" class="icon text-primary"></i>
                        <span>Laporkan Masalah (Komplain)</span>
                    </h2>
                    <button onclick="closeComplaintModal()" class="modal-close-btn" aria-label="Tutup">
                        <i data-lucide="x" style="width: 20px; height: 20px;"></i>
                    </button>
                </div>
                <form id="complaint-form" onsubmit="handleComplaintSubmit(event)">
                    <input type="hidden" id="complaint-tipe">
                    <input type="hidden" id="complaint-ref-id">

                    <h4 id="complaint-ref-title" style="font-size: 14px; font-weight: 600; color: var(--text-muted); margin-bottom: 16px;">Pesanan #123</h4>

                    <div class="form-group">
                        <label class="form-label">Judul Masalah / Komplain</label>
                        <input type="text" id="complaint-judul" class="form-control" required placeholder="Contoh: Barang Rusak saat Diterima, dll" style="padding: 10px; background: rgba(0, 0, 0, 0.2); color: var(--text-main); border: 1px solid var(--glass-border); border-radius: var(--radius-sm); width: 100%;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Deskripsi Masalah secara Detail</label>
                        <textarea id="complaint-deskripsi" class="form-control" rows="5" required placeholder="Jelaskan kendala yang Anda hadapi agar kami bisa memberikan solusi terbaik..." style="padding: 10px; background: rgba(0, 0, 0, 0.2); color: var(--text-main); border: 1px solid var(--glass-border); border-radius: var(--radius-sm); width: 100%;"></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Foto Bukti Pendukung (Maksimal 3)</label>
                        <input type="file" id="complaint-foto" class="form-control" accept="image/*" multiple style="padding: 10px;">
                    </div>

                    <div style="display: flex; gap: 12px; margin-top: 20px;">
                        <button type="submit" class="btn btn-primary" style="flex: 1;" id="btn-submit-complaint">Kirim Laporan</button>
                        <button type="button" class="btn btn-outline" style="flex: 1;" onclick="closeComplaintModal()">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        #detail-modal .modal-panel,
        #upload-modal .modal-panel,
        #review-modal .modal-panel,
        #complaint-modal .modal-panel {
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            padding: 24px;
            position: relative;
        }

        .star-btn {
            font-size: 36px;
            cursor: pointer;
            color: var(--text-muted);
            transition: color 0.15s, transform 0.15s;
            user-select: none;
        }
        .star-btn:hover {
            transform: scale(1.25);
        }

        #detail-modal .modal-panel {
            max-width: 760px;
            width: 90%;
        }

        #detail-modal .modal-header,
        #upload-modal .modal-header {
            flex-shrink: 0;
            margin-bottom: 16px;
            border-bottom: 1px solid var(--glass-border);
            padding-bottom: 12px;
        }

        #detail-modal #detail-content,
        #upload-modal form {
            flex: 1;
            overflow-y: auto;
            padding-right: 6px;
        }

        #detail-modal #detail-content::-webkit-scrollbar,
        #upload-modal form::-webkit-scrollbar {
            width: 6px;
        }

        #detail-modal #detail-content::-webkit-scrollbar-track,
        #upload-modal form::-webkit-scrollbar-track {
            background: transparent;
        }

        #detail-modal #detail-content::-webkit-scrollbar-thumb,
        #upload-modal form::-webkit-scrollbar-thumb {
            background: var(--glass-border-hover);
            border-radius: 4px;
        }

        /* Main switcher */
        .main-tabs-wrapper {
            display: flex;
            gap: 8px;
            margin-bottom: 18px;
            background: rgba(30, 41, 59, 0.4);
            padding: 6px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--glass-border);
            width: fit-content;
        }

        .main-tab-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: var(--radius-md);
            border: none;
            background: transparent;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.25s;
        }

        .main-tab-btn.active {
            background: linear-gradient(135deg, var(--primary) 0%, #1d4ed8 100%) !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .main-tab-btn:hover:not(.active) {
            color: var(--text-main);
            background: rgba(255, 255, 255, 0.05);
        }

        /* Modern status tabs */
        .modern-status-tabs {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 14px;
            margin-bottom: 18px;
            border-radius: 18px;
            border: 1px solid var(--glass-border, rgba(226, 232, 240, 0.9));
            background: var(--bg-surface, #ffffff);
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
            overflow-x: auto;
            overflow-y: hidden;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .modern-status-tabs::-webkit-scrollbar {
            display: none;
        }

        .modern-status-tabs .filter-tab,
        .modern-status-tabs .filter-tab-service {
            height: 42px;
            padding: 0 20px;
            border-radius: 999px;
            border: 1px solid var(--glass-border, #e5e7eb);
            background: var(--bg-surface, #ffffff);
            color: var(--text-muted, #64748b);
            font-size: 14px;
            font-weight: 700;
            font-family: inherit;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            white-space: nowrap;
            cursor: pointer;
            box-shadow: 0 3px 9px rgba(15, 23, 42, 0.025);
            transition: all 0.22s ease;
            flex: 0 0 auto;
        }

        .modern-status-tabs .filter-tab:hover,
        .modern-status-tabs .filter-tab-service:hover {
            border-color: rgba(37, 99, 235, 0.34);
            color: var(--primary, #2563eb);
            background: rgba(37, 99, 235, 0.04);
            transform: translateY(-1px);
        }

        .modern-status-tabs .filter-tab.active,
        .modern-status-tabs .filter-tab-service.active {
            border-color: var(--primary, #2563eb);
            background: var(--primary, #2563eb);
            color: #ffffff;
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.22);
        }

        .modern-status-tabs .filter-tab svg,
        .modern-status-tabs .filter-tab-service svg {
            width: 17px;
            height: 17px;
            flex-shrink: 0;
        }

        .modern-status-tabs .filter-tab.active svg,
        .modern-status-tabs .filter-tab-service.active svg {
            color: #ffffff;
        }

        @media (max-width: 768px) {
            .main-tabs-wrapper {
                width: 100%;
                overflow-x: auto;
            }

            .main-tab-btn {
                flex: 1;
                justify-content: center;
                padding: 9px 12px;
                font-size: 13px;
            }

            .modern-status-tabs {
                padding: 10px;
                gap: 8px;
                border-radius: 16px;
            }

            .modern-status-tabs .filter-tab,
            .modern-status-tabs .filter-tab-service {
                height: 38px;
                padding: 0 15px;
                font-size: 13px;
            }
        }

        /* Service card styles */
        .service-card {
            border-radius: var(--radius-lg);
            border: 1px solid var(--glass-border);
            background: var(--bg-card);
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            transition: transform 0.2s, border-color 0.2s;
        }

        .service-card:hover {
            border-color: var(--glass-border-hover);
            transform: translateY(-2px);
        }

        .service-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--glass-border);
            padding-bottom: 14px;
        }

        .service-code {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 16px;
            color: var(--text-main);
        }

        .service-date {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .service-body {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .service-info-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .service-info-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .service-info-value {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-main);
        }

        .service-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid var(--glass-border);
            padding-top: 18px;
            margin-top: 4px;
        }

        .service-cost-label {
            font-size: 12px;
            color: var(--text-muted);
        }

        .service-cost-value {
            font-family: 'Outfit', sans-serif;
            font-size: 18px;
            font-weight: 800;
            color: var(--text-main);
        }

        .service-payment-instructions {
            margin-top: 16px;
            border: 1px solid rgba(37, 99, 235, 0.15);
            border-radius: 14px;
            padding: 14px;
            background: rgba(37, 99, 235, 0.04);
        }

        .service-payment-instructions-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 10px;
        }

        .service-payment-instructions-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .service-pay-card {
            padding: 12px;
            border-radius: 12px;
            border: 1px solid var(--glass-border);
            background: var(--bg-surface, #fff);
        }

        .service-pay-card strong {
            display: block;
            font-size: 13px;
            margin-bottom: 6px;
        }

        .service-pay-card span {
            font-size: 13px;
            color: var(--text-muted);
        }

        @media (max-width: 768px) {
            .service-payment-instructions-grid {
                grid-template-columns: 1fr;
            }

            .service-footer {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
        }

        /* Service status colors */
        .status-badge.status-menunggu {
            background: rgba(245, 158, 11, 0.15) !important;
            border: 1px solid rgba(245, 158, 11, 0.3) !important;
            color: #f59e0b !important;
        }

        .status-badge.status-diperiksa {
            background: rgba(168, 85, 247, 0.15) !important;
            border: 1px solid rgba(168, 85, 247, 0.3) !important;
            color: #a855f7 !important;
        }

        .status-badge.status-dikerjakan {
            background: rgba(59, 130, 246, 0.15) !important;
            border: 1px solid rgba(59, 130, 246, 0.3) !important;
            color: #3b82f6 !important;
        }

        .status-badge.status-selesai {
            background: rgba(16, 185, 129, 0.15) !important;
            border: 1px solid rgba(16, 185, 129, 0.3) !important;
            color: #10b981 !important;
        }

        .status-badge.status-diambil {
            background: rgba(100, 116, 139, 0.15) !important;
            border: 1px solid rgba(100, 116, 139, 0.3) !important;
            color: #94a3b8 !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        window.onerror = function(message, source, lineno, colno, error) {
            alert("JS Error: " + message + " at line " + lineno + ":" + colno + "\nSource: " + source);
            return false;
        };

        document.addEventListener('DOMContentLoaded', () => {
            if (!isLoggedIn()) {
                window.location.href = '/login';
                return;
            }
            const params = new URLSearchParams(window.location.search);
            const tab = params.get('tab');

            if (tab === 'services') {
                setMainTab('services');
            } else {
                loadOrders();
            }

            const uploadFile = document.getElementById('upload-file');
            if (uploadFile) {
                uploadFile.addEventListener('change', function () {
                    const file = this.files[0];
                    const preview = document.getElementById('upload-preview');
                    const img = document.getElementById('preview-img');
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = e => { img.src = e.target.result; preview.style.display = 'block'; };
                        reader.readAsDataURL(file);
                    } else {
                        preview.style.display = 'none';
                    }
                });
            }
        });

        let allOrders = [];
        let currentFilter = '';
        let allServices = [];
        let currentServiceFilter = '';
        let activeMainTab = 'orders';

        function setMainTab(tab) {
            activeMainTab = tab;

            document.querySelectorAll('.main-tab-btn').forEach(btn => btn.classList.remove('active'));
            document.getElementById(`tab-${tab}`).classList.add('active');

            if (tab === 'orders') {
                document.getElementById('orders-section').style.display = 'block';
                document.getElementById('services-section').style.display = 'none';
                if (allOrders.length === 0) {
                    loadOrders();
                }
            } else {
                document.getElementById('orders-section').style.display = 'none';
                document.getElementById('services-section').style.display = 'block';
                if (allServices.length === 0) {
                    loadServices();
                }
            }

            if (window.lucide) lucide.createIcons();
        }

        async function loadServices() {
            const loading = document.getElementById('services-loading-state');
            const empty = document.getElementById('services-empty-state');
            const list = document.getElementById('services-list');

            loading.style.display = 'flex';
            empty.style.display = 'none';
            list.style.display = 'none';

            try {
                const res = await apiFetch('/servis-saya');
                allServices = res.data || [];

                loading.style.display = 'none';

                if (allServices.length === 0) {
                    empty.style.display = 'block';
                    list.style.display = 'none';
                    return;
                }

                empty.style.display = 'none';
                list.style.display = 'flex';
                const filtered = currentServiceFilter ? allServices.filter(s => s.status === currentServiceFilter) : allServices;
                renderServices(filtered);
            } catch (err) {
                loading.style.display = 'none';
                empty.style.display = 'block';
                showToast(err.message, 'error');
            }
        }

        function filterServices(btn, status) {
            document.querySelectorAll('.filter-tab-service').forEach(t => t.classList.remove('active'));
            btn.classList.add('active');
            currentServiceFilter = status;

            const filtered = status ? allServices.filter(s => s.status === status) : allServices;
            renderServices(filtered);
        }

        function normalizeServicePaymentStatus(service = {}) {
            const raw = String(service.status_pembayaran || service.payment_status || '').toLowerCase();
            if (['dibayar', 'paid', 'lunas', 'success', 'settlement', 'sukses'].includes(raw)) return 'dibayar';
            if (service.bukti_pembayaran_url || service.bukti_pembayaran) return 'dibayar';
            return 'pending';
        }

        function getServicePaymentLabel(service = {}) {
            return normalizeServicePaymentStatus(service) === 'dibayar' ? 'Dibayar' : 'Belum Dibayar';
        }

        function getServicePaymentMethod(service = {}) {
            return service.metode_pembayaran || service.metode_pembayaran_label || 'Belum dipilih';
        }

        function getServiceProofUrl(service = {}) {
            const raw = service.bukti_pembayaran_url || service.bukti_pembayaran || '';
            if (!raw) return '';
            if (String(raw).startsWith('http') || String(raw).startsWith('/')) return raw;
            if (String(raw).startsWith('storage/')) return '/' + raw;
            return '/storage/' + raw;
        }

        function getServiceTotal(service = {}) {
            return Number(service.total_biaya_final || service.total_biaya || service.estimasi_biaya || 0);
        }

        function getServiceId(service = {}) {
            return service.id || service.id_servis || service.servis_id || '';
        }

        function renderServices(services) {
            const list = document.getElementById('services-list');
            const empty = document.getElementById('services-empty-state');

            if (services.length === 0) {
                list.style.display = 'none';
                empty.style.display = 'block';
                return;
            }

            empty.style.display = 'none';
            list.style.display = 'flex';
            list.innerHTML = '';

            services.forEach(service => {
                const date = new Date(service.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                const totalBiaya = getServiceTotal(service);
                const biaya = totalBiaya > 0 ? formatRupiah(totalBiaya) : 'Menunggu konfirmasi';
                const payStatus = normalizeServicePaymentStatus(service);
                const payLabel = getServicePaymentLabel(service);
                const payMethod = getServicePaymentMethod(service);
                const serviceIdentifier = getServiceId(service) || service.kode_servis;

                const el = document.createElement('div');
                el.className = 'glass service-card';
                el.onclick = () => showServiceDetail(serviceIdentifier);
                el.innerHTML = `
                    <div class="service-header">
                        <div>
                            <div class="service-code">${escapeHtml(service.kode_servis || '-')}</div>
                            <div class="service-date">Daftar: ${date}</div>
                        </div>
                        <span class="status-badge status-${escapeHtml(service.status || '')}">${String(service.status || '').toUpperCase()}</span>
                    </div>

                    <div class="service-body">
                        <div class="service-info-group">
                            <div class="service-info-label">Perangkat</div>
                            <div class="service-info-value">${escapeHtml(service.merek_laptop || service.nama_perangkat || '-')}</div>
                        </div>
                        <div class="service-info-group">
                            <div class="service-info-label">Jenis Kerusakan</div>
                            <div class="service-info-value">${escapeHtml(service.jenis_kerusakan || '-')}</div>
                        </div>
                        <div class="service-info-group">
                            <div class="service-info-label">Pembayaran</div>
                            <div class="service-info-value">
                                <span class="status-badge ${payStatus === 'dibayar' ? 'status-selesai' : 'status-menunggu'}" style="font-size:11px; padding:3px 8px;">${payLabel}</span>
                                <div class="text-muted" style="font-size:12px; margin-top:5px;">${escapeHtml(payMethod)}</div>
                            </div>
                        </div>
                        <div class="service-info-group">
                            <div class="service-info-label">Total Pembayaran</div>
                            <div class="service-info-value">${biaya}</div>
                        </div>
                        ${service.deskripsi ? `
                        <div class="service-info-group" style="grid-column: span 2;">
                            <div class="service-info-label">Keluhan</div>
                            <div class="service-info-value" style="font-size: 13px; line-height: 1.5; color: var(--text-muted);">${escapeHtml(service.deskripsi)}</div>
                        </div>
                        ` : ''}
                        <div class="service-info-group" style="grid-column: 1 / -1; background: rgba(255, 255, 255, 0.02); padding: 12px; border-radius: 8px; border: 1px solid var(--glass-border); margin-top: 8px;">
                            <div class="service-info-label" style="color: var(--primary); display: flex; align-items: center; gap: 6px;">
                                <i data-lucide="message-square" class="icon icon-xs"></i> Catatan Teknisi / Admin
                            </div>
                            <div class="service-info-value" style="font-size: 13px; line-height: 1.5; color: var(--text-main); margin-top: 4px;">
                                ${service.keterangan ? escapeHtml(service.keterangan) : '<span style="font-style: italic; opacity: 0.5;">Belum ada catatan dari teknisi/admin</span>'}
                            </div>
                        </div>
                    </div>

                    <div class="service-footer">
                        <div>
                            <div class="service-cost-label">Total Servis:</div>
                            <div class="service-cost-value">${biaya}</div>
                        </div>
                        <div style="display: flex; gap: 8px; flex-wrap: wrap; justify-content: flex-end;">
                            ${payStatus !== 'dibayar' && getServiceId(service) ? `
                            <button type="button" class="btn btn-primary" style="padding: 6px 14px; font-size: 13px;" onclick="event.stopPropagation(); openServiceUploadModal('${escapeAttr(getServiceId(service))}')">
                                Upload Bukti <i data-lucide="upload" class="icon icon-sm"></i>
                            </button>
                            ` : ''}
                            <button type="button" class="btn btn-outline" style="padding: 6px 14px; font-size: 13px;" onclick="event.stopPropagation(); showServiceDetail('${escapeAttr(serviceIdentifier)}')">
                                Detail <i data-lucide="arrow-right" class="icon icon-sm"></i>
                            </button>
                        </div>
                    </div>
                `;
                list.appendChild(el);
            });
            lucide.createIcons();
        }

        function showServiceDetail(identifier) {
            const service = allServices.find(s =>
                String(s.id) === String(identifier) ||
                String(s.id_servis) === String(identifier) ||
                String(s.kode_servis) === String(identifier)
            );
            if (!service) {
                showToast('Data servis tidak ditemukan.', 'error');
                return;
            }

            const modal = document.getElementById('detail-modal');
            const content = document.getElementById('detail-content');
            const title = document.getElementById('detail-modal-title');
            const serviceId = getServiceId(service);
            const proofUrl = getServiceProofUrl(service);
            const payStatusRaw = normalizeServicePaymentStatus(service);
            const payStatus = getServicePaymentLabel(service);
            const payMethod = getServicePaymentMethod(service);
            const totalBiaya = getServiceTotal(service);
            const paymentTime = service.waktu_pembayaran
                ? new Date(service.waktu_pembayaran).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })
                : 'Belum ada pembayaran';

            if (title) title.textContent = 'Detail Servis';

            let serviceReviewHtml = '';
            const sStatus = String(service.status || '').toLowerCase();
            if (sStatus === 'diambil' || sStatus === 'selesai') {
                if (service.ulasan) {
                    const stars = Array.from({length: 5}, (_, i) => 
                        `<span style="color:${i < service.ulasan.rating ? '#F59E0B' : '#64748b'}; font-size:20px;">★</span>`
                    ).join('');
                    serviceReviewHtml = `
                        <div class="glass" style="padding: 16px; border-radius: 12px; margin-top: 16px; background: rgba(255,255,255,0.01);">
                            <div class="service-info-label" style="margin-bottom: 6px;">Ulasan Anda</div>
                            <div style="display:flex; gap:2px; margin-bottom: 8px;">${stars}</div>
                            <p style="font-size: 13px; line-height: 1.5; margin: 0; color: var(--text-main);">${escapeHtml(service.ulasan.komentar || 'Tidak ada komentar.')}</p>
                        </div>
                    `;
                } else {
                    serviceReviewHtml = `
                        <div style="margin-top: 16px;">
                            <button type="button" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 13px;"
                                onclick="closeDetailModal(); openReviewModal('servis', null, null, ${service.id})">
                                ⭐ Beri Ulasan Servis
                            </button>
                        </div>
                    `;
                }
            }

            let serviceComplaintHtml = '';
            if (sStatus === 'diambil' || sStatus === 'selesai') {
                if (service.complaint) {
                    const statusColors = {
                        menunggu: { bg: 'rgba(245,158,11,0.15)', color: '#f59e0b', label: 'Menunggu Respon' },
                        diproses: { bg: 'rgba(59,130,246,0.15)', color: '#3b82f6', label: 'Sedang Diproses' },
                        selesai:  { bg: 'rgba(16, 185, 129, 0.15)', color: '#10b981', label: 'Komplain Selesai' },
                        ditolak:  { bg: 'rgba(239, 68, 68, 0.15)', color: '#ef4444', label: 'Komplain Ditolak' },
                    };
                    const c = service.complaint;
                    const sc = statusColors[c.status] || { bg: '#eee', color: '#555', label: c.status };
                    serviceComplaintHtml = `
                        <div class="glass" style="padding: 16px; border-radius: 12px; margin-top: 16px; border-left: 4px solid ${sc.color}; background: rgba(255,255,255,0.01);">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                                <div style="font-size:11px; font-weight:600; color:var(--text-muted); text-transform:uppercase;">Status Komplain</div>
                                <span class="status-badge" style="background:${sc.bg}; color:${sc.color}; font-size:11px; padding:3px 8px; border-radius:6px; font-weight:600;">${sc.label}</span>
                            </div>
                            <p style="font-size: 14px; font-weight: 600; margin: 0 0 4px; color: var(--text-main);">${escapeHtml(c.judul)}</p>
                            <p class="text-muted" style="font-size: 13px; margin: 0 0 10px;">${escapeHtml(c.deskripsi)}</p>
                            ${c.respons_admin ? `
                                <div style="margin-top:10px; padding:10px; background:rgba(255,255,255,0.03); border-radius:8px; border:1px solid var(--glass-border);">
                                    <div style="font-size:11px; font-weight:600; color:${sc.color}; margin-bottom:4px;">Tanggapan Admin:</div>
                                    <div style="font-size:13px; color: var(--text-main);">${escapeHtml(c.respons_admin)}</div>
                                </div>
                            ` : ''}
                        </div>
                    `;
                } else {
                    serviceComplaintHtml = `
                        <div style="margin-top:12px;">
                            <button type="button" class="btn btn-outline" style="width: 100%; padding: 12px; font-size: 13px; border-color: #D97706; color: #D97706; background: transparent;" 
                                onclick="closeDetailModal(); openComplaintModal('servis', ${service.id})">
                                <i data-lucide="alert-circle" class="icon icon-sm" style="color:#D97706; margin-right:6px;"></i> Laporkan Masalah / Komplain
                            </button>
                        </div>
                    `;
                }
            }

            content.innerHTML = `
                <div class="detail-section">
                    <h3 style="margin:0 0 14px;">Detail Servis ${escapeHtml(service.kode_servis || '')}</h3>
                    <div style="display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:14px; margin-bottom:16px;">
                        <div class="glass" style="padding:16px; border-radius:12px;">
                            <div class="service-info-label">Data Perangkat</div>
                            <p><strong>${escapeHtml(service.merek_laptop || service.nama_perangkat || '-')}</strong></p>
                            <p class="text-muted">Kerusakan: ${escapeHtml(service.jenis_kerusakan || '-')}</p>
                            <p class="text-muted">Keluhan: ${escapeHtml(service.deskripsi || '-')}</p>
                        </div>
                        <div class="glass" style="padding:16px; border-radius:12px;">
                            <div class="service-info-label">Status Servis</div>
                            <p><span class="status-badge status-${escapeHtml(service.status || '')}">${String(service.status || '').toUpperCase()}</span></p>
                            <p class="text-muted">Tanggal: ${new Date(service.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</p>
                            <p class="text-muted">Kode: ${escapeHtml(service.kode_servis || '-')}</p>
                        </div>
                    </div>

                    <div class="glass" style="padding:16px; border-radius:12px; margin-bottom:16px;">
                        <div class="service-info-label">Pembayaran</div>
                        <div style="display:grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap:12px; margin-top:10px;">
                            <div>
                                <div class="text-muted" style="font-size:12px;">Status</div>
                                <strong>
                                    <span class="status-badge ${payStatusRaw === 'dibayar' ? 'status-selesai' : 'status-menunggu'}" style="font-size:11px; padding:3px 8px;">${escapeHtml(payStatus)}</span>
                                </strong>
                            </div>
                            <div><div class="text-muted" style="font-size:12px;">Metode</div><strong>${escapeHtml(payMethod)}</strong></div>
                            <div><div class="text-muted" style="font-size:12px;">Total</div><strong>${formatRupiah(totalBiaya)}</strong></div>
                        </div>
                        <div class="text-muted" style="font-size:12px; margin-top:10px;">Waktu Pembayaran: ${escapeHtml(paymentTime)}</div>
                        <div style="margin-top:14px;">
                            <div class="service-info-label">Bukti Pembayaran</div>
                            ${proofUrl ? `
                                <a href="${escapeAttr(proofUrl)}" target="_blank" title="Buka bukti pembayaran">
                                    <img src="${escapeAttr(proofUrl)}" alt="Bukti pembayaran" style="max-width:280px; max-height:220px; object-fit:contain; border-radius:10px; border:1px solid var(--glass-border); margin-top:8px;">
                                </a>
                            ` : '<p class="text-muted">Belum ada bukti pembayaran.</p>'}
                        </div>
                    </div>

                    ${payStatusRaw !== 'dibayar' ? `
                    <div class="service-payment-instructions">
                        <div class="service-payment-instructions-title">
                            <i data-lucide="alert-circle" class="icon icon-sm text-warning"></i>
                            Pembayaran Servis Belum Dikirim
                        </div>
                        <p class="text-muted" style="font-size:13px; margin:0 0 12px;">Silakan lakukan pembayaran sesuai nominal, lalu upload bukti pembayaran agar status berubah menjadi <strong>Dibayar</strong>.</p>
                        <div class="service-payment-instructions-grid">
                            <div class="service-pay-card">
                                <strong>QRIS</strong>
                                <span>Scan QRIS toko melalui GoPay, OVO, DANA, LinkAja, atau m-Banking.</span>
                            </div>
                            <div class="service-pay-card">
                                <strong>Virtual Account / Transfer</strong>
                                <span>Pilih metode pembayaran pada form upload bukti pembayaran.</span>
                            </div>
                        </div>
                        ${serviceId ? `
                        <button class="btn btn-primary" style="width:100%; margin-top:14px; padding:12px;" onclick="openServiceUploadModal('${escapeAttr(serviceId)}')">
                            <i data-lucide="upload-cloud" class="icon"></i> Upload Bukti Pembayaran Servis
                        </button>
                        ` : `
                        <div class="text-muted" style="font-size:12px; margin-top:12px;">ID servis tidak ditemukan, coba refresh halaman terlebih dahulu.</div>
                        `}
                    </div>
                    ` : ''}

                    <div class="glass" style="padding:16px; border-radius:12px; margin-top:16px;">
                        <div class="service-info-label">Catatan Teknisi / Admin</div>
                        <p>${service.keterangan ? escapeHtml(service.keterangan) : '<span class="text-muted">Belum ada catatan.</span>'}</p>
                    </div>

                    ${serviceReviewHtml}
                    ${serviceComplaintHtml}

                    <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:18px;">
                        <a class="btn btn-outline" href="/servis/track?code=${encodeURIComponent(service.kode_servis || '')}">Lacak Status</a>
                        <button class="btn btn-primary" onclick="closeDetailModal()">Tutup</button>
                    </div>
                </div>
            `;
            modal.style.display = 'flex';
            lucide.createIcons();
        }

        async function loadOrders() {
            const loading = document.getElementById('loading-state');
            const empty = document.getElementById('empty-state');
            const list = document.getElementById('orders-list');

            try {
                const res = await apiFetch('/pesanan-saya');
                allOrders = res.data?.data || res.data || [];

                loading.style.display = 'none';

                if (allOrders.length === 0) {
                    empty.style.display = 'block';
                    list.style.display = 'none';
                    return;
                }

                empty.style.display = 'none';
                list.style.display = 'flex';
                const filtered = currentFilter ? allOrders.filter(o => o.status === currentFilter) : allOrders;
                renderOrders(filtered);
            } catch (err) {
                loading.style.display = 'none';
                empty.style.display = 'block';
                showToast(err.message, 'error');
            }
        }

        function filterOrders(btn, status) {
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            btn.classList.add('active');
            currentFilter = status;

            const filtered = status ? allOrders.filter(o => o.status === status) : allOrders;
            renderOrders(filtered);
        }

        function renderOrders(orders) {
            const list = document.getElementById('orders-list');
            const empty = document.getElementById('empty-state');

            if (orders.length === 0) {
                list.style.display = 'none';
                empty.style.display = 'block';
                return;
            }

            empty.style.display = 'none';
            list.style.display = 'flex';
            list.innerHTML = '';

            orders.forEach(order => {
                const date = new Date(order.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                const details = order.detail || order.details || [];
                const productPreview = details.slice(0, 3).map(d => {
                    const p = d.produk || {};
                    return `<img src="${p.foto_url || '/img/placeholder.png'}" alt="" class="order-product-img" loading="lazy">`;
                }).join('');
                const moreCount = details.length > 3 ? `<span class="text-muted" style="font-size:12px;">+${details.length - 3} lainnya</span>` : '';

                const el = document.createElement('div');
                el.className = 'glass order-card';
                el.onclick = () => showOrderDetail(order.id_pesanan);
                el.innerHTML = `
                    <div class="order-card-header">
                        <div class="order-card-meta">
                            <div class="order-card-meta-id">Pesanan #${order.id_pesanan}</div>
                            <div>${date}</div>
                        </div>
                        <span class="status-badge status-${order.status}">${order.status}</span>
                    </div>
                    <div class="order-card-previews">
                        ${productPreview} ${moreCount}
                    </div>
                    <div class="order-card-footer">
                        <div>
                            <span class="order-card-total-lbl">Total:</span>
                            <span class="order-card-total-val">${formatRupiah(order.total_harga)}</span>
                        </div>
                        <div class="gap-8">
                            <span class="btn btn-outline" style="padding: 6px 14px; font-size: 13px;">
                                ${String(order.status || '').toLowerCase() === 'pending' ? 'Detail & Pembayaran' : 'Detail'} <i data-lucide="arrow-right" class="icon icon-sm"></i>
                            </span>
                        </div>
                    </div>
                `;
                list.appendChild(el);
            });
            lucide.createIcons();
        }

        async function showOrderDetail(id) {
            const modal = document.getElementById('detail-modal');
            const content = document.getElementById('detail-content');
            const title = document.getElementById('detail-modal-title');
            if (title) title.textContent = 'Detail Pesanan';
            content.innerHTML = '<div class="flex-center" style="padding: 40px;"><div class="loader"></div></div>';
            modal.style.display = 'flex';

            try {
                const res = await apiFetch(`/pesanan/${id}`);
                const order = res.data;
                const oStatus = String(order.status || '').toLowerCase();
                const details = order.detail || order.details || [];
                const date = new Date(order.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });

                let productsHtml = details.map(d => {
                    const p = d.produk || {};
                    const ulasanList = order.ulasan || [];
                    const isReviewed = ulasanList.some(r => Number(r.id_produk) === Number(p.id_produk));
                    
                    let reviewBtn = '';
                    if (oStatus === 'selesai') {
                        if (isReviewed) {
                            reviewBtn = `<span class="status-badge status-selesai" style="font-size: 11px; padding: 4px 10px; margin-top: 6px; display: inline-block;">★ Sudah Direview</span>`;
                        } else {
                            reviewBtn = `
                                <button type="button" class="btn btn-primary" style="padding: 6px 12px; font-size: 11px; border-radius: 6px; margin-top: 6px;" 
                                    onclick="event.stopPropagation(); closeDetailModal(); openReviewModal('produk', ${order.id_pesanan}, ${p.id_produk}, null)">
                                    Beri Review
                                </button>
                            `;
                        }
                    }

                    return `
                        <div class="order-product-row" style="display: flex; align-items: center; justify-content: space-between; gap: 12px; border-bottom: 1px solid var(--glass-border); padding-bottom: 10px; margin-bottom: 10px;">
                            <div style="display: flex; gap: 12px; align-items: center; flex: 1;">
                                <img src="${p.foto_url || '/img/placeholder.png'}" alt="" class="order-product-img" loading="lazy" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover;">
                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-size: 14px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${p.nama_produk || 'Produk'}</div>
                                    <div class="text-muted" style="font-size: 12px;">${d.jumlah}x @ ${formatRupiah(d.harga)}</div>
                                    ${reviewBtn}
                                </div>
                            </div>
                            <div style="font-weight: 600; font-size: 14px; text-align: right;">${formatRupiah(d.harga * d.jumlah)}</div>
                        </div>
                    `;
                }).join('');

                let complaintHtml = '';
                if (oStatus === 'selesai') {
                    if (order.complaint) {
                        const statusColors = {
                            menunggu: { bg: 'rgba(245,158,11,0.15)', color: '#f59e0b', label: 'Menunggu Respon' },
                            diproses: { bg: 'rgba(59,130,246,0.15)', color: '#3b82f6', label: 'Sedang Diproses' },
                            selesai:  { bg: 'rgba(16, 185, 129, 0.15)', color: '#10b981', label: 'Komplain Selesai' },
                            ditolak:  { bg: 'rgba(239, 68, 68, 0.15)', color: '#ef4444', label: 'Komplain Ditolak' },
                        };
                        const c = order.complaint;
                        const sc = statusColors[c.status] || { bg: '#eee', color: '#555', label: c.status };
                        complaintHtml = `
                            <div class="glass" style="padding: 16px; border-radius: 12px; margin-top: 16px; border-left: 4px solid ${sc.color}; background: rgba(255,255,255,0.01);">
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                                    <div style="font-size:11px; font-weight:600; color:var(--text-muted); text-transform:uppercase;">Status Komplain</div>
                                    <span class="status-badge" style="background:${sc.bg}; color:${sc.color}; font-size:11px; padding:3px 8px; border-radius:6px; font-weight:600;">${sc.label}</span>
                                </div>
                                <p style="font-size: 14px; font-weight: 600; margin: 0 0 4px; color: var(--text-main);">${escapeHtml(c.judul)}</p>
                                <p class="text-muted" style="font-size: 13px; margin: 0 0 10px;">${escapeHtml(c.deskripsi)}</p>
                                ${c.respons_admin ? `
                                    <div style="margin-top:10px; padding:10px; background:rgba(255,255,255,0.03); border-radius:8px; border:1px solid var(--glass-border);">
                                        <div style="font-size:11px; font-weight:600; color:${sc.color}; margin-bottom:4px;">Tanggapan Admin:</div>
                                        <div style="font-size:13px; color: var(--text-main);">${escapeHtml(c.respons_admin)}</div>
                                    </div>
                                ` : ''}
                            </div>
                        `;
                    } else {
                        complaintHtml = `
                            <div style="margin-top:16px;">
                                <button type="button" class="btn btn-outline" style="width: 100%; padding: 12px; font-size: 13px; border-color: #D97706; color: #D97706; background: transparent;" 
                                    onclick="closeDetailModal(); openComplaintModal('pesanan', ${order.id_pesanan})">
                                    <i data-lucide="alert-circle" class="icon icon-sm" style="color: #D97706; margin-right: 6px;"></i> Laporkan Masalah / Komplain
                                </button>
                            </div>
                        `;
                    }
                }

                content.innerHTML = `
                    <div class="order-detail-header">
                        <div>
                            <div class="order-detail-id-lbl">ID Pesanan</div>
                            <div class="order-detail-id-val">#${order.id_pesanan}</div>
                        </div>
                        <span class="status-badge status-${order.status}">${order.status}</span>
                    </div>

                    <div class="glass order-detail-section">
                        <div class="order-detail-section-title">PRODUK</div>
                        ${productsHtml}
                    </div>

                    <div class="order-detail-grid">
                        <div class="glass order-detail-grid-item">
                            <div class="order-detail-grid-lbl">Tanggal</div>
                            <div class="order-detail-grid-val">${date}</div>
                        </div>
                        <div class="glass order-detail-grid-item">
                            <div class="order-detail-grid-lbl">Ekspedisi</div>
                            <div class="order-detail-grid-val">${order.ekspedisi?.nama_layanan || order.layanan_ekspedisi?.nama_layanan || '-'}</div>
                        </div>
                        <div class="glass order-detail-grid-item">
                            <div class="order-detail-grid-lbl">Pembayaran</div>
                            <div class="order-detail-grid-val">${order.metode_pembayaran || 'Belum dibayar'}</div>
                        </div>
                        <div class="glass order-detail-grid-item">
                            <div class="order-detail-grid-lbl">Alamat</div>
                            <div class="order-detail-grid-val">${order.alamat_pengiriman || '-'}</div>
                        </div>
                    </div>

                    ${order.bukti_pembayaran_url ? `
                    <div class="glass order-detail-section">
                        <div class="order-detail-grid-lbl" style="margin-bottom: 8px;">Bukti Pembayaran</div>
                        <img src="${order.bukti_pembayaran_url}" style="max-width: 100%; max-height: 200px; border-radius: var(--radius-sm);">
                    </div>
                    ` : ''}

                    <div class="order-detail-footer">
                        <span class="order-detail-total-lbl">Total Pembayaran</span>
                        <span class="order-detail-total-val">${formatRupiah(order.total_harga)}</span>
                    </div>

                    ${complaintHtml}

                    ${order.status === 'pending' ? `
                    <div class="glass payment-instructions-container">
                        <div class="payment-instructions-header">
                            <i data-lucide="alert-triangle" class="icon icon-sm text-warning"></i> INSTRUKSI PEMBAYARAN
                        </div>
                        <div class="payment-accounts-list">
                            <div class="payment-account-card">
                                <div>
                                    <div class="payment-account-name">SeaBank</div>
                                    <div class="payment-account-holder">a/n Defkan Computer</div>
                                </div>
                                <div class="payment-account-number">1109001025205</div>
                            </div>
                            <div class="payment-account-card">
                                <div>
                                    <div class="payment-account-name">DANA</div>
                                    <div class="payment-account-holder">a/n Defkan Computer</div>
                                </div>
                                <div class="payment-account-number">083897628556</div>
                            </div>
                            <div class="qris-container">
                                <div class="qris-title">QRIS</div>
                                <img src="/img/qris.jpg" alt="QRIS Code" class="qris-img">
                                <div class="qris-holder">a/n GUMELAR LAPTOP - ELECTRONICS</div>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary" style="width: 100%; margin-top: 16px; padding: 14px;" onclick="closeDetailModal(); openUploadModal(${order.id_pesanan})">
                        <i data-lucide="credit-card" class="icon"></i> Bayar & Upload Bukti Pembayaran
                    </button>
                    ` : ''}
                `;
                lucide.createIcons();
            } catch (err) {
                content.innerHTML = `<p style="text-align:center; color:var(--danger);">Gagal memuat detail: ${err.message}</p>`;
            }
        }

        function closeDetailModal() {
            document.getElementById('detail-modal').style.display = 'none';
        }

        function openUploadModal(targetTypeOrId, targetId = null) {
            let targetType = 'order';
            let resolvedId = targetTypeOrId;

            if (targetId !== null) {
                targetType = targetTypeOrId || 'order';
                resolvedId = targetId;
            }

            document.getElementById('upload-target-type').value = targetType;
            document.getElementById('upload-pesanan-id').value = targetType === 'order' ? resolvedId : '';
            document.getElementById('upload-servis-id').value = targetType === 'service' ? resolvedId : '';

            const titleText = document.getElementById('upload-modal-title-text');
            if (titleText) {
                titleText.textContent = targetType === 'service'
                    ? 'Upload Bukti Pembayaran Servis'
                    : 'Upload Bukti Pembayaran';
            }

            const uploadForm = document.getElementById('upload-form');
            uploadForm.reset();
            document.getElementById('upload-preview').style.display = 'none';
            document.getElementById('upload-modal').style.display = 'flex';
            lucide.createIcons();
        }

        function openServiceUploadModal(serviceId) {
            closeDetailModal();
            openUploadModal('service', serviceId);
        }

        function closeUploadModal() {
            document.getElementById('upload-modal').style.display = 'none';
        }

        async function handleUploadBukti(e) {
            e.preventDefault();
            const targetType = document.getElementById('upload-target-type').value || 'order';
            const pesananId = document.getElementById('upload-pesanan-id').value;
            const servisId = document.getElementById('upload-servis-id').value;
            const metode = document.getElementById('upload-metode').value;
            const file = document.getElementById('upload-file').files[0];
            const btn = document.getElementById('btn-upload');

            const targetId = targetType === 'service' ? servisId : pesananId;

            if (!targetId) {
                showToast('ID data tidak ditemukan. Silakan refresh halaman.', 'error');
                return;
            }

            if (!metode || !file) {
                showToast('Lengkapi semua data pembayaran', 'error');
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                showToast('Ukuran bukti pembayaran terlalu besar. Maksimal 5MB.', 'error');
                return;
            }

            const originalText = btn.innerHTML;
            btn.innerHTML = '<div class="loader" style="width:18px;height:18px;border-width:2px;"></div> Mengunggah...';
            btn.disabled = true;

            try {
                const formData = new FormData();
                formData.append('metode_pembayaran', metode);
                formData.append('bukti_pembayaran', file);

                const endpoint = targetType === 'service'
                    ? `/servis/${targetId}/bayar`
                    : `/pesanan/${targetId}/bayar`;

                await apiFetch(endpoint, {
                    method: 'POST',
                    body: formData
                });

                showToast(targetType === 'service'
                    ? 'Bukti pembayaran servis berhasil diunggah!'
                    : 'Bukti pembayaran berhasil diunggah!'
                );

                closeUploadModal();

                if (targetType === 'service') {
                    await loadServices();
                } else {
                    await loadOrders();
                }
            } catch (err) {
                showToast(err.message, 'error');
            } finally {
                btn.innerHTML = originalText || 'Kirim Bukti';
                btn.disabled = false;
                if (window.lucide) lucide.createIcons();
            }
        }

        let selectedRating = 0;

        function setRatingValue(value) {
            selectedRating = value;
            document.getElementById('review-rating-val').value = value;
            document.querySelectorAll('.star-btn').forEach(btn => {
                const val = parseInt(btn.getAttribute('data-value'));
                if (val <= value) {
                    btn.style.color = '#F59E0B'; // Gold color
                } else {
                    btn.style.color = 'var(--text-muted)';
                }
            });
        }

        function openReviewModal(tipe, pesananId, produkId, servisId, itemName) {
            console.log('[DEBUG] openReviewModal called', {tipe, pesananId, produkId, servisId, itemName});
            
            const reviewModal = document.getElementById('review-modal');
            if (!reviewModal) {
                alert('ERROR: Elemen #review-modal tidak ditemukan di halaman!');
                return;
            }
            
            document.getElementById('review-tipe').value = tipe;
            document.getElementById('review-pesanan-id').value = pesananId || '';
            document.getElementById('review-produk-id').value = produkId || '';
            document.getElementById('review-servis-id').value = servisId || '';
            
            if (!itemName) {
                if (tipe === 'produk') {
                    const order = allOrders.find(o => Number(o.id_pesanan) === Number(pesananId));
                    if (order) {
                        const details = order.detail || order.details || [];
                        const p = details.find(item => Number(item.id_produk) === Number(produkId))?.produk || {};
                        itemName = p.nama_produk || 'Produk';
                    } else {
                        itemName = 'Produk';
                    }
                } else if (tipe === 'servis') {
                    const service = allServices.find(s => Number(s.id) === Number(servisId));
                    itemName = 'Servis Laptop: ' + (service ? (service.merek_laptop || 'Laptop') : 'Laptop');
                }
            }
            document.getElementById('review-item-name').textContent = itemName;
            setRatingValue(0);
            document.getElementById('review-komentar').value = '';
            document.getElementById('review-foto').value = '';
            
            reviewModal.style.cssText = 'display:flex !important; position:fixed !important; inset:0 !important; z-index:99999 !important; background:rgba(15,23,42,0.85) !important; justify-content:center !important; align-items:center !important; backdrop-filter:blur(8px) !important;';
            console.log('[DEBUG] review-modal display set to flex:', reviewModal.style.display);
            if (window.lucide) lucide.createIcons();
        }

        function closeReviewModal() {
            const m = document.getElementById('review-modal');
            m.style.cssText = 'display:none;';
        }

        async function handleReviewSubmit(e) {
            e.preventDefault();
            const tipe = document.getElementById('review-tipe').value;
            const pesananId = document.getElementById('review-pesanan-id').value;
            const produkId = document.getElementById('review-produk-id').value;
            const servisId = document.getElementById('review-servis-id').value;
            const rating = document.getElementById('review-rating-val').value;
            const komentar = document.getElementById('review-komentar').value;
            const files = document.getElementById('review-foto').files;
            const btn = document.getElementById('btn-submit-review');

            if (!rating || rating === '0') {
                showToast('Pilih rating bintang terlebih dahulu', 'error');
                return;
            }

            const originalText = btn.innerHTML;
            btn.innerHTML = '<div class="loader" style="width:18px;height:18px;border-width:2px;"></div> Mengirim...';
            btn.disabled = true;

            try {
                const formData = new FormData();
                formData.append('tipe', tipe);
                formData.append('rating', rating);
                formData.append('komentar', komentar);
                if (pesananId) formData.append('id_pesanan', pesananId);
                if (produkId) formData.append('id_produk', produkId);
                if (servisId) formData.append('id_servis', servisId);
                
                for (let i = 0; i < files.length; i++) {
                    formData.append('foto_bukti[]', files[i]);
                }

                const res = await apiFetch('/ulasan', {
                    method: 'POST',
                    body: formData
                });

                showToast('Ulasan Anda berhasil dikirim!');
                closeReviewModal();
                
                // Refresh data
                if (tipe === 'servis') {
                    await loadServices();
                } else {
                    await loadOrders();
                }
            } catch (err) {
                showToast(err.message, 'error');
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        function openComplaintModal(tipe, refId, refTitle) {
            document.getElementById('complaint-tipe').value = tipe;
            document.getElementById('complaint-ref-id').value = refId;
            
            if (!refTitle) {
                if (tipe === 'pesanan') {
                    refTitle = 'Pesanan #' + refId;
                } else if (tipe === 'servis') {
                    const service = allServices.find(s => Number(s.id) === Number(refId));
                    refTitle = 'Servis ' + (service ? (service.kode_servis || '') : '');
                }
            }
            
            document.getElementById('complaint-ref-title').textContent = refTitle;
            document.getElementById('complaint-judul').value = '';
            document.getElementById('complaint-deskripsi').value = '';
            document.getElementById('complaint-foto').value = '';

            const complaintModal = document.getElementById('complaint-modal');
            complaintModal.style.cssText = 'display:flex !important; position:fixed !important; inset:0 !important; z-index:99999 !important; background:rgba(15,23,42,0.85) !important; justify-content:center !important; align-items:center !important; backdrop-filter:blur(8px) !important;';
            if (window.lucide) lucide.createIcons();
        }

        function closeComplaintModal() {
            const m = document.getElementById('complaint-modal');
            m.style.cssText = 'display:none;';
        }

        async function handleComplaintSubmit(e) {
            e.preventDefault();
            const tipe = document.getElementById('complaint-tipe').value;
            const refId = document.getElementById('complaint-ref-id').value;
            const judul = document.getElementById('complaint-judul').value;
            const deskripsi = document.getElementById('complaint-deskripsi').value;
            const files = document.getElementById('complaint-foto').files;
            const btn = document.getElementById('btn-submit-complaint');

            const originalText = btn.innerHTML;
            btn.innerHTML = '<div class="loader" style="width:18px;height:18px;border-width:2px;"></div> Mengirim...';
            btn.disabled = true;

            try {
                const formData = new FormData();
                formData.append('tipe', tipe);
                formData.append('judul', judul);
                formData.append('deskripsi', deskripsi);
                if (tipe === 'pesanan') {
                    formData.append('id_pesanan', refId);
                } else {
                    formData.append('id_servis', refId);
                }

                for (let i = 0; i < files.length; i++) {
                    formData.append('foto_bukti[]', files[i]);
                }

                const res = await apiFetch('/complaint', {
                    method: 'POST',
                    body: formData
                });

                showToast('Laporan komplain berhasil diajukan!');
                closeComplaintModal();

                // Refresh data
                if (tipe === 'servis') {
                    await loadServices();
                } else {
                    await loadOrders();
                }
            } catch (err) {
                showToast(err.message, 'error');
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        function escapeHtml(value) {
            const div = document.createElement('div');
            div.textContent = value == null ? '' : String(value);
            return div.innerHTML;
        }

        function escapeAttr(value) {
            return escapeHtml(value).replaceAll('`', '&#096;').replaceAll('"', '&quot;');
        }
    </script>
@endpush
