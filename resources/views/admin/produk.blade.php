@extends('layouts.admin')

@section('title', 'Admin — Kelola Produk | Defkan Computer')
@section('admin-title', 'Kelola Produk')
@section('tab-produk', 'active')

@section('content')
    <div class="produk-layout">
        {{-- Stats Cards --}}
        <div class="stats-grid">
            <div class="stat-card clickable" onclick="setStokFilter('')">
                <div class="stat-icon-wrapper" style="background: rgba(37, 99, 235, 0.1); color: var(--primary);">
                    <i data-lucide="package" class="stat-icon"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Total Produk</span>
                    <h3 class="stat-value" id="stat-total">0</h3>
                    <span class="stat-subtext">Semua produk terdaftar</span>
                </div>
            </div>
            <div class="stat-card clickable" onclick="setStokFilter('tersedia')">
                <div class="stat-icon-wrapper" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">
                    <i data-lucide="shopping-bag" class="stat-icon"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Stok Tersedia</span>
                    <h3 class="stat-value" id="stat-tersedia">0</h3>
                    <span class="stat-subtext">Produk dengan stok > 0</span>
                </div>
            </div>
            <div class="stat-card clickable" onclick="setStokFilter('habis')">
                <div class="stat-icon-wrapper" style="background: rgba(239, 68, 68, 0.1); color: var(--danger);">
                    <i data-lucide="package-x" class="stat-icon"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Stok Habis</span>
                    <h3 class="stat-value" id="stat-habis">0</h3>
                    <span class="stat-subtext">Produk dengan stok 0</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-wrapper" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                    <i data-lucide="grid" class="stat-icon"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Kategori</span>
                    <h3 class="stat-value" id="stat-kategori">0</h3>
                    <span class="stat-subtext">Total kategori produk</span>
                </div>
            </div>
        </div>

        <div class="glass-panel table-section">
            {{-- Header Row --}}
            <div class="table-header">
                <h2 class="section-title">Daftar Produk</h2>

                <div class="table-filters">
                    <div class="search-input-wrapper">
                        <i data-lucide="search" class="search-icon"></i>
                        <input type="text" id="filter-produk" class="filter-input compact-search"
                            placeholder="Cari produk, merek, atau spesifikasi..." oninput="filterProduk(this.value)">
                    </div>

                    <select class="filter-input filter-select" id="filter-kategori" onchange="applyFilters()">
                        <option value="">Semua Kategori</option>
                        <option value="Laptop Pelajar / Mahasiswa">Laptop Pelajar / Mahasiswa</option>
                        <option value="Laptop Kantor / Kerja">Laptop Kantor / Kerja</option>
                        <option value="Laptop Gaming">Laptop Gaming</option>
                        <option value="Laptop Desain & Editing">Laptop Desain & Editing</option>
                        <option value="Laptop Programming">Laptop Programming</option>
                        <option value="Laptop Tipis & Ringan">Laptop Tipis & Ringan</option>
                        <option value="Laptop Budget / Hemat">Laptop Budget / Hemat</option>
                    </select>

                    <select class="filter-input filter-select" id="filter-stok" onchange="applyFilters()">
                        <option value="">Semua Stok</option>
                        <option value="tersedia">Tersedia</option>
                        <option value="habis">Habis</option>
                    </select>

                    <select class="filter-input filter-select" id="filter-sort" onchange="applyFilters()">
                        <option value="terbaru">Terbaru</option>
                        <option value="terlama">Terlama</option>
                        <option value="harga_tertinggi">Harga Tertinggi</option>
                        <option value="harga_terendah">Harga Terendah</option>
                    </select>

                    <button class="btn-export-mini" type="button" onclick="exportToExcel()">
                        <i data-lucide="download" class="icon-sm"></i>
                        Export
                    </button>
                </div>
            </div>

            {{-- Action Bar --}}
            <div class="action-bar">
                <a href="/admin/produk/tambah" class="btn-add-produk">
                    <i data-lucide="plus" class="icon-xs"></i> Tambah Produk Baru
                </a>
            </div>

            {{-- Table --}}
            <div id="produk-table-wrapper" class="table-scroll-area">
                <div class="flex-center" style="padding: 60px;">
                    <div class="loader"></div>
                </div>
            </div>

            {{-- Pagination --}}
            <div id="pagination-wrapper" class="pagination-container"></div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .produk-layout {
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        .table-section {
            display: flex;
            flex-direction: column;
            padding: 16px 20px 14px;
            border-radius: var(--radius-md);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 16px;
            flex-shrink: 0;
        }

        .stat-card {
            background: var(--bg-surface);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            transition: all 0.2s ease;
        }

        .stat-card.clickable {
            cursor: pointer;
        }

        .stat-card.clickable:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px -2px rgba(0, 0, 0, 0.05);
            border-color: var(--primary);
        }

        .stat-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon {
            width: 24px;
            height: 24px;
        }

        .stat-info {
            display: flex;
            flex-direction: column;
        }

        .stat-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-main);
            margin: 0 0 2px 0;
            line-height: 1;
        }

        .stat-subtext {
            font-size: 11px;
            color: var(--text-muted);
        }

        /* Header */
        .table-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 14px;
            flex-shrink: 0;
        }

        .section-title {
            font-size: 15px;
            font-weight: 800;
            color: var(--text-main);
            margin: 0;
            white-space: nowrap;
        }

        .table-filters {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
        }

        .filter-input {
            background: var(--bg-surface);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-sm);
            padding: 6px 10px;
            color: var(--text-main);
            font-size: 12px;
            font-family: inherit;
            outline: none;
            transition: var(--transition);
            height: 32px;
        }

        .filter-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.08);
        }

        .search-input-wrapper {
            position: relative;
            flex: 1;
            max-width: 280px;
        }

        .search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 14px;
            height: 14px;
            color: var(--text-muted);
            pointer-events: none;
        }

        .compact-search {
            padding-left: 32px !important;
            width: 100%;
        }

        select.filter-input {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 8px center;
            background-size: 12px;
            padding-right: 26px;
            cursor: pointer;
        }

        .filter-select {
            width: auto;
            min-width: 110px;
        }

        .btn-export-mini {
            height: 32px;
            padding: 0 40px;
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

        /* Action Bar */
        .action-bar {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            flex-shrink: 0;
        }

        .btn-add-produk {
            background: var(--primary);
            color: white;
            font-size: 12px;
            font-weight: 600;
            padding: 0 16px;
            height: 32px;
            border: none;
            border-radius: var(--radius-sm);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
        }

        .btn-add-produk:hover {
            background: #1d4ed8;
        }

        /* Table Scroll */
        .table-scroll-area {
            width: 100%;
            overflow-x: auto;
        }

        /* Table */
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
        }

        .custom-table thead {
            position: sticky;
            top: 0;
            z-index: 2;
            background: var(--bg-surface);
        }

        .custom-table th {
            text-align: left;
            padding: 10px 14px;
            color: var(--text-subtle);
            font-weight: 700;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--glass-border);
        }

        .custom-table td {
            padding: 10px 14px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
            vertical-align: middle;
        }

        .custom-table tbody tr {
            transition: background 0.15s;
        }

        .custom-table tbody tr:hover td {
            background: rgba(37, 99, 235, 0.02);
        }

        /* Product Cell */
        .product-info-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .product-thumb {
            width: 42px;
            height: 42px;
            border-radius: 6px;
            object-fit: cover;
            border: 1px solid var(--glass-border);
            background: #f8fafc;
            flex-shrink: 0;
        }

        .product-details {
            display: flex;
            flex-direction: column;
        }

        .product-name {
            font-weight: 700;
            font-size: 13px;
            color: var(--text-main);
            line-height: 1.3;
        }

        .product-meta {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        /* Specs */
        .specs-text {
            color: var(--text-muted);
            font-size: 11.5px;
            line-height: 1.5;
        }

        /* Price */
        .price-text {
            font-weight: 700;
            color: var(--primary);
            font-size: 13px;
            white-space: nowrap;
        }

        /* Stock */
        .stok-number {
            font-weight: 700;
            font-size: 13px;
        }

        .text-success-stok {
            color: #10b981;
        }

        .text-danger-stok {
            color: #ef4444;
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            border: 1px solid;
        }

        .status-tersedia {
            background: rgba(16, 185, 129, 0.08);
            color: #10b981;
            border-color: rgba(16, 185, 129, 0.2);
        }

        .status-habis {
            background: rgba(239, 68, 68, 0.08);
            color: #ef4444;
            border-color: rgba(239, 68, 68, 0.2);
        }

        /* Action Buttons */
        .actions-cell {
            display: flex;
            gap: 6px;
            white-space: nowrap;
        }

        .btn-action-outline {
            background: white;
            border: 1px solid;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
            transition: var(--transition);
            height: 28px;
        }

        .btn-detail {
            color: var(--primary);
            border-color: rgba(37, 99, 235, 0.2);
        }

        .btn-detail:hover {
            background: rgba(37, 99, 235, 0.06);
        }

        .btn-edit {
            color: #6366f1;
            border-color: rgba(99, 102, 241, 0.2);
        }

        .btn-edit:hover {
            background: rgba(99, 102, 241, 0.06);
        }

        .btn-hapus {
            color: #ef4444;
            border-color: rgba(239, 68, 68, 0.2);
        }

        .btn-hapus:hover {
            background: rgba(239, 68, 68, 0.06);
        }

        /* Pagination */
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 12px;
            border-top: 1px solid var(--glass-border);
            flex-shrink: 0;
        }

        .pagination-info {
            font-size: 12px;
            color: var(--text-muted);
        }

        .pagination-controls {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .pag-btn {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: 1px solid var(--glass-border);
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            transition: var(--transition);
        }

        .pag-btn:hover:not(.disabled) {
            border-color: var(--primary);
            color: var(--primary);
        }

        .pag-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .pag-btn.disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .pag-dots {
            color: var(--text-muted);
            padding: 0 2px;
            font-size: 12px;
        }

        .per-page-select {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--text-muted);
        }

        .per-page-select select {
            padding: 4px 22px 4px 8px;
            height: 28px;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .table-header {
                flex-wrap: wrap;
            }

            .table-filters {
                flex-wrap: wrap;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        let allProducts = [];
        let currentPage = 1;
        let perPage = 3;
        let filterCat = '';
        let filterStock = '';
        let filterQuery = '';
        let filterSort = 'terbaru';

        document.addEventListener('DOMContentLoaded', () => {
            if (!isLoggedIn() || !isAdmin()) { window.location.href = '/'; return; }
            loadProducts();
        });

        async function exportToExcel() {
            try {
                showToast('Mempersiapkan file Export...', 'info');
                const token = localStorage.getItem('auth_token');
                const response = await fetch('/api/produk/export', {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'text/csv'
                    }
                });

                if (!response.ok) {
                    const errData = await response.json().catch(() => ({}));
                    throw new Error(errData.message || 'Gagal mengunduh file');
                }

                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'Daftar_Produk_Defkan.csv';
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
                showToast('Berhasil mengunduh file Export!');
            } catch (err) {
                showToast(err.message, 'error');
            }
        }

        async function loadProducts(page = 1) {
            currentPage = page;
            perPage = document.getElementById('per-page-dropdown')?.value || 3;

            let qs = `?page=${page}&per_page=${perPage}`;
            if (filterSort) qs += `&sort=${filterSort}`;
            if (filterCat) qs += `&kategori=${encodeURIComponent(filterCat)}`;
            if (filterQuery) qs += `&search=${encodeURIComponent(filterQuery)}`;
            if (filterStock) qs += `&stok_status=${filterStock}`;

            try {
                const res = await apiFetch(`/produk${qs}`);
                const paginationData = res.data;
                allProducts = paginationData?.data || [];

                // Update Stats
                if (res.stats) {
                    document.getElementById('stat-total').innerText = res.stats.total || 0;
                    document.getElementById('stat-tersedia').innerText = res.stats.tersedia || 0;
                    document.getElementById('stat-habis').innerText = res.stats.habis || 0;
                    document.getElementById('stat-kategori').innerText = res.stats.kategori || 0;
                }

                renderTable(allProducts);
                renderPagination(paginationData);
                if (typeof loadSidebarCounts === 'function') {
                    loadSidebarCounts();
                }
            } catch (e) {
                document.getElementById('produk-table-wrapper').innerHTML = `<p class="text-muted" style="text-align:center; padding: 40px;">Gagal memuat: ${e.message}</p>`;
            }
        }

        function applyFilters() {
            filterCat = document.getElementById('filter-kategori').value;
            filterStock = document.getElementById('filter-stok').value;
            filterSort = document.getElementById('filter-sort').value;
            loadProducts(1);
        }

        function setStokFilter(val) {
            document.getElementById('filter-stok').value = val;
            applyFilters();
        }

        function filterProduk(query) {
            filterQuery = query;
            loadProducts(1);
        }

        function renderTable(products) {
            const wrapper = document.getElementById('produk-table-wrapper');
            if (products.length === 0) {
                wrapper.innerHTML = '<p class="text-muted" style="text-align:center; padding: 40px;">Tidak ada produk ditemukan.</p>';
                return;
            }

            wrapper.innerHTML = `
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>PRODUK</th>
                                <th>SPESIFIKASI</th>
                                <th>HARGA</th>
                                <th style="text-align:center;">STOK</th>
                                <th style="text-align:center;">STATUS</th>
                                <th style="text-align:center;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${products.map(p => {
                const fotoSrc = p.foto ? (p.foto.startsWith('http') ? p.foto : '/storage/' + p.foto) : 'https://via.placeholder.com/60?text=Laptop';

                let specs = [];
                if (p.cpu) specs.push(p.cpu);
                if (p.ram) specs.push(p.ram + 'GB RAM');
                if (p.storage) specs.push(p.storage + 'GB SSD');
                let specsLine1 = specs.join(' • ');
                let specsLine2 = p.vga || '';

                return `
                                    <tr>
                                        <td>
                                            <div class="product-info-cell">
                                                <img src="${fotoSrc}" class="product-thumb" alt="${p.nama_produk}">
                                                <div class="product-details">
                                                    <span class="product-name">${p.nama_produk}</span>
                                                    <span class="product-meta">${p.merek || '-'} • ${p.kategori || '-'}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="specs-text">
                                                ${specsLine1}${specsLine2 ? '<br>' + specsLine2 : ''}
                                            </div>
                                        </td>
                                        <td class="price-text">${formatRupiah(p.harga)}</td>
                                        <td style="text-align:center;">
                                            <span class="stok-number ${p.stok > 0 ? 'text-success-stok' : 'text-danger-stok'}">${p.stok}</span>
                                        </td>
                                        <td style="text-align:center;">
                                            ${p.stok > 0
                        ? '<span class="status-badge status-tersedia">Tersedia</span>'
                        : '<span class="status-badge status-habis">Habis</span>'
                    }
                                        </td>
                                        <td style="text-align:center;">
                                            <div class="actions-cell" style="justify-content:center;">
                                                <button onclick="detailProduk(${p.id_produk})" class="btn-action-outline btn-detail"><i data-lucide="eye" class="icon-xs"></i> Detail</button>
                                                <button onclick="editProduk(${p.id_produk})" class="btn-action-outline btn-edit"><i data-lucide="edit-2" class="icon-xs"></i> Edit</button>
                                                <button onclick="hapusProduk(${p.id_produk}, this)" class="btn-action-outline btn-hapus"><i data-lucide="trash-2" class="icon-xs"></i> Hapus</button>
                                            </div>
                                        </td>
                                    </tr>
                                `;
            }).join('')}
                        </tbody>
                    </table>
                `;
            lucide.createIcons();
        }

        function renderPagination(data) {
            const wrapper = document.getElementById('pagination-wrapper');
            if (!data || !data.total) {
                wrapper.innerHTML = '';
                return;
            }

            let buttons = '';

            buttons += `
                    <button class="pag-btn ${data.current_page <= 1 ? 'disabled' : ''}" onclick="${data.current_page <= 1 ? '' : `loadProducts(${data.current_page - 1})`}">
                        <i data-lucide="chevron-left" class="icon-xs"></i>
                    </button>
                `;

            if (data.last_page) {
                for (let i = 1; i <= data.last_page; i++) {
                    if (i === 1 || i === data.last_page || (i >= data.current_page - 2 && i <= data.current_page + 2)) {
                        buttons += `
                                <button class="pag-btn ${data.current_page === i ? 'active' : ''}" onclick="loadProducts(${i})">
                                    ${i}
                                </button>
                            `;
                    } else if (i === 2 || i === data.last_page - 1) {
                        buttons += `<span class="pag-dots">...</span>`;
                    }
                }
            }

            buttons += `
                    <button class="pag-btn ${data.current_page >= data.last_page ? 'disabled' : ''}" onclick="${data.current_page >= data.last_page ? '' : `loadProducts(${data.current_page + 1})`}">
                        <i data-lucide="chevron-right" class="icon-xs"></i>
                    </button>
                `;

            let start = (data.current_page - 1) * perPage + 1;
            let end = Math.min(data.current_page * perPage, data.total);
            if (data.total === 0) { start = 0; end = 0; }

            wrapper.innerHTML = `
                    <div class="pagination-info">Menampilkan ${start} - ${end} dari ${data.total} produk</div>
                    <div class="pagination-controls">
                        ${buttons}
                    </div>
                    <div class="per-page-select">
                        Tampilkan
                        <select class="filter-input" id="per-page-dropdown" onchange="loadProducts(1)">
                            <option value="3" ${perPage == 3 ? 'selected' : ''}>3</option>
                            <option value="10" ${perPage == 10 ? 'selected' : ''}>10</option>
                            <option value="20" ${perPage == 20 ? 'selected' : ''}>20</option>
                            <option value="50" ${perPage == 50 ? 'selected' : ''}>50</option>
                            <option value="100" ${perPage == 100 ? 'selected' : ''}>100</option>
                        </select>
                        per halaman
                    </div>
                `;
            lucide.createIcons();
        }

        function editProduk(id) {
            window.location.href = `/admin/produk/edit/${id}`;
        }

        function detailProduk(id) {
            editProduk(id);
        }

        async function hapusProduk(id, btn) {
            if (!confirm('Hapus produk ini? Tindakan ini tidak dapat dibatalkan.')) return;
            const orig = btn.innerHTML; btn.disabled = true;
            try {
                await apiFetch(`/produk/${id}`, { method: 'DELETE' });
                showToast('Produk dihapus!');
                loadProducts(currentPage);
            } catch (e) {
                showToast(e.message, 'error');
                btn.innerHTML = orig; btn.disabled = false;
            }
        }
    </script>
@endpush