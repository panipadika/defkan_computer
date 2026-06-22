@extends('layouts.admin')

@section('title', 'Kelola Ulasan — Admin Defkan Computer')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Ulasan Pelanggan</h1>
        <p class="admin-page-subtitle">Pantau dan moderasi ulasan yang diberikan pelanggan.</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid" id="ulasan-stats" style="grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 16px; margin-bottom: 28px;">
    <div class="stat-card">
        <div class="stat-label">Total Ulasan</div>
        <div class="stat-value" id="stat-total">—</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Ditampilkan</div>
        <div class="stat-value" id="stat-visible" style="color: var(--success);">—</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Disembunyikan</div>
        <div class="stat-value" id="stat-hidden" style="color: var(--danger);">—</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Rata-rata Rating</div>
        <div class="stat-value" id="stat-rating" style="color: #F59E0B;">— ⭐</div>
    </div>
</div>

<!-- Filter Bar -->
<div class="filter-bar glass-panel" style="display:flex; gap:12px; flex-wrap:wrap; align-items:center; margin-bottom:20px; padding:16px 20px;">
    <input type="text" id="search-ulasan" placeholder="Cari komentar, nama, produk…" class="form-input" style="flex:1; min-width:200px; max-width:320px;">
    <select id="filter-tipe" class="form-input" style="width:140px;">
        <option value="">Semua Tipe</option>
        <option value="produk">Produk</option>
        <option value="servis">Servis</option>
    </select>
    <select id="filter-rating" class="form-input" style="width:140px;">
        <option value="">Semua Rating</option>
        <option value="5">⭐ 5 Bintang</option>
        <option value="4">⭐ 4 Bintang</option>
        <option value="3">⭐ 3 Bintang</option>
        <option value="2">⭐ 2 Bintang</option>
        <option value="1">⭐ 1 Bintang</option>
    </select>
    <select id="filter-visibility" class="form-input" style="width:160px;">
        <option value="">Semua Visibilitas</option>
        <option value="1">Ditampilkan</option>
        <option value="0">Disembunyikan</option>
    </select>
    <button onclick="loadUlasan()" class="btn btn-primary">Cari</button>
</div>

<!-- Tabel Ulasan -->
<div class="glass-panel" style="overflow-x:auto;">
    <table class="admin-table" style="width:100%; border-collapse:collapse;">
        <thead>
            <tr>
                <th style="width:50px;">#</th>
                <th>Pengguna</th>
                <th style="width:80px;">Tipe</th>
                <th>Produk / Servis</th>
                <th style="width:110px;">Rating</th>
                <th>Komentar</th>
                <th style="width:100px;">Tanggal</th>
                <th style="width:100px;">Status</th>
                <th style="width:130px;">Aksi</th>
            </tr>
        </thead>
        <tbody id="ulasan-tbody">
            <tr><td colspan="9" style="text-align:center; padding:40px; color:var(--text-muted);">Memuat data…</td></tr>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div id="ulasan-pagination" style="margin-top:16px; display:flex; justify-content:center; gap:8px;"></div>

<!-- Modal Konfirmasi Hapus -->
<div id="modal-hapus" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div class="glass-panel" style="max-width:400px; width:90%; padding:28px; text-align:center;">
        <i data-lucide="trash-2" style="width:48px; height:48px; color:var(--danger); margin-bottom:12px;"></i>
        <h3 style="margin-bottom:8px;">Hapus Ulasan?</h3>
        <p class="text-muted" style="margin-bottom:24px; font-size:14px;">Ulasan yang dihapus tidak dapat dikembalikan.</p>
        <div style="display:flex; gap:12px; justify-content:center;">
            <button onclick="document.getElementById('modal-hapus').style.display='none'" class="btn btn-outline">Batal</button>
            <button id="btn-konfirmasi-hapus" class="btn btn-danger" style="background:var(--danger); color:#fff; border:none; padding:10px 24px; border-radius:8px; cursor:pointer;">Hapus</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentPage = 1;
    let ulasanIdToDelete = null;

    document.addEventListener('DOMContentLoaded', () => {
        loadUlasan();

        // Enter key di search
        document.getElementById('search-ulasan').addEventListener('keydown', e => {
            if (e.key === 'Enter') loadUlasan();
        });
    });

    async function loadUlasan(page = 1) {
        currentPage = page;
        const search     = document.getElementById('search-ulasan').value;
        const tipe       = document.getElementById('filter-tipe').value;
        const rating     = document.getElementById('filter-rating').value;
        const visibility = document.getElementById('filter-visibility').value;

        const params = new URLSearchParams({ page, per_page: 15 });
        if (search)     params.append('search', search);
        if (tipe)       params.append('tipe', tipe);
        if (rating)     params.append('rating', rating);
        if (visibility !== '') params.append('is_visible', visibility);

        const tbody = document.getElementById('ulasan-tbody');
        tbody.innerHTML = '<tr><td colspan="9" style="text-align:center; padding:40px;"><div class="loader loader-centered"></div></td></tr>';

        try {
            const res = await apiFetch(`/admin/ulasan?${params}`);

            // Update stats
            if (res.stats) {
                document.getElementById('stat-total').textContent   = res.stats.total;
                document.getElementById('stat-visible').textContent = res.stats.visible;
                document.getElementById('stat-hidden').textContent  = res.stats.hidden;
                document.getElementById('stat-rating').textContent  = (res.stats.rata_rata || 0) + ' ⭐';
            }

            const items = res.data?.data || [];
            if (items.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" style="text-align:center; padding:40px; color:var(--text-muted);">Tidak ada ulasan ditemukan.</td></tr>';
                renderPagination(res.data, loadUlasan);
                return;
            }

            tbody.innerHTML = items.map(u => {
                const stars = Array.from({length: 5}, (_, i) =>
                    `<span style="color:${i < u.rating ? '#F59E0B' : '#D1D5DB'}; font-size:14px;">★</span>`
                ).join('');

                const nama    = u.pengguna?.nama || '—';
                const email   = u.pengguna?.email || '';
                const subjek  = u.tipe === 'produk'
                    ? (u.produk?.nama_produk || `Produk #${u.id_produk}`)
                    : (u.servis?.jenis_kerusakan || `Servis #${u.id_servis}`);
                const kom     = u.komentar ? u.komentar.substring(0, 80) + (u.komentar.length > 80 ? '…' : '') : '<em style="color:var(--text-muted)">—</em>';
                const tgl     = new Date(u.created_at).toLocaleDateString('id-ID', {day:'numeric', month:'short', year:'numeric'});
                const visibleBadge = u.is_visible
                    ? '<span style="background:rgba(34,197,94,0.1);color:#15803D;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600;">Tampil</span>'
                    : '<span style="background:rgba(239,68,68,0.1);color:#DC2626;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600;">Tersembunyi</span>';
                const tipeBadge = `<span style="background:${u.tipe==='produk'?'rgba(14,165,233,0.1)':'rgba(99,102,241,0.1)'};color:${u.tipe==='produk'?'var(--primary)':'#6366F1'};padding:2px 8px;border-radius:4px;font-size:11px;font-weight:500;text-transform:uppercase;">${u.tipe}</span>`;

                return `<tr>
                    <td style="padding:12px 16px; font-size:12px; color:var(--text-muted);">#${u.id}</td>
                    <td style="padding:12px 16px;">
                        <div style="font-weight:600; font-size:13px;">${nama}</div>
                        <div style="font-size:11px; color:var(--text-muted);">${email}</div>
                    </td>
                    <td style="padding:12px 16px;">${tipeBadge}</td>
                    <td style="padding:12px 16px; font-size:13px; max-width:160px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="${subjek}">${subjek}</td>
                    <td style="padding:12px 16px;">${stars}</td>
                    <td style="padding:12px 16px; font-size:13px; color:var(--text);">${kom}</td>
                    <td style="padding:12px 16px; font-size:12px; color:var(--text-muted);">${tgl}</td>
                    <td style="padding:12px 16px;">${visibleBadge}</td>
                    <td style="padding:12px 16px;">
                        <div style="display:flex; gap:8px; flex-wrap:wrap;">
                            <button onclick="toggleVisibility(${u.id}, this)" title="${u.is_visible ? 'Sembunyikan' : 'Tampilkan'}"
                                style="background:${u.is_visible?'rgba(245,158,11,0.1)':'rgba(34,197,94,0.1)'};color:${u.is_visible?'#D97706':'#15803D'};border:none;padding:6px 10px;border-radius:6px;cursor:pointer;font-size:12px;">
                                ${u.is_visible ? '🙈 Sembunyikan' : '👁 Tampilkan'}
                            </button>
                            <button onclick="konfirmasiHapus(${u.id})"
                                style="background:rgba(239,68,68,0.1);color:#DC2626;border:none;padding:6px 10px;border-radius:6px;cursor:pointer;font-size:12px;">
                                🗑 Hapus
                            </button>
                        </div>
                    </td>
                </tr>`;
            }).join('');

            renderPagination(res.data, loadUlasan);
        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="9" style="text-align:center; padding:40px; color:var(--danger);">Gagal memuat data ulasan.</td></tr>';
        }
    }

    async function toggleVisibility(id, btn) {
        btn.disabled = true;
        try {
            await apiFetch(`/admin/ulasan/${id}/visibility`, { method: 'PATCH' });
            await loadUlasan(currentPage);
        } catch (e) {
            showToast('Gagal mengubah visibilitas ulasan.', 'error');
        } finally {
            btn.disabled = false;
        }
    }

    function konfirmasiHapus(id) {
        ulasanIdToDelete = id;
        const modal = document.getElementById('modal-hapus');
        modal.style.display = 'flex';
        document.getElementById('btn-konfirmasi-hapus').onclick = async () => {
            modal.style.display = 'none';
            try {
                await apiFetch(`/admin/ulasan/${ulasanIdToDelete}`, { method: 'DELETE' });
                showToast('Ulasan berhasil dihapus.', 'success');
                await loadUlasan(currentPage);
            } catch (e) {
                showToast('Gagal menghapus ulasan.', 'error');
            }
        };
    }

    function renderPagination(data, callback) {
        const el = document.getElementById('ulasan-pagination');
        if (!data || data.last_page <= 1) { el.innerHTML = ''; return; }
        let html = '';
        for (let p = 1; p <= data.last_page; p++) {
            html += `<button onclick="loadUlasan(${p})" style="padding:6px 14px; border-radius:6px; border: 1px solid var(--glass-border); background:${p===data.current_page?'var(--primary)':'transparent'}; color:${p===data.current_page?'#fff':'var(--text)'}; cursor:pointer;">${p}</button>`;
        }
        el.innerHTML = html;
    }
</script>
@endpush
