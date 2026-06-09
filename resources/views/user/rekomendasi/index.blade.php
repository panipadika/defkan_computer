@extends('layouts.app')

@section('title', 'Rekomendasi Laptop — Defkan Computer')

@section('content')
<div class="rec-page-compact">

    {{-- Compact Header --}}
    <div class="rec-header-compact">
        <h1 class="rec-title-compact"><span class="text-primary-gradient">Rekomendasi Laptop</span> Sesuai Kebutuhan</h1>
        <p class="text-muted rec-subtitle-compact">Temukan laptop yang sempurna untuk aktivitas Anda.</p>
    </div>

    {{-- Main Layout --}}
    <div id="rec-main-layout" class="rec-layout-compact">

        {{-- ====== LEFT PANEL: Kriteria ====== --}}
        <div class="glass-panel rec-left-panel">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
                <h2 style="font-size: 14px; font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 6px; margin: 0;"><i data-lucide="sliders-horizontal" style="width: 16px; height: 16px;"></i> Kriteria Anda</h2>
                <button onclick="resetKriteria()" id="btn-reset" title="Reset Semua" style="display: flex; align-items: center; gap: 4px; padding: 4px 8px; border-radius: 6px; border: 1.5px solid var(--glass-border); background: transparent; color: var(--text-muted); cursor: pointer; font-size: 10px; font-weight: 600; font-family: inherit; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--danger)'; this.style.color='var(--danger)'" onmouseout="this.style.borderColor='var(--glass-border)'; this.style.color='var(--text-muted)'">
                    <i data-lucide="rotate-ccw" style="width: 12px; height: 12px;"></i> Reset
                </button>
            </div>

            {{-- Tipe Penggunaan --}}
            <div class="rec-section">
                <label class="rec-label">Tipe Penggunaan Utama</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 6px;">
                    <button type="button" class="tipe-btn active" data-tipe="kerja" onclick="selectTipe(this, 'kerja')">
                        <i data-lucide="briefcase" style="width: 16px; height: 16px; display: block; margin: 0 auto 4px;"></i>
                        Kerja
                    </button>
                    <button type="button" class="tipe-btn" data-tipe="sekolah" onclick="selectTipe(this, 'sekolah')">
                        <i data-lucide="graduation-cap" style="width: 16px; height: 16px; display: block; margin: 0 auto 4px;"></i>
                        Kuliah
                    </button>
                    <button type="button" class="tipe-btn" data-tipe="gaming" onclick="selectTipe(this, 'gaming')">
                        <i data-lucide="gamepad-2" style="width: 16px; height: 16px; display: block; margin: 0 auto 4px;"></i>
                        Gaming
                    </button>
                </div>
            </div>

            <div class="rec-section">
                <label class="rec-label">Software Digunakan</label>
                <div id="software-checklist" class="rec-sw-checklist">
                    <div style="grid-column: 1/-1; text-align: center; padding: 12px;"><div class="loader" style="margin: 0 auto;"></div></div>
                </div>
            </div>

            {{-- Budget Range --}}
            <div class="rec-section">
                <label class="rec-label" style="display: flex; justify-content: space-between;">
                    <span>Budget</span>
                    <span id="budget-label" style="color: var(--primary); font-size: 11px;">Rp 5 Jt - Rp 15 Jt</span>
                </label>
                <input type="range" id="budget-min" min="2" max="30" step="1" value="5" oninput="updateBudgetDisplay()" style="width: 100%; accent-color: var(--primary); margin-bottom: 4px;">
                <input type="range" id="budget-max" min="2" max="30" step="1" value="15" oninput="updateBudgetDisplay()" style="width: 100%; accent-color: var(--primary);">
                <div style="display: flex; justify-content: space-between; font-size: 10px; color: var(--text-subtle); margin-top: 2px;">
                    <span>1 Jt</span><span>30+ Jt</span>
                </div>
            </div>

            {{-- Tombol Cari --}}
            <button id="btn-recommend" onclick="cariRekomendasi()" class="btn btn-primary rec-btn-search">
                <i data-lucide="search" style="width: 14px; height: 14px;"></i>
                Cari Rekomendasi
            </button>
        </div>

        {{-- ====== RIGHT PANEL: Hasil ====== --}}
        <div class="rec-right-panel">

            {{-- Spesifikasi Minimal Card (initially hidden) --}}
            <div id="spec-card" class="glass-panel rec-spec-card" style="display: none;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                    <h3 class="rec-spec-title" style="margin-bottom: 0;"><i data-lucide="cpu" style="width: 14px; height: 14px; color: var(--primary);"></i> Spesifikasi Minimal Rekomendasi</h3>
                </div>
                <div id="spec-grid" class="rec-spec-grid"></div>
            </div>

            {{-- Section Judul Hasil --}}
            <div id="result-title-row" style="margin-bottom: 12px; display: none; align-items: center; justify-content: space-between;">
                <h3 style="font-size: 14px; font-weight: 700; margin: 0;">Laptop Rekomendasi Untukmu</h3>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <i data-lucide="arrow-up-down" style="width: 13px; height: 13px; color: var(--text-muted);"></i>
                    <select id="sort-price" onchange="sortResults()" style="font-family: inherit; font-size: 11px; font-weight: 600; padding: 4px 8px; border-radius: 7px; border: 1.5px solid var(--glass-border); background: var(--bg-surface, #fff); color: var(--text-main); cursor: pointer; outline: none;">
                        <option value="relevance">Paling Relevan</option>
                        <option value="price_asc">Harga: Rendah ke Tinggi</option>
                        <option value="price_desc">Harga: Tinggi ke Rendah</option>
                    </select>
                </div>
            </div>

            {{-- Result Grid --}}
            <div id="result-grid" class="rec-result-grid"></div>

            {{-- Empty State --}}
            <div id="no-result" class="glass-panel rec-empty-state" style="display: none;">
                <i data-lucide="frown" style="width: 40px; height: 40px; stroke-width: 1.25; margin-bottom: 10px; color: var(--text-subtle);"></i>
                <h3 style="font-size: 14px; font-weight: 700; margin-bottom: 6px;">Tidak ada laptop yang cocok</h3>
                <p class="text-muted" style="font-size: 12px; margin-bottom: 14px;">Coba ubah kriteria atau budget Anda.</p>
                <a href="/produk" class="btn btn-outline" style="font-size: 12px;">Lihat Semua Produk</a>
            </div>

            {{-- Initial State (before search) --}}
            <div id="initial-state" class="glass-panel rec-empty-state">
                <i data-lucide="laptop" style="width: 44px; height: 44px; stroke-width: 1.25; margin-bottom: 10px; color: var(--text-subtle);"></i>
                <h3 style="font-size: 14px; font-weight: 700; margin-bottom: 6px;">Isi Kriteria Anda</h3>
                <p class="text-muted" style="font-size: 12px;">Pilih tipe penggunaan, software, dan budget, lalu klik <strong>"Cari"</strong> untuk mendapatkan rekomendasi.</p>
            </div>
        </div>
    </div>
</div>

{{-- Product Detail Modal --}}
<div id="detail-modal" class="modal-overlay" style="display: none;" onclick="closeDetailModal(event)">
    <div class="glass-panel modal-panel product-detail-modal-panel" onclick="event.stopPropagation()">
        <button onclick="closeDetailModal()" class="product-detail-close-btn"><i data-lucide="x" class="icon"></i></button>
        <div id="modal-content">
            <div class="flex-center" style="padding: 60px;"><div class="loader"></div></div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* ===== Page Layout ===== */
    .rec-page-compact {
        min-height: 80vh;
        display: flex;
        flex-direction: column;
        padding: 12px 20px 40px;
        max-width: 1280px;
        margin: 0 auto;
    }
    .rec-header-compact {
        text-align: center;
        padding: 8px 0 10px;
        flex-shrink: 0;
    }
    .rec-title-compact {
        font-size: clamp(22px, 3vw, 32px);
        font-weight: 800;
        margin: 0 0 4px;
        line-height: 1.1;
    }
    .rec-subtitle-compact {
        font-size: 12.5px;
        margin: 0;
        line-height: 1.4;
    }
    .rec-layout-compact {
        display: grid;
        grid-template-columns: 260px 1fr;
        gap: 16px;
        flex: 1;
    }
    .rec-left-panel {
        padding: 14px !important;
    }
    @media (min-width: 861px) {
        .rec-left-panel {
            position: sticky;
            top: 90px;
            height: fit-content;
        }
    }
    .rec-panel-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .rec-section { margin-bottom: 14px; }
    .rec-label {
        font-size: 11.5px;
        font-weight: 600;
        color: var(--text-main);
        display: block;
        margin-bottom: 8px;
    }
    .rec-sw-checklist {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2px;
        max-height: 120px;
        overflow-y: auto;
        padding: 6px;
        border: 1px solid var(--glass-border);
        border-radius: 8px;
        background: rgba(0,0,0,0.02);
        scrollbar-width: thin;
    }
    .rec-sw-checklist::-webkit-scrollbar { width: 3px; }
    .rec-sw-checklist::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }
    .rec-btn-search {
        width: 100%;
        padding: 10px;
        font-size: 12.5px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border-radius: 10px;
    }
    .rec-right-panel {
        padding-right: 4px;
    }
    .rec-spec-card {
        margin-bottom: 12px;
        padding: 12px !important;
    }
    .rec-spec-title {
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .rec-spec-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
        gap: 8px;
    }
    .rec-result-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
        gap: 14px;
    }
    .rec-empty-state {
        text-align: center;
        padding: 40px 24px;
    }

    /* ===== Tipe Buttons ===== */
    .tipe-btn {
        padding: 8px 4px;
        border-radius: 8px;
        border: 1.5px solid var(--glass-border);
        background: transparent;
        color: var(--text-muted);
        cursor: pointer;
        font-size: 10.5px;
        font-weight: 600;
        text-align: center;
        transition: all 0.2s;
        font-family: inherit;
        line-height: 1.3;
    }
    .tipe-btn.active {
        border-color: var(--primary);
        background: rgba(37,99,235,0.1);
        color: var(--primary);
    }
    .tipe-btn:hover:not(.active) {
        border-color: var(--text-muted);
        color: var(--text-main);
    }
    .sw-check-item {
        display: flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        padding: 3px 6px;
        border-radius: 5px;
        transition: background 0.15s;
        font-size: 11px;
        color: var(--text-main);
        user-select: none;
    }
    .sw-check-item:hover { background: rgba(37,99,235,0.06); }
    .sw-check-item input[type="checkbox"] {
        accent-color: var(--primary);
        width: 12px;
        height: 12px;
        cursor: pointer;
        flex-shrink: 0;
    }
    .spec-item {
        background: var(--bg-surface-alt);
        border-radius: 8px;
        padding: 8px 10px;
    }
    .spec-item-label {
        font-size: 9px;
        color: var(--text-subtle);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }
    .spec-item-value {
        font-size: 11.5px;
        font-weight: 700;
        color: var(--text-main);
    }

    /* ===== Product Cards (Catalog-matching) ===== */
    .rec-product-card {
        border-radius: 14px;
        border: 1px solid var(--glass-border);
        background: var(--bg-surface, #fff);
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        position: relative;
        display: flex;
        flex-direction: column;
        box-shadow: 0 8px 20px rgba(15,23,42,0.06);
    }
    .rec-product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(15,23,42,0.1);
        border-color: rgba(37,99,235,0.2);
    }
    .rec-card-img-wrap {
        position: relative;
        height: 150px;
        padding: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f8fafc 0%, #eef4ff 100%);
        overflow: hidden;
        cursor: pointer;
    }
    .rec-card-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
        transition: transform 0.25s ease;
    }
    .rec-product-card:hover .rec-card-img-wrap img { transform: scale(1.03); }
    .rec-card-badges {
        position: absolute;
        inset: 8px 8px auto 8px;
        display: flex;
        justify-content: space-between;
        gap: 6px;
        pointer-events: none;
    }
    .rec-badge-cat, .rec-badge-stok {
        height: 22px;
        display: inline-flex;
        align-items: center;
        padding: 0 8px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
        backdrop-filter: blur(8px);
    }
    .rec-badge-cat {
        background: rgba(255,255,255,.88);
        color: var(--text-muted);
        border: 1px solid rgba(226,232,240,.85);
    }
    .rec-badge-stok.ok {
        background: rgba(16,185,129,.14);
        color: #059669;
        border: 1px solid rgba(16,185,129,.25);
    }
    .rec-badge-stok.empty {
        background: rgba(239,68,68,.14);
        color: #dc2626;
        border: 1px solid rgba(239,68,68,.25);
    }
    .rec-product-body {
        padding: 10px 12px 12px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .rec-product-brand {
        color: var(--text-muted);
        font-size: 9.5px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: 800;
        margin-bottom: 2px;
    }
    .rec-product-name {
        font-size: 13px;
        font-weight: 800;
        margin-bottom: 6px;
        color: var(--text-main);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 34px;
        line-height: 1.3;
        cursor: pointer;
    }
    .rec-product-name:hover { color: var(--primary); }
    .rec-product-specs {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
        margin-bottom: 6px;
    }
    .rec-spec-chip {
        height: 20px;
        display: inline-flex;
        align-items: center;
        padding: 0 6px;
        border-radius: 6px;
        font-size: 9.5px;
        font-weight: 800;
    }
    .rec-chip-ram {
        background: rgba(14,165,233,0.1);
        border: 1px solid rgba(14,165,233,0.2);
        color: var(--primary);
    }
    .rec-chip-storage {
        background: rgba(3,105,161,0.1);
        border: 1px solid rgba(3,105,161,0.2);
        color: var(--accent);
    }
    .rec-chip-vga {
        background: rgba(124,58,237,0.1);
        border: 1px solid rgba(124,58,237,0.2);
        color: var(--secondary);
    }
    .rec-warranty-row {
        display: flex;
        align-items: center;
        gap: 4px;
        flex-wrap: wrap;
        margin-bottom: 8px;
    }
    .rec-warranty-chip {
        height: 20px;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 0 6px;
        border-radius: 6px;
        border: 1px solid rgba(16,185,129,0.22);
        background: rgba(16,185,129,0.10);
        color: #059669;
        font-size: 9.5px;
        font-weight: 800;
        white-space: nowrap;
    }
    .rec-warranty-chip.ready {
        border-color: rgba(37,99,235,0.18);
        background: rgba(37,99,235,0.08);
        color: var(--primary);
    }
    .rec-warranty-chip svg { width: 10px !important; height: 10px !important; }
    .rec-card-bottom { margin-top: auto; }
    .rec-price-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        margin-bottom: 8px;
    }
    .rec-card-price {
        font-size: 15px;
        font-weight: 900;
        color: #0369a1;
        letter-spacing: -0.02em;
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
    .rec-action-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1.35fr;
        gap: 5px;
    }
    .rec-action-row .btn {
        height: 30px;
        padding: 0 6px !important;
        font-size: 10.5px !important;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        font-weight: 800;
    }
    .rec-action-row .btn-primary { box-shadow: 0 6px 14px rgba(37,99,235,.18); }
    .rec-top-match-badge {
        position: absolute;
        top: 8px;
        left: 8px;
        background: #f59e0b;
        color: white;
        font-size: 9px;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 20px;
        z-index: 2;
    }

    /* ===== Detail Modal Styles (match catalog) ===== */
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
        width: 100% !important; height: 100% !important;
        object-fit: contain !important; object-position: center !important;
        background: transparent !important; padding: 0 !important;
    }
    .product-detail-slider-btn {
        position: absolute; top: 50%; transform: translateY(-50%);
        width: 36px; height: 36px; border-radius: 999px;
        border: 1px solid rgba(226,232,240,0.9);
        background: rgba(255,255,255,0.92);
        color: var(--text-main); display: inline-flex;
        align-items: center; justify-content: center;
        cursor: pointer; z-index: 3;
        box-shadow: 0 6px 16px rgba(15,23,42,0.14);
        transition: 0.2s ease;
    }
    .product-detail-slider-btn:hover {
        background: var(--primary); border-color: var(--primary);
        color: #fff; transform: translateY(-50%) scale(1.04);
    }
    .product-detail-slider-btn.prev { left: 12px; }
    .product-detail-slider-btn.next { right: 12px; }
    .product-detail-slider-btn.hidden { display: none !important; }
    .product-detail-photo-counter {
        position: absolute; right: 12px; bottom: 10px;
        min-width: 42px; height: 22px; padding: 0 8px;
        border-radius: 999px; background: rgba(15,23,42,0.72);
        color: #fff; font-size: 10px; font-weight: 800;
        display: inline-flex; align-items: center; justify-content: center;
        z-index: 3; backdrop-filter: blur(8px);
    }
    .product-gallery-thumbnails {
        display: flex; gap: 8px; overflow-x: auto;
        padding: 8px 2px 6px; scrollbar-width: thin;
    }
    .gallery-thumbnail-img {
        width: 60px !important; height: 60px !important;
        object-fit: contain !important; border-radius: 10px !important;
        border: 2px solid var(--glass-border) !important;
        background: #f8fafc !important; padding: 4px !important;
        cursor: pointer; transition: 0.2s ease; flex: 0 0 auto;
    }
    .gallery-thumbnail-img.active {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
    }
    .product-detail-warranty-box {
        display: flex; gap: 8px; flex-wrap: wrap; margin: 10px 0 12px;
    }
    .product-detail-warranty-box span {
        display: inline-flex; align-items: center; gap: 5px;
        height: 28px; padding: 0 10px; border-radius: 999px;
        background: rgba(16,185,129,0.10);
        border: 1px solid rgba(16,185,129,0.22);
        color: #059669; font-size: 11.5px; font-weight: 800;
    }
    .product-detail-warranty-box span:last-child {
        background: rgba(37,99,235,0.08);
        border-color: rgba(37,99,235,0.18);
        color: var(--primary);
    }

    /* ===== Responsive ===== */
    @media (max-width: 860px) {
        .rec-layout-compact { grid-template-columns: 1fr !important; }
        .rec-page-compact { height: auto; overflow: auto; padding-bottom: 20px; }
        .rec-right-panel { overflow: visible; }
    }
</style>
@endpush

@push('scripts')
<script>
let selectedSoftware = new Set();
let allSoftware = [];
let selectedTipes = new Set(['kerja']); // Initialize with 'kerja' since it's active by default in HTML
let lastFetchedProducts = []; // Store last fetch results for sort

const tipeToKategori = {
    'kerja': ['Kantoran', 'Developing', 'Editing', 'Desain', 'Rendering & 3D'],
    'sekolah': ['Kantoran', 'Developing', 'Desain', 'Editing'],
    'gaming': ['Gaming']
};

document.addEventListener('DOMContentLoaded', () => {
    loadSoftware();
    updateBudgetDisplay();
});

function selectTipe(btn, tipe) {
    if (btn.classList.contains('active')) {
        btn.classList.remove('active');
        selectedTipes.delete(tipe);
    } else {
        btn.classList.add('active');
        selectedTipes.add(tipe);
    }
    filterAndRenderSoftware();
}

function filterAndRenderSoftware() {
    if (selectedTipes.size === 0) {
        renderSoftwareChecklist(allSoftware);
        return;
    }
    let allowedCategories = new Set();
    selectedTipes.forEach(t => {
        const cats = tipeToKategori[t] || [];
        cats.forEach(c => allowedCategories.add(c));
    });
    const filtered = allSoftware.filter(sw => allowedCategories.has(sw.kategori));
    renderSoftwareChecklist(filtered);
}

function updateBudgetDisplay() {
    let min = parseInt(document.getElementById('budget-min').value);
    let max = parseInt(document.getElementById('budget-max').value);
    if (min > max) {
        // Swap jika min > max
        document.getElementById('budget-min').value = max;
        document.getElementById('budget-max').value = min;
        [min, max] = [max, min];
    }
    const label = `Rp ${min} Jt${min < 30 ? '' : '+'} - Rp ${max} Jt${max >= 30 ? '+' : ''}`;
    document.getElementById('budget-label').textContent = label;
}

async function loadSoftware() {
    try {
        const res = await apiFetch('/software');
        allSoftware = res.data || [];
        filterAndRenderSoftware();
    } catch (err) {
        document.getElementById('software-checklist').innerHTML =
            `<p style="color:var(--danger); font-size:13px;">Gagal memuat: ${err.message}</p>`;
    }
}

function renderSoftwareChecklist(list) {
    const container = document.getElementById('software-checklist');
    if (!list || list.length === 0) {
        container.innerHTML = '<p class="text-muted" style="font-size:13px; grid-column:1/-1;">Tidak ada software tersedia.</p>';
        return;
    }
    container.innerHTML = list.map(sw => {
        const checked = selectedSoftware.has(sw.id) ? 'checked' : '';
        const iconHtml = sw.icon
            ? `<img src="${sw.icon}" alt="" style="width:16px;height:16px;object-fit:contain;flex-shrink:0;border-radius:3px;" onerror="this.style.display='none'">`
            : `<i data-lucide="package" style="width:14px;height:14px;flex-shrink:0;color:var(--text-muted);"></i>`;
        return `
        <label class="sw-check-item">
            <input type="checkbox" value="${sw.id}" ${checked} onchange="toggleSoftware(${sw.id})">
            ${iconHtml}
            <span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${sw.nama}</span>
        </label>
    `;
    }).join('');
    lucide.createIcons();
}

function toggleSoftware(id) {
    if (selectedSoftware.has(id)) {
        selectedSoftware.delete(id);
    } else {
        selectedSoftware.add(id);
    }
}

async function cariRekomendasi() {
    const btn = document.getElementById('btn-recommend');
    const minBudget = parseInt(document.getElementById('budget-min').value) * 1000000;
    const maxBudgetVal = parseInt(document.getElementById('budget-max').value);
    const maxBudget = maxBudgetVal >= 30 ? null : maxBudgetVal * 1000000;

    btn.innerHTML = '<div class="loader" style="width:18px;height:18px;border-width:2px;border-top-color:white;"></div> Mencari...';
    btn.disabled = true;

    try {
        const body = {
            software_ids: Array.from(selectedSoftware),
            tipe: Array.from(selectedTipes),
            budget_min: minBudget,
            budget_max: maxBudget
        };

        const res = await apiFetch('/rekomendasi/laptop', {
            method: 'POST',
            body
        });

        const products = res.data || [];
        lastFetchedProducts = products; // save for sort
        document.getElementById('initial-state').style.display = 'none';
        renderSpecSummary(res.requirement);
        document.getElementById('sort-price').value = 'relevance'; // reset sort on each new search
        renderResults(products);

        document.getElementById('spec-card').scrollIntoView({ behavior: 'smooth', block: 'start' });

    } catch (err) {
        showToast('Gagal mendapatkan rekomendasi: ' + err.message, 'error');
    } finally {
        btn.innerHTML = '<i data-lucide="search" style="width:14px;height:14px;"></i> Cari Rekomendasi';
        btn.disabled = false;
        lucide.createIcons();
    }
}

function resetKriteria() {
    // Reset tipe penggunaan
    selectedTipes = new Set(['kerja']);
    document.querySelectorAll('.tipe-btn').forEach(btn => {
        const tipe = btn.getAttribute('data-tipe');
        if (tipe === 'kerja') {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });

    // Reset software
    selectedSoftware = new Set();
    filterAndRenderSoftware();

    // Reset budget
    document.getElementById('budget-min').value = 5;
    document.getElementById('budget-max').value = 15;
    updateBudgetDisplay();

    // Hide hasil rekomendasi
    lastFetchedProducts = [];
    document.getElementById('spec-card').style.display = 'none';
    document.getElementById('result-title-row').style.display = 'none';
    document.getElementById('result-grid').innerHTML = '';
    document.getElementById('no-result').style.display = 'none';
    document.getElementById('initial-state').style.display = 'block';
    lucide.createIcons();
}

function sortResults() {
    const sortVal = document.getElementById('sort-price').value;
    if (!lastFetchedProducts.length) return;

    let sorted = [...lastFetchedProducts];
    if (sortVal === 'price_asc') {
        sorted.sort((a, b) => a.harga - b.harga);
    } else if (sortVal === 'price_desc') {
        sorted.sort((a, b) => b.harga - a.harga);
    }
    // 'relevance' renders original backend order
    renderResults(sorted);
}

function renderSpecSummary(req) {
    const card = document.getElementById('spec-card');
    const grid = document.getElementById('spec-grid');
    
    // Fallback default jika tidak ada response dari backend
    req = req || { cpu_min: 1, vga_min: 1, ram_min: 4, storage_min: 256 };

    let cpuText = 'Intel Core i3 / Ryzen 3';
    if (req.cpu_min == 2) cpuText = 'Intel Core i5 / Ryzen 5';
    else if (req.cpu_min >= 3) cpuText = 'Intel Core i7 / Ryzen 7';

    let vgaText = 'Integrated Graphics';
    if (req.vga_min == 2) vgaText = 'Dedicated GTX / MX Series';
    else if (req.vga_min >= 3) vgaText = 'Dedicated RTX / RX Series';

    let ramText = `${req.ram_min} GB DDR4`;
    let storageText = req.storage_min >= 1000 ? `${req.storage_min / 1000} TB SSD` : `${req.storage_min} GB SSD`;

    grid.innerHTML = `
        <div class="spec-item">
            <div class="spec-item-label">Processor</div>
            <div class="spec-item-value">${cpuText}</div>
        </div>
        <div class="spec-item">
            <div class="spec-item-label">RAM</div>
            <div class="spec-item-value">${ramText}</div>
        </div>
        <div class="spec-item">
            <div class="spec-item-label">Storage</div>
            <div class="spec-item-value">${storageText}</div>
        </div>
        <div class="spec-item">
            <div class="spec-item-label">VGA</div>
            <div class="spec-item-value">${vgaText}</div>
        </div>
        <div class="spec-item">
            <div class="spec-item-label">Layar</div>
            <div class="spec-item-value">14" FHD (1920×1080)</div>
        </div>
    `;

    card.style.display = 'block';
    lucide.createIcons();
}

function escapeHtml(v) {
    return String(v ?? '').replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;');
}

function renderResults(products) {
    const grid = document.getElementById('result-grid');
    const noResult = document.getElementById('no-result');
    const titleRow = document.getElementById('result-title-row');
    grid.innerHTML = '';
    noResult.style.display = 'none';
    if (!products || products.length === 0) {
        titleRow.style.display = 'none';
        noResult.style.display = 'block';
        return;
    }
    titleRow.style.display = 'flex';
    products.forEach((produk, idx) => {
        const fotoUrl = produk.foto_url || `https://placehold.co/400x300/EFF8FF/0EA5E9?text=${encodeURIComponent(produk.merek || 'Laptop')}`;
        const isTopMatch = idx === 0 && produk.meets_critical;
        const stokBadge = produk.stok > 0
            ? `<span class="rec-badge-stok ok">Stok: ${produk.stok}</span>`
            : `<span class="rec-badge-stok empty">Habis</span>`;
        const kategoriBadge = produk.kategori ? `<span class="rec-badge-cat">${escapeHtml(produk.kategori)}</span>` : '<span></span>';
        const disabledAttr = Number(produk.stok || 0) === 0 ? 'disabled' : '';
        const card = document.createElement('div');
        card.className = 'rec-product-card';
        card.style.animationDelay = `${idx * 0.05}s`;
        card.innerHTML = `
            ${isTopMatch ? '<div class="rec-top-match-badge">Top Match</div>' : ''}
            <div class="rec-card-img-wrap" onclick="showDetailModal(${produk.id_produk})">
                <img src="${fotoUrl}" alt="${escapeHtml(produk.nama_produk)}" onerror="this.src='https://placehold.co/400x300/EFF8FF/0EA5E9?text=Laptop'">
                <div class="rec-card-badges">${kategoriBadge}${stokBadge}</div>
            </div>
            <div class="rec-product-body">
                <span class="rec-product-brand">${escapeHtml(produk.merek || '')}</span>
                <h3 class="rec-product-name" onclick="showDetailModal(${produk.id_produk})">${escapeHtml(produk.nama_produk)}</h3>
                <div class="rec-product-specs">
                    ${produk.ram ? `<span class="rec-spec-chip rec-chip-ram">RAM ${produk.ram}GB</span>` : ''}
                    ${produk.storage ? `<span class="rec-spec-chip rec-chip-storage">${produk.storage}GB</span>` : ''}
                    ${produk.vga ? `<span class="rec-spec-chip rec-chip-vga" title="${escapeHtml(produk.vga)}">${escapeHtml(produk.vga)}</span>` : ''}
                </div>
                <div class="rec-warranty-row">
                    <span class="rec-warranty-chip"><i data-lucide="shield-check" class="icon icon-xs"></i> Garansi 1 Bln</span>
                    <span class="rec-warranty-chip ready"><i data-lucide="check-circle" class="icon icon-xs"></i> Siap Pakai</span>
                </div>
                <div class="rec-card-bottom">
                    <div class="rec-price-row">
                        <div class="rec-card-price">${formatRupiah(produk.harga)}</div>
                        <div class="marketplace-mini" title="Tersedia juga di Marketplace">
                            <a href="${produk.link_shopee || '#'}" target="_blank" onclick="event.stopPropagation();" class="marketplace-btn" style="background: #ee4d2d;">
                                <svg fill="#ffffff" viewBox="0 0 24 24" width="15" height="15"><path d="M15.9414 17.9633c.229-1.879-.981-3.077-4.1758-4.0969-1.548-.528-2.277-1.22-2.26-2.1719.065-1.056 1.048-1.825 2.352-1.85a5.2898 5.2898 0 0 1 2.8838.89c.116.072.197.06.263-.039.09-.145.315-.494.39-.62.051-.081.061-.187-.068-.281-.185-.1369-.704-.4149-.983-.5319a6.4697 6.4697 0 0 0-2.5118-.514c-1.909.008-3.4129 1.215-3.5389 2.826-.082 1.1629.494 2.1078 1.73 2.8278.262.152 1.6799.716 2.2438.892 1.774.552 2.695 1.5419 2.478 2.6969-.197 1.047-1.299 1.7239-2.818 1.7439-1.2039-.046-2.2878-.537-3.1278-1.19l-.141-.11c-.104-.08-.218-.075-.287.03-.05.077-.376.547-.458.67-.077.108-.035.168.045.234.35.293.817.613 1.134.775a6.7097 6.7097 0 0 0 2.8289.727 4.9048 4.9048 0 0 0 2.0759-.354c1.095-.465 1.8029-1.394 1.9449-2.554zM11.9986 1.4009c-2.068 0-3.7539 1.95-3.8329 4.3899h7.6657c-.08-2.44-1.765-4.3899-3.8328-4.3899zm7.8516 22.5981-.08.001-15.7843-.002c-1.074-.04-1.863-.91-1.971-1.991l-.01-.195L1.298 6.2858a.459.459 0 0 1 .45-.494h4.9748C6.8448 2.568 9.1607 0 11.9996 0c2.8388 0 5.1537 2.5689 5.2757 5.7898h4.9678a.459.459 0 0 1 .458.483l-.773 15.5883-.007.131c-.094 1.094-.979 1.9769-2.0709 2.0059z"/></svg>
                            </a>
                            <a href="${produk.link_tokopedia || '#'}" target="_blank" onclick="event.stopPropagation();" class="marketplace-img-link">
                                <img src="/img/tokped.png" alt="Tokopedia" style="width: 28px; height: 28px; object-fit: contain;">
                            </a>
                        </div>
                    </div>
                    <div class="rec-action-row">
                        <button onclick="showDetailModal(${produk.id_produk})" class="btn btn-outline" title="Detail"><i data-lucide="eye" class="icon icon-sm"></i> Detail</button>
                        <button onclick="event.stopPropagation(); tambahKeKeranjang(${produk.id_produk}, this)" class="btn btn-outline" ${disabledAttr} title="Keranjang"><i data-lucide="shopping-cart" class="icon icon-sm"></i></button>
                        <button onclick="event.stopPropagation(); beliLangsung(${produk.id_produk}, this)" class="btn ${produk.stok > 0 ? 'btn-primary' : 'btn-outline'}" ${disabledAttr}>${produk.stok > 0 ? 'Beli' : 'Habis'}</button>
                    </div>
                </div>
            </div>`;
        grid.appendChild(card);
    });
    lucide.createIcons();
}

async function tambahKeKeranjang(id_produk, btn) {
    if (!isLoggedIn()) {
        window.location.href = '/login';
        return;
    }
    
    const origText = btn ? btn.innerHTML : '';
    if (btn) { btn.innerHTML = '<div class="loader" style="width:14px;height:14px;border-width:2px;margin:0 auto;"></div>'; btn.disabled = true; }
    
    try {
        await apiFetch('/keranjang', {
            method: 'POST',
            body: { id_produk, jumlah: 1 }
        });
        showToast('Berhasil ditambahkan ke keranjang!');
        if (typeof checkCartCount === 'function') checkCartCount();
    } catch (error) {
        showToast(error.message, 'error');
    } finally {
        if (btn) { btn.innerHTML = origText; btn.disabled = false; lucide.createIcons(); }
    }
}

async function beliLangsung(id_produk, btn) {
    if (!isLoggedIn()) {
        window.location.href = '/login';
        return;
    }
    
    const origText = btn ? btn.innerHTML : '';
    if (btn) { btn.innerHTML = '<div class="loader" style="width:14px;height:14px;border-width:2px;margin:0 auto;"></div>'; btn.disabled = true; }
    
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
        if (btn) { btn.innerHTML = origText; btn.disabled = false; lucide.createIcons(); }
    }
}

let detailGalleryPhotos = [];
let detailGalleryIndex = 0;

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
        const allPhotos = [fotoUrl, ...gallerySources].filter(Boolean).filter((p,i,a) => a.indexOf(p)===i);
        detailGalleryPhotos = allPhotos.length > 0 ? allPhotos : [fotoUrl];
        detailGalleryIndex = 0;
        const hasMulti = detailGalleryPhotos.length > 1;
        const galleryHtml = hasMulti ? `
            <div style="margin-top:12px;margin-bottom:8px;">
                <div class="product-gallery-thumbnails">
                    ${detailGalleryPhotos.map((p,i) => `<img src="${p}" class="gallery-thumbnail-img ${i===0?'active':''}" data-index="${i}" onclick="changeDetailImageByIndex(${i})" onerror="this.src='https://placehold.co/100x100/EFF8FF/0EA5E9?text=Laptop'">`).join('')}
                </div>
            </div>` : '';
        content.innerHTML = `
            <div class="product-detail-grid">
                <div>
                    <div class="product-detail-media-card">
                        <button type="button" class="product-detail-slider-btn prev ${hasMulti?'':'hidden'}" onclick="slideDetailImage(-1)"><i data-lucide="chevron-left" class="icon"></i></button>
                        <img id="main-detail-img" src="${detailGalleryPhotos[0]}" alt="${escapeHtml(produk.nama_produk)}" class="product-detail-img" onerror="this.src='https://placehold.co/600x400/EFF8FF/0EA5E9?text=Laptop'">
                        <button type="button" class="product-detail-slider-btn next ${hasMulti?'':'hidden'}" onclick="slideDetailImage(1)"><i data-lucide="chevron-right" class="icon"></i></button>
                        <span class="product-detail-photo-counter ${hasMulti?'':'hidden'}" id="detail-photo-counter">1/${detailGalleryPhotos.length}</span>
                    </div>
                    ${galleryHtml}
                    ${produk.kategori ? `<div style="margin-top:12px;"><span class="badge badge-primary">${escapeHtml(produk.kategori)}</span></div>` : ''}
                </div>
                <div>
                    <p class="product-detail-brand">${escapeHtml(produk.merek || '')}</p>
                    <h2 class="product-detail-title">${escapeHtml(produk.nama_produk)}</h2>
                    <div class="product-detail-price">${formatRupiah(produk.harga)}</div>
                    ${produk.stok > 0
                        ? `<p class="product-detail-stok-success"><i data-lucide="check-circle" class="icon icon-sm"></i> Stok tersedia: ${produk.stok} unit</p>`
                        : `<p class="product-detail-stok-danger"><i data-lucide="x-circle" class="icon icon-sm"></i> Stok habis</p>`}
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
                    <div style="display:flex;gap:12px;margin-top:20px;">
                        <button onclick="tambahKeKeranjang(${produk.id_produk}, this); closeDetailModal()" class="btn btn-outline" style="flex:1;padding:14px;" ${produk.stok===0?'disabled':''}><i data-lucide="shopping-cart" class="icon"></i></button>
                        <button onclick="beliLangsung(${produk.id_produk}, this); closeDetailModal()" class="btn ${produk.stok>0?'btn-primary':'btn-outline'}" style="flex:2;padding:14px;" ${produk.stok===0?'disabled':''}>Beli Sekarang</button>
                    </div>
                </div>
            </div>
            ${produk.deskripsi ? `
            <div style="background:var(--bg-surface-alt);border:1px solid var(--glass-border);border-radius:var(--radius-sm);padding:18px;margin-top:24px;">
                <h4 style="font-size:14px;font-weight:700;margin-bottom:10px;color:var(--text-main);display:flex;align-items:center;gap:6px;"><i data-lucide="file-text" class="icon icon-sm text-primary"></i> Detail Produk</h4>
                <p style="font-size:13px;color:var(--text-muted);line-height:1.75;white-space:pre-line;">${escapeHtml(produk.deskripsi)}</p>
            </div>` : ''}`;
        lucide.createIcons();
    } catch (err) {
        content.innerHTML = `<p style="text-align:center;color:var(--danger);padding:40px;">Gagal memuat detail: ${err.message}</p>`;
    }
}

function closeDetailModal(e) {
    if (!e || e === true || e.target === document.getElementById('detail-modal') || (e.target && e.target.closest && e.target.closest('.product-detail-close-btn'))) {
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

function slideDetailImage(dir) { updateDetailImage(detailGalleryIndex + dir); }
function changeDetailImageByIndex(i) { updateDetailImage(i); }
</script>
@endpush
