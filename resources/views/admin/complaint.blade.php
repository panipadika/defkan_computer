@extends('layouts.admin')

@section('title', 'Kelola Komplain — Admin Defkan Computer')

@section('content')
<div class="admin-page-header">
    <div>
        <h1 class="admin-page-title">Komplain Pelanggan</h1>
        <p class="admin-page-subtitle">Tangani komplain pelanggan terkait pesanan dan servis.</p>
    </div>
</div>

<!-- Stats Cards -->
<div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:16px; margin-bottom:28px;" id="complaint-stats">
    <div class="stat-card"><div class="stat-label">Total</div><div class="stat-value" id="stat-total">—</div></div>
    <div class="stat-card"><div class="stat-label">Menunggu</div><div class="stat-value" id="stat-menunggu" style="color:#D97706;">—</div></div>
    <div class="stat-card"><div class="stat-label">Diproses</div><div class="stat-value" id="stat-diproses" style="color:var(--primary);">—</div></div>
    <div class="stat-card"><div class="stat-label">Selesai</div><div class="stat-value" id="stat-selesai" style="color:var(--success);">—</div></div>
    <div class="stat-card"><div class="stat-label">Ditolak</div><div class="stat-value" id="stat-ditolak" style="color:var(--danger);">—</div></div>
</div>

<!-- Filter Bar -->
<div class="filter-bar glass-panel" style="display:flex; gap:12px; flex-wrap:wrap; align-items:center; margin-bottom:20px; padding:16px 20px;">
    <input type="text" id="search-complaint" placeholder="Cari judul, nama pengguna…" class="form-input" style="flex:1; min-width:200px; max-width:320px;">
    <select id="filter-status" class="form-input" style="width:160px;">
        <option value="">Semua Status</option>
        <option value="menunggu">⏳ Menunggu</option>
        <option value="diproses">🔄 Diproses</option>
        <option value="selesai">✅ Selesai</option>
        <option value="ditolak">❌ Ditolak</option>
    </select>
    <select id="filter-tipe" class="form-input" style="width:140px;">
        <option value="">Semua Tipe</option>
        <option value="pesanan">Pesanan</option>
        <option value="servis">Servis</option>
    </select>
    <button onclick="loadComplaint()" class="btn btn-primary">Cari</button>
</div>

<!-- Tabel Complaint -->
<div class="glass-panel" style="overflow-x:auto;">
    <table class="admin-table" style="width:100%; border-collapse:collapse;">
        <thead>
            <tr>
                <th style="width:50px;">#</th>
                <th>Pengguna</th>
                <th style="width:80px;">Tipe</th>
                <th>Judul Komplain</th>
                <th style="width:120px;">Referensi</th>
                <th style="width:120px;">Status</th>
                <th style="width:100px;">Tanggal</th>
                <th style="width:100px;">Aksi</th>
            </tr>
        </thead>
        <tbody id="complaint-tbody">
            <tr><td colspan="8" style="text-align:center; padding:40px; color:var(--text-muted);">Memuat data…</td></tr>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div id="complaint-pagination" style="margin-top:16px; display:flex; justify-content:center; gap:8px;"></div>

<!-- Modal Detail & Respons -->
<div id="modal-complaint" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:flex-start; justify-content:center; padding:40px 16px; overflow-y:auto;">
    <div class="glass-panel" style="max-width:600px; width:100%; padding:32px; position:relative;">
        <button onclick="tutupModal()" style="position:absolute; top:16px; right:16px; background:none; border:none; cursor:pointer; font-size:20px; color:var(--text-muted);">✕</button>
        <h3 id="modal-judul" style="font-size:18px; font-weight:700; margin-bottom:4px;">Detail Komplain</h3>
        <div id="modal-meta" style="font-size:12px; color:var(--text-muted); margin-bottom:20px;"></div>

        <div class="form-group" style="margin-bottom:16px;">
            <label style="font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;">Deskripsi Masalah</label>
            <div id="modal-deskripsi" style="margin-top:8px; padding:14px; background:rgba(0,0,0,0.03); border-radius:8px; font-size:14px; line-height:1.6; border: 1px solid var(--glass-border);"></div>
        </div>

        <div id="modal-foto-container" style="margin-bottom:16px; display:none;">
            <label style="font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px;">Foto Bukti</label>
            <div id="modal-foto" style="display:flex; gap:10px; flex-wrap:wrap; margin-top:8px;"></div>
        </div>

        <div id="modal-respons-lama" style="display:none; margin-bottom:16px;">
            <label style="font-size:12px; font-weight:600; color:var(--success); text-transform:uppercase; letter-spacing:0.5px;">Respons Admin Sebelumnya</label>
            <div id="modal-respons-teks" style="margin-top:8px; padding:14px; background:rgba(34,197,94,0.05); border-radius:8px; font-size:14px; line-height:1.6; border: 1px solid rgba(34,197,94,0.2);"></div>
        </div>

        <hr style="border-color:var(--glass-border); margin: 20px 0;">
        <h4 style="font-size:15px; font-weight:600; margin-bottom:16px;">Kirim Respons</h4>

        <input type="hidden" id="modal-complaint-id">
        <div class="form-group" style="margin-bottom:14px;">
            <label class="form-label">Status</label>
            <select id="modal-status" class="form-input">
                <option value="diproses">🔄 Sedang Diproses</option>
                <option value="selesai">✅ Selesai</option>
                <option value="ditolak">❌ Ditolak</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom:20px;">
            <label class="form-label">Pesan Respons untuk Pelanggan</label>
            <textarea id="modal-respons-input" class="form-input" rows="4" placeholder="Tulis respons Anda untuk pelanggan…" style="resize:vertical;"></textarea>
        </div>
        <div style="display:flex; gap:12px; justify-content:flex-end;">
            <button onclick="tutupModal()" class="btn btn-outline">Batal</button>
            <button onclick="kirimRespons()" id="btn-kirim-respons" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:6px;">
                <i data-lucide="send" class="icon icon-sm"></i> Kirim Respons
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentPage = 1;

    document.addEventListener('DOMContentLoaded', () => {
        loadComplaint();
        document.getElementById('search-complaint').addEventListener('keydown', e => {
            if (e.key === 'Enter') loadComplaint();
        });
    });

    async function loadComplaint(page = 1) {
        currentPage = page;
        const search = document.getElementById('search-complaint').value;
        const status = document.getElementById('filter-status').value;
        const tipe   = document.getElementById('filter-tipe').value;

        const params = new URLSearchParams({ page, per_page: 15 });
        if (search) params.append('search', search);
        if (status) params.append('status', status);
        if (tipe)   params.append('tipe', tipe);

        const tbody = document.getElementById('complaint-tbody');
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:40px;"><div class="loader loader-centered"></div></td></tr>';

        try {
            const res   = await apiFetch(`/admin/complaint?${params}`);
            const stats = res.stats || {};

            document.getElementById('stat-total').textContent    = stats.total    || 0;
            document.getElementById('stat-menunggu').textContent = stats.menunggu || 0;
            document.getElementById('stat-diproses').textContent = stats.diproses || 0;
            document.getElementById('stat-selesai').textContent  = stats.selesai  || 0;
            document.getElementById('stat-ditolak').textContent  = stats.ditolak  || 0;

            const items = res.data?.data || [];
            if (items.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">Tidak ada komplain ditemukan.</td></tr>';
                renderPagination(res.data);
                return;
            }

            const statusColors = {
                menunggu: { bg: 'rgba(245,158,11,0.1)', color: '#D97706', label: '⏳ Menunggu' },
                diproses: { bg: 'rgba(14,165,233,0.1)', color: 'var(--primary)', label: '🔄 Diproses' },
                selesai:  { bg: 'rgba(34,197,94,0.1)',  color: 'var(--success)', label: '✅ Selesai' },
                ditolak:  { bg: 'rgba(239,68,68,0.1)',  color: 'var(--danger)',  label: '❌ Ditolak' },
            };

            tbody.innerHTML = items.map(c => {
                const sc  = statusColors[c.status] || { bg:'#eee', color:'#555', label: c.status };
                const tgl = new Date(c.created_at).toLocaleDateString('id-ID', {day:'numeric',month:'short',year:'numeric'});
                const tipeBadge = `<span style="background:${c.tipe==='pesanan'?'rgba(14,165,233,0.1)':'rgba(99,102,241,0.1)'};color:${c.tipe==='pesanan'?'var(--primary)':'#6366F1'};padding:2px 8px;border-radius:4px;font-size:11px;font-weight:500;text-transform:uppercase;">${c.tipe}</span>`;
                const ref = c.referensi_info?.label || `${c.tipe} #${c.id_referensi}`;

                return `<tr>
                    <td style="padding:12px 16px; font-size:12px; color:var(--text-muted);">#${c.id}</td>
                    <td style="padding:12px 16px;">
                        <div style="font-weight:600;font-size:13px;">${c.pengguna?.nama || '—'}</div>
                        <div style="font-size:11px;color:var(--text-muted);">${c.pengguna?.email || ''}</div>
                    </td>
                    <td style="padding:12px 16px;">${tipeBadge}</td>
                    <td style="padding:12px 16px; font-size:13px; max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="${c.judul}">${c.judul}</td>
                    <td style="padding:12px 16px; font-size:12px; color:var(--text-muted);">${ref}</td>
                    <td style="padding:12px 16px;">
                        <span style="background:${sc.bg};color:${sc.color};padding:4px 10px;border-radius:6px;font-size:11px;font-weight:600;">${sc.label}</span>
                    </td>
                    <td style="padding:12px 16px; font-size:12px; color:var(--text-muted);">${tgl}</td>
                    <td style="padding:12px 16px;">
                        <button onclick="bukaModal(${c.id})" class="btn btn-outline" style="padding:6px 12px; font-size:12px;">
                            Detail & Balas
                        </button>
                    </td>
                </tr>`;
            }).join('');

            renderPagination(res.data);
            if (window.lucide) lucide.createIcons();
        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--danger);">Gagal memuat data komplain.</td></tr>';
        }
    }

    async function bukaModal(id) {
        document.getElementById('modal-complaint').style.display = 'flex';
        document.getElementById('modal-judul').textContent = 'Memuat detail…';
        document.getElementById('modal-deskripsi').textContent = '';
        document.getElementById('modal-respons-lama').style.display = 'none';

        try {
            const res = await apiFetch(`/admin/complaint/${id}`);
            const c   = res.data;

            document.getElementById('modal-complaint-id').value = c.id;
            document.getElementById('modal-judul').textContent  = c.judul;
            document.getElementById('modal-meta').innerHTML     = `
                <span style="margin-right:12px;">👤 ${c.pengguna?.nama || '—'} (${c.pengguna?.email || ''})</span>
                <span style="margin-right:12px;">${c.tipe === 'pesanan' ? '🛒 Pesanan' : '🔧 Servis'} #${c.id_referensi}</span>
                <span>📅 ${new Date(c.created_at).toLocaleDateString('id-ID', {day:'numeric',month:'long',year:'numeric'})}</span>
            `;
            document.getElementById('modal-deskripsi').textContent = c.deskripsi;

            // Foto bukti
            const fotoContainer = document.getElementById('modal-foto-container');
            const fotoEl        = document.getElementById('modal-foto');
            if (c.foto_bukti_urls && c.foto_bukti_urls.length > 0) {
                fotoEl.innerHTML = c.foto_bukti_urls.map(url =>
                    `<a href="${url}" target="_blank"><img src="${url}" style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:1px solid var(--glass-border);"></a>`
                ).join('');
                fotoContainer.style.display = 'block';
            } else {
                fotoContainer.style.display = 'none';
            }

            // Respons sebelumnya
            if (c.respons_admin) {
                document.getElementById('modal-respons-teks').textContent = c.respons_admin;
                document.getElementById('modal-respons-lama').style.display = 'block';
            }

            // Set status default
            document.getElementById('modal-status').value = c.status === 'menunggu' ? 'diproses' : c.status;
            document.getElementById('modal-respons-input').value = '';
        } catch (e) {
            document.getElementById('modal-judul').textContent = 'Gagal memuat detail.';
        }
    }

    function tutupModal() {
        document.getElementById('modal-complaint').style.display = 'none';
    }

    async function kirimRespons() {
        const id      = document.getElementById('modal-complaint-id').value;
        const status  = document.getElementById('modal-status').value;
        const respons = document.getElementById('modal-respons-input').value.trim();
        const btn     = document.getElementById('btn-kirim-respons');

        if (!respons) {
            showToast('Pesan respons tidak boleh kosong.', 'error');
            return;
        }

        btn.disabled    = true;
        btn.textContent = 'Mengirim…';

        try {
            await apiFetch(`/admin/complaint/${id}/respond`, {
                method: 'POST',
                body: JSON.stringify({ status, respons_admin: respons }),
                headers: { 'Content-Type': 'application/json' },
            });
            showToast('Respons komplain berhasil dikirim!', 'success');
            tutupModal();
            await loadComplaint(currentPage);
        } catch (e) {
            showToast('Gagal mengirim respons. Silakan coba lagi.', 'error');
        } finally {
            btn.disabled    = false;
            btn.innerHTML   = '<i data-lucide="send" class="icon icon-sm"></i> Kirim Respons';
            if (window.lucide) lucide.createIcons();
        }
    }

    function renderPagination(data) {
        const el = document.getElementById('complaint-pagination');
        if (!data || data.last_page <= 1) { el.innerHTML = ''; return; }
        let html = '';
        for (let p = 1; p <= data.last_page; p++) {
            html += `<button onclick="loadComplaint(${p})" style="padding:6px 14px;border-radius:6px;border:1px solid var(--glass-border);background:${p===data.current_page?'var(--primary)':'transparent'};color:${p===data.current_page?'#fff':'var(--text)'};cursor:pointer;">${p}</button>`;
        }
        el.innerHTML = html;
    }
</script>
@endpush
