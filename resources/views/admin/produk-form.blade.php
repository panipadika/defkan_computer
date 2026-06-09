@extends('layouts.admin')

@section('title', isset($id) ? 'Admin — Edit Produk | Defkan Computer' : 'Admin — Tambah Produk | Defkan Computer')
@section('admin-title', isset($id) ? 'Edit Produk' : 'Tambah Produk')
@section('tab-produk', 'active')

@section('content')
    <div class="produk-layout">
        <div class="glass-panel form-section">
            <div class="form-header">
                <h2 id="form-title" class="section-title">
                    <i data-lucide="{{ isset($id) ? 'edit-2' : 'plus-circle' }}" class="icon text-primary"></i> 
                    {{ isset($id) ? 'Edit Produk' : 'Tambah Produk Baru' }}
                </h2>
                <a href="/admin/produk" class="btn-cancel-modern">
                    <i data-lucide="arrow-left" class="btn-icon"></i> Kembali
                </a>
            </div>

            <form id="produk-form" onsubmit="submitProduk(event)">
                <input type="hidden" id="edit-id" value="{{ isset($id) ? $id : '' }}">

                <div class="form-grid">
                    {{-- Row 1 --}}
                    <div class="form-group span-3">
                        <label for="f-nama">Nama Produk *</label>
                        <input type="text" id="f-nama" class="form-control-modern" required
                            placeholder="Contoh: ASUS VivoBook 14">
                    </div>
                    <div class="form-group span-2">
                        <label for="f-harga">Harga (Rp) *</label>
                        <input type="number" id="f-harga" class="form-control-modern" required
                            placeholder="Contoh: 4500000">
                    </div>
                    <div class="form-group span-1">
                        <label for="f-stok">Stok *</label>
                        <input type="number" id="f-stok" class="form-control-modern" required placeholder="Contoh: 1"
                            min="0">
                    </div>
                    <div class="form-group span-2">
                        <label for="f-kategori">Kategori</label>
                        <select id="f-kategori" class="form-control-modern">
                            <option value="">Pilih Kategori</option>
                            <option>Laptop Pelajar / Mahasiswa</option>
                            <option>Laptop Kantor / Kerja</option>
                            <option>Laptop Gaming</option>
                            <option>Laptop Desain & Editing</option>
                            <option>Laptop Programming</option>
                            <option>Laptop Tipis & Ringan</option>
                            <option>Laptop Budget / Hemat</option>
                        </select>
                    </div>
                    <div class="form-group span-2">
                        <label for="f-merek">Merek</label>
                        <select id="f-merek" class="form-control-modern">
                            <option value="">Pilih Merek</option>
                            <option value="ASUS">ASUS</option>
                            <option value="Lenovo">Lenovo</option>
                            <option value="HP">HP</option>
                             <option value="Advan">Advan</option>
                            <option value="Zyrex">Zyrex</option>
                            <option value="Acer">Acer</option>
                            <option value="Dell">Dell</option>
                            <option value="Apple">Apple</option>
                            <option value="Axioo">Axioo</option>
                            <option value="MSI">MSI</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group span-2">
                        <label for="f-ram">RAM (GB)</label>
                        <input type="number" id="f-ram" class="form-control-modern" placeholder="Contoh: 8" min="0">
                    </div>

                    {{-- Row 2 --}}
                    <div class="form-group span-2">
                        <label for="f-storage">Storage (GB)</label>
                        <input type="number" id="f-storage" class="form-control-modern" placeholder="Contoh: 512" min="0">
                    </div>
                    <div class="form-group span-2">
                        <label for="f-vga">VGA / GPU</label>
                        <input type="text" id="f-vga" class="form-control-modern" placeholder="Contoh: RTX 3060">
                    </div>
                    <div class="form-group span-2">
                        <label for="f-cpu">CPU / Processor</label>
                        <input type="text" id="f-cpu" class="form-control-modern" placeholder="Contoh: Core i5-1135G7">
                    </div>

                    {{-- Upload Foto Utama --}}
                    <div class="form-group span-3" style="grid-row: span 2;">
                        <label>Foto Produk Utama *</label>
                        <div class="upload-zone-modern">
                            <div class="image-preview-wrapper" id="preview-main-wrapper" style="display: none;">
                                <img id="img-preview-main" src="" alt="Main Preview">
                                <button type="button" class="btn-remove-img" onclick="clearMainImage()"><i
                                        data-lucide="x"></i></button>
                            </div>
                            <div class="upload-placeholder" id="placeholder-main">
                                <i data-lucide="cloud-upload" class="upload-icon text-primary"></i>
                                <span class="upload-text">Klik untuk upload</span>
                                <span class="upload-hint">JPG, PNG, WebP. Maks 2MB</span>
                                <input type="file" id="f-foto" accept="image/*"
                                    onchange="previewImage(this, 'img-preview-main', 'preview-main-wrapper', 'placeholder-main')">
                            </div>
                        </div>
                    </div>

                    {{-- Upload Galeri --}}
                    <div class="form-group span-3" style="grid-row: span 2;">
                        <label>Galeri Foto Pendukung (Maks. 5 foto)</label>
                        <div class="upload-zone-modern">
                            <div class="galeri-preview-grid" id="galeri-preview-container" style="display: none;"></div>
                            <div class="upload-placeholder" id="placeholder-galeri">
                                <i data-lucide="cloud-upload" class="upload-icon text-primary"></i>
                                <span class="upload-text">Klik untuk upload</span>
                                <span class="upload-hint">JPG, PNG, WebP. Maks 2MB per file</span>
                                <input type="file" id="f-galeri" accept="image/*" multiple onchange="previewGallery(this)">
                            </div>
                        </div>
                    </div>

                    {{-- Row 3 --}}
                    <div class="form-group span-6" style="grid-row: span 2;">
                        <label for="f-deskripsi">Deskripsi</label>
                        <textarea id="f-deskripsi" class="form-control-modern" rows="8"
                            placeholder="Tuliskan deskripsi singkat kondisi, kelebihan produk, dll." style="resize: none;" maxlength="1000"></textarea>
                    </div>

                    <div class="form-group"
                        style="grid-column: 7 / span 6; display: flex; align-items: flex-end; justify-content: flex-end; gap: 8px; flex-direction: row; height: 100%; margin-top: 10px;">
                        <button type="submit" class="btn-submit-modern" id="submit-btn"
                            style="width: 160px; height: 40px; font-size: 13px;">
                            <i data-lucide="{{ isset($id) ? 'save' : 'plus-square' }}" class="btn-icon"></i> <span id="submit-text">{{ isset($id) ? 'Simpan Perubahan' : 'Simpan Produk' }}</span>
                        </button>
                        <button type="button" class="btn-cancel-modern" onclick="resetForm()" style="width: 100px; height: 40px; font-size: 13px;">
                            <i data-lucide="refresh-cw" class="btn-icon"></i> Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .produk-layout {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 16px;
            height: calc(100vh - 96px);
            overflow: hidden;
        }

        .form-section {
            padding: 24px 30px 10px 30px;
            background: var(--bg-surface);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
        }

        #produk-form {
            flex: 1;
            overflow: hidden;
            padding-right: 0;
            padding-bottom: 0;
            display: flex;
            flex-direction: column;
        }

        .form-header {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--glass-border);
            padding-bottom: 16px;
            flex-shrink: 0;
        }

        .section-title {
            font-size: 16px;
            font-weight: 800;
            color: var(--text-main);
            margin: 0;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 16px 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group label {
            font-size: 11.5px;
            font-weight: 700;
            color: var(--text-muted);
        }

        .form-control-modern {
            background: var(--bg-surface);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-sm);
            padding: 8px 12px;
            color: var(--text-main);
            font-size: 13px;
            font-family: inherit;
            outline: none;
            transition: var(--transition);
            width: 100%;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.01) inset;
            height: 38px;
        }

        textarea.form-control-modern {
            height: auto;
        }

        .form-control-modern:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            background: white;
        }

        select.form-control-modern {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 14px;
            padding-right: 30px;
        }

        .span-1 { grid-column: span 1; }
        .span-2 { grid-column: span 2; }
        .span-3 { grid-column: span 3; }
        .span-4 { grid-column: span 4; }
        .span-6 { grid-column: span 6; }
        .span-12 { grid-column: span 12; }

        @media (max-width: 1024px) {
            .form-grid { grid-template-columns: repeat(6, 1fr); }
            .span-1, .span-2, .span-3 { grid-column: span 3; }
            .span-4, .span-6 { grid-column: span 6; }
        }

        /* Upload Zones */
        .upload-zone-modern {
            position: relative;
            border: 1px dashed var(--glass-border);
            border-radius: var(--radius-sm);
            background: rgba(248, 250, 252, 0.5);
            transition: var(--transition);
            height: 110px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .upload-zone-modern:hover {
            border-color: var(--primary);
            background: rgba(37, 99, 235, 0.02);
        }

        .upload-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 16px;
            cursor: pointer;
            width: 100%;
            height: 100%;
            position: relative;
        }

        .upload-placeholder input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
        }

        .upload-icon {
            width: 24px;
            height: 24px;
            margin-bottom: 6px;
        }

        .upload-text {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 4px;
        }

        .upload-hint {
            font-size: 11px;
            color: var(--text-muted);
        }

        /* Image Previews */
        .image-preview-wrapper {
            width: 100%;
            height: 100%;
            position: relative;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .image-preview-wrapper img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .btn-remove-img {
            position: absolute;
            top: 6px;
            right: 6px;
            background: rgba(239, 68, 68, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 10px;
        }

        .galeri-preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
            gap: 8px;
            padding: 10px;
            width: 100%;
        }

        .galeri-preview-item {
            position: auto;
            aspect-ratio: 1;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid var(--glass-border);
        }

        .galeri-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .btn-submit-modern,
        .btn-cancel-modern {
            font-family: inherit;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: var(--transition);
            text-decoration: none;
        }

        .btn-submit-modern {
            background: var(--primary);
            color: white;
            border: none;
        }

        .btn-submit-modern:hover {
            background: #1d4ed8;
        }

        .btn-cancel-modern {
            background: white;
            border: 1px solid var(--glass-border);
            color: var(--text-main);
        }

        .btn-cancel-modern:hover {
            background: #f8fafc;
        }
    </style>
@endpush

@push('scripts')
    <script>
        const editId = document.getElementById('edit-id').value;

        document.addEventListener('DOMContentLoaded', () => {
            if (!isLoggedIn() || !isAdmin()) { window.location.href = '/'; return; }
            if (editId) {
                loadProdukData(editId);
            }
        });

        async function loadProdukData(id) {
            try {
                const res = await apiFetch(`/produk/${id}`);
                const p = res.data;

                document.getElementById('f-nama').value = p.nama_produk;
                document.getElementById('f-harga').value = p.harga;
                document.getElementById('f-stok').value = p.stok;
                document.getElementById('f-kategori').value = p.kategori || '';
                document.getElementById('f-merek').value = p.merek || '';
                document.getElementById('f-ram').value = p.ram || '';
                document.getElementById('f-storage').value = p.storage || '';
                document.getElementById('f-vga').value = p.vga || '';
                document.getElementById('f-cpu').value = p.cpu || '';
                document.getElementById('f-deskripsi').value = p.deskripsi || '';

                const previewImg = document.getElementById('img-preview-main');
                const previewWrapper = document.getElementById('preview-main-wrapper');
                const placeholder = document.getElementById('placeholder-main');

                if (p.foto) {
                    const fotoSrc = p.foto.startsWith('http') ? p.foto : '/storage/' + p.foto;
                    previewImg.src = fotoSrc;
                    previewWrapper.style.display = 'block';
                    placeholder.style.display = 'none';
                }

                const galleryContainer = document.getElementById('galeri-preview-container');
                if (p.galeri_foto && p.galeri_foto.length > 0) {
                    galleryContainer.style.display = 'grid';
                    galleryContainer.innerHTML = p.galeri_foto.map(img => {
                        const imgUrl = img.startsWith('http') ? img : '/storage/' + img;
                        return `<div class="galeri-preview-item"><img src="${imgUrl}"></div>`;
                    }).join('');
                }
            } catch (err) {
                showToast('Gagal memuat data produk: ' + err.message, 'error');
            }
        }

        function previewImage(input, previewImgId, wrapperId, placeholderId) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById(previewImgId).src = e.target.result;
                    document.getElementById(wrapperId).style.display = 'block';
                    document.getElementById(placeholderId).style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        }

        function clearMainImage() {
            document.getElementById('f-foto').value = '';
            document.getElementById('img-preview-main').src = '';
            document.getElementById('preview-main-wrapper').style.display = 'none';
            document.getElementById('placeholder-main').style.display = 'flex';
        }

        function previewGallery(input) {
            const files = input.files;
            const container = document.getElementById('galeri-preview-container');
            if (files.length > 0) {
                container.style.display = 'grid';
                container.innerHTML = '';
                Array.from(files).slice(0, 5).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const div = document.createElement('div');
                        div.className = 'galeri-preview-item';
                        div.innerHTML = `<img src="${e.target.result}">`;
                        container.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });
            } else {
                container.style.display = 'none';
                container.innerHTML = '';
            }
        }

        async function submitProduk(e) {
            e.preventDefault();
            const btn = document.getElementById('submit-btn');
            const origTxt = btn.innerHTML;

            const formData = new FormData();
            formData.append('nama_produk', document.getElementById('f-nama').value);
            formData.append('harga', document.getElementById('f-harga').value);
            formData.append('stok', document.getElementById('f-stok').value);
            formData.append('kategori', document.getElementById('f-kategori').value);
            formData.append('merek', document.getElementById('f-merek').value);
            formData.append('ram', document.getElementById('f-ram').value);
            formData.append('storage', document.getElementById('f-storage').value);
            formData.append('vga', document.getElementById('f-vga').value);
            formData.append('cpu', document.getElementById('f-cpu').value);
            formData.append('deskripsi', document.getElementById('f-deskripsi').value);

            const fotoFile = document.getElementById('f-foto').files[0];
            if (fotoFile) formData.append('foto', fotoFile);

            const galeriFiles = document.getElementById('f-galeri').files;
            if (galeriFiles.length > 5) {
                showToast('Maksimal foto pendukung adalah 5 file!', 'error');
                return;
            }
            if (galeriFiles.length > 0) {
                for (let i = 0; i < galeriFiles.length; i++) {
                    formData.append('galeri[]', galeriFiles[i]);
                }
            }

            btn.innerHTML = '<div class="loader" style="width:14px; height:14px; border-width:2px; border-color:white;"></div> Menyimpan...'; 
            btn.disabled = true;

            try {
                if (editId) {
                    formData.append('_method', 'PUT');
                    await apiFetch(`/produk/${editId}`, { method: 'POST', body: formData });
                    showToast('Produk berhasil diperbarui!');
                } else {
                    await apiFetch('/produk', { method: 'POST', body: formData });
                    showToast('Produk berhasil ditambahkan!');
                }
                
                setTimeout(() => {
                    window.location.href = '/admin/produk';
                }, 1000);
            } catch (err) {
                showToast('Gagal menyimpan produk: ' + err.message, 'error');
                btn.innerHTML = origTxt; 
                btn.disabled = false;
            }
        }

        function resetForm() {
            document.getElementById('produk-form').reset();
            clearMainImage();
            
            const galleryContainer = document.getElementById('galeri-preview-container');
            galleryContainer.style.display = 'none';
            galleryContainer.innerHTML = '';
        }
    </script>
@endpush
