@extends('layouts.app')

@section('title', 'Defkan Computer — Servis & Jual Beli Laptop Premium Terpercaya')

@push('styles')
@endpush

@section('content')

    <!-- ===================== -->
    <!-- HERO SECTION          -->
    <!-- ===================== -->
    <div class="hero-section"
        style="position: relative; background: url('{{ asset('img/storefront_defkan.jpg') }}') no-repeat center center/cover; display: flex; align-items: center; justify-content: center; margin-top: -100px; overflow: hidden;">
        <!-- Light sky-blue overlay to make text highly readable and blend with the bright theme -->
        <div class="hero-overlay"></div>

        <!-- Subtle modern glow effect -->
        <div class="hero-glow"></div>

        <div class="hero-content-wrapper container">
            <!-- Tagline: LAPTOP SECOND TERBAIK -->
            <div class="hero-tagline-container">
                <div class="hero-tagline-line"></div>
                <span class="hero-tagline">LAPTOP SECOND TERBAIK & SERVIS PREMIUM</span>
                <div class="hero-tagline-line"></div>
            </div>

            <!-- Massive Bold Headline (Google Sans font) -->
            <h1 class="hero-title" style="font-family: 'Google Sans', system-ui, sans-serif; font-weight: 800;">
                LAPTOP BEKAS BERKUALITAS & <span class="hero-title-highlight">SERVIS LAPTOP TERPERCAYA</span>
            </h1>

            <!-- Subtitle/Description -->
            <p class="hero-desc">
                Spesialis laptop second berkualitas di Tasikmalaya. Layanan servis profesional, cepat, & transparan.
            </p>

            <!-- CTA Buttons -->
            <div class="hero-btn-container">
                <a href="/produk" class="btn btn-hero-primary">
                    <i data-lucide="search" class="icon icon-sm"></i> Cari Laptop
                </a>
                <a href="https://maps.google.com/?q=Defkan+Computer+Tasikmalaya" target="_blank"
                    class="btn btn-hero-outline">
                    <i data-lucide="map-pin" class="icon icon-sm"></i> Lokasi Toko
                </a>
            </div>

            <!-- Premium Stats Section -->
            <div class="hero-stats">
                <div class="hero-stat-item">
                    <div class="hero-stat-val">5.000<span style="color: var(--primary);">+</span></div>
                    <div class="hero-stat-lbl">Laptop Lebih Terjual</div>
                </div>
                <div class="hero-stat-item">
                    <div class="hero-stat-val"
                        style="display: inline-flex; align-items: center; gap: 4px; justify-content: center;"><i
                            data-lucide="star" class="icon icon-sm"
                            style="color: #F59E0B; fill: #F59E0B; vertical-align: middle;"></i> 4.9</div>
                    <div class="hero-stat-lbl">Rating Toko</div>
                </div>
                <div class="hero-stat-item-last">
                    <div class="hero-stat-val">1 <span
                            style="color: var(--primary); font-size: 18px; font-family: 'Outfit', sans-serif;">bln</span>
                    </div>
                    <div class="hero-stat-lbl">Garansi Toko</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rest of the home page inside standard container -->
    <div class="container" style="margin-top: 64px; margin-bottom: 80px;">

        <!-- ===================== -->
        <!-- QUICK FEATURES        -->
        <!-- ===================== -->
        <div class="home-features">
            <a href="/servis" style="text-decoration: none;">
                <div class="feature-card">
                    <div class="feature-icon"><i data-lucide="wrench"
                            style="width: 48px; height: 48px; stroke-width: 1.5;"></i></div>
                    <h3 class="feature-title">Servis Cepat</h3>
                    <p class="text-muted feature-desc">Diagnosa dan servis laptop dengan teknisi berpengalaman. Transparansi
                        harga & durasi pengerjaan.</p>
                </div>
            </a>
            <a href="/produk" style="text-decoration: none;">
                <div class="feature-card">
                    <div class="feature-icon"><i data-lucide="shopping-bag"
                            style="width: 48px; height: 48px; stroke-width: 1.5;"></i></div>
                    <h3 class="feature-title">Katalog Laptop</h3>
                    <p class="text-muted feature-desc">Katalog laptop bekas berkualitas dengan garansi toko. Pilihan lengkap
                        dari berbagai merek terkemuka.</p>
                </div>
            </a>
            <a href="/rekomendasi" style="text-decoration: none;">
                <div class="feature-card" style="transition: var(--transition);"
                    onmouseover="this.style.borderColor='var(--secondary)'"
                    onmouseout="this.style.borderColor='var(--glass-border)'">
                    <div class="feature-icon" style="color: #1D4ED8;"><i data-lucide="laptop"
                            style="width: 48px; height: 48px; stroke-width: 1.5;"></i></div>
                    <h3 class="feature-title">Rekomendasi Laptop</h3>
                    <p class="text-muted feature-desc">Bingung pilih laptop? Kami akan merekomendasikan laptop terbaik
                        sesuai kebutuhan software Anda.</p>
                </div>
            </a>
        </div>

        <!-- ===================== -->
        <!-- FEATURED PRODUCTS     -->
        <!-- ===================== -->
        <div class="home-products-section">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Produk <span class="text-primary-gradient">Terbaru</span></h2>
                    <p class="text-muted section-subtitle">Laptop bekas pilihan dengan kondisi terbaik.</p>
                </div>
                <a href="/produk" class="btn btn-outline btn-view-all"
                    style="display: inline-flex; align-items: center; gap: 4px;">Lihat Semua <i data-lucide="arrow-right"
                        class="icon icon-sm"></i></a>
            </div>

            <div id="featured-loading" class="loading-wrapper">
                <div class="loader loader-centered"></div>
            </div>
            <div id="featured-grid" class="grid" style="display: none;"></div>
        </div>

        <!-- ===================== -->
        <!-- REKOMENDASI LAPTOP    -->
        <!-- ===================== -->
        <div class="glass-panel ai-banner">
            <div class="ai-banner-overlay"></div>
            <div class="ai-banner-glow"></div>
            <div class="ai-banner-content">
                <div class="ai-banner-icon">
                    <i data-lucide="laptop" style="color: #1D4ED8; width: 64px; height: 64px; stroke-width: 1.5;"></i>
                </div>
                <h2 class="ai-banner-title">Bingung Pilih Laptop?</h2>
                <p class="text-muted ai-banner-desc">
                    Kami akan menganalisis kebutuhan software Anda dan merekomendasikan laptop yang tepat dengan spesifikasi
                    yang pas dan harga terbaik.
                </p>
                <div class="ai-software-tags">
                    @foreach(['Microsoft Office', 'Adobe Photoshop', 'Android Studio', 'Unity 3D', 'AutoCAD'] as $sw)
                        <span class="ai-software-tag">{{ $sw }}</span>
                    @endforeach
                    <span class="text-muted ai-software-tag-more">+8 lainnya...</span>
                </div>
                <a href="/rekomendasi" class="btn btn-primary"
                    style="display: inline-flex; align-items: center; gap: 8px; color:">
                    <i data-lucide="sparkles" class="icon" style="color: #fff;"></i> Rekomendasi Laptop
                </a>
            </div>
        </div>

        <!-- ===================== -->
        <!-- SERVIS INFO           -->
        <!-- ===================== -->
        <div class="glass-panel servis-section">
            <div class="servis-grid">
                <div>
                    <h2 class="servis-title">Layanan Servis <span class="text-primary-gradient">Profesional</span></h2>
                    <p class="text-muted servis-desc">
                        Dari masalah layar rusak hingga keyboard tidak berfungsi, tim teknisi berpengalaman kami siap
                        menangani dengan cepat dan transparan.
                    </p>
                    <div class="servis-features">
                        <div class="servis-feature-item">
                            <i data-lucide="check-circle-2" class="icon" style="color: var(--success);"></i> Estimasi biaya
                            transparan sebelum dikerjakan
                        </div>
                        <div class="servis-feature-item">
                            <i data-lucide="smartphone" class="icon" style="color: var(--primary);"></i> Tracking status
                            servis secara real-time
                        </div>
                        <div class="servis-feature-item">
                            <i data-lucide="shield-check" class="icon" style="color: var(--success);"></i> Garansi servis 30
                            hari
                        </div>
                        <div class="servis-feature-item">
                            <i data-lucide="zap" class="icon" style="color: var(--warning); fill: var(--warning);"></i>
                            Pengerjaan cepat 1-5 hari kerja
                        </div>
                    </div>
                    <a href="/servis" class="btn btn-primary btn-servis-cta"
                        style="display: inline-flex; align-items: center; gap: 6px;"><i data-lucide="wrench"
                            class="icon icon-sm"></i> Daftarkan Servis</a>
                </div>
                <div>
                    <div id="estimasi-list" class="estimasi-list">
                        <div class="flex-center estimasi-loading">
                            <div class="loader"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            loadFeaturedProducts();
            loadEstimasiServis();
        });

        async function loadFeaturedProducts() {
            const grid = document.getElementById('featured-grid');
            const loading = document.getElementById('featured-loading');

            try {
                const res = await apiFetch('/produk?tersedia=1');
                const products = (res.data?.data || res.data || []).slice(0, 4);

                loading.style.display = 'none';
                grid.style.display = 'grid';

                if (products.length === 0) {
                    grid.innerHTML = '<p class="text-muted" style="text-align:center; grid-column: 1/-1;">Belum ada produk yang tersedia saat ini.</p>';
                    return;
                }

                products.forEach((p, idx) => {
                    const fotoUrl = p.foto_url || `https://placehold.co/400x300/EFF8FF/0EA5E9?text=${encodeURIComponent(p.merek || 'Laptop')}`;
                    const card = document.createElement('div');
                    card.className = 'card';
                    card.style.animationDelay = `${idx * 0.07}s`;
                    card.innerHTML = `
                    <div onclick="window.location='/produk'">
                        <img src="${fotoUrl}" class="card-img" alt="${p.nama_produk}" onerror="this.src='https://placehold.co/400x300/EFF8FF/0EA5E9?text=Laptop'">
                    </div>
                    <div class="card-body">
                        <p style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">${p.merek || ''}</p>
                        <h3 class="card-title" style="font-size: 15px; margin-bottom: 10px;">${p.nama_produk}</h3>
                        <div style="display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 14px;">
                            ${p.ram ? `<span style="background: rgba(14,165,233,0.1); border: 1px solid rgba(14,165,233,0.2); padding: 2px 8px; border-radius: 4px; font-size: 11px; color: var(--primary);">RAM ${p.ram}GB</span>` : ''}
                            ${p.storage ? `<span style="background: rgba(3,105,161,0.1); border: 1px solid rgba(3,105,161,0.2); padding: 2px 8px; border-radius: 4px; font-size: 11px; color: var(--accent);">${p.storage}GB</span>` : ''}
                        </div>
                        <div style="font-size: 20px; font-weight: 800; color: var(--accent); margin-bottom: 14px;">${formatRupiah(p.harga)}</div>
                        <a href="/produk" class="btn btn-outline" style="width: 100%; text-align: center; padding: 10px;">Lihat Detail</a>
                    </div>
                `;
                    grid.appendChild(card);
                });
            } catch (e) {
                loading.style.display = 'none';
                grid.style.display = 'block';
                grid.innerHTML = '<p class="text-muted" style="text-align:center;">Gagal memuat produk.</p>';
            }
        }

        async function loadEstimasiServis() {
            const list = document.getElementById('estimasi-list');
            try {
                const res = await apiFetch('/servis/estimasi');
                const items = (res.data || []).slice(0, 6);
                list.innerHTML = items.map(e => `
                <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--glass-border); align-items: center;">
                    <div>
                        <div style="font-size: 14px; font-weight: 500;">${e.jenis_kerusakan}</div>
                        <div class="text-muted" style="font-size: 11px; margin-top: 4px; display: inline-flex; align-items: center; gap: 4px;">
                            <i data-lucide="clock" class="icon icon-sm"></i> ${e.estimasi_durasi || '1-3 Hari Kerja'}
                        </div>
                    </div>
                    <div style="font-size: 13px; font-weight: 700; color: var(--accent); white-space: nowrap; margin-left: 12px;">
                        ~ ${formatRupiah(e.harga_estimasi)}
                    </div>
                </div>
            `).join('') + `
                <a href="/servis" style="display: block; text-align: center; padding: 14px; font-size: 13px; color: var(--primary); text-decoration: none; margin-top: 8px; border: 1px solid rgba(14,165,233,0.3); border-radius: var(--radius-sm); transition: all 0.2s;" onmouseover="this.style.background='rgba(14,165,233,0.06)'" onmouseout="this.style.background='transparent'">
                    Lihat Semua Estimasi Servis →
                </a>
            `;
                lucide.createIcons();
            } catch (e) {
                list.innerHTML = '<p class="text-muted" style="text-align:center; font-size:13px;">Gagal memuat data estimasi.</p>';
            }
        }
    </script>
@endpush