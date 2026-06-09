@extends('layouts.app')

@section('title', 'Checkout Pesanan — Defkan Computer')

@section('content')
<div class="container page-container">

    {{-- Page Header --}}
    <div class="page-header">
        <h1 class="page-title">
            <i data-lucide="shield-check" class="icon icon-lg text-primary page-title-icon"></i> <span class="text-primary-gradient">Checkout</span> Pesanan
        </h1>
        <p class="text-muted page-subtitle">Selesaikan pesanan Anda dengan melengkapi informasi billing dan pengiriman.</p>
    </div>

    {{-- Loading --}}
    <div id="loading-state" class="flex-center empty-state" style="display:none !important;"></div>

    {{-- Main Checkout Layout --}}
    <div id="checkout-content" style="display: none;">
        <div class="checkout-layout-grid">
            
            {{-- Left Column: Forms --}}
            <div class="checkout-forms-col">
                {{-- Detil Billing & Pengiriman (Gabungan) --}}
                <div class="glass-panel checkout-card">
                    <h3 class="checkout-card-title">Detil Billing & Pengiriman</h3>

                    {{-- Billing Fields --}}
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" id="billing-fullname" class="form-control" placeholder="Nama lengkap Anda" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="form-label">Nomor HP <span class="text-danger">*</span></label>
                            <input type="text" id="billing-phone" class="form-control" placeholder="08xx-xxxx-xxxx" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" id="billing-email" class="form-control" placeholder="email@domain.com" required>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div style="border-top: 1px solid var(--glass-border); margin: 20px 0;"></div>

                    {{-- Shipping Method --}}
                    <label class="form-label" style="font-weight: 700; margin-bottom: 12px; display: block;">Metode Pengiriman</label>
                    <div class="shipping-options-grid">
                        <label class="shipping-option-card" id="option-online-label">
                            <input type="radio" name="shipping_method" value="online" checked onchange="toggleShippingMethod('online')">
                            <div class="shipping-option-content">
                                <div class="shipping-option-title">Belanja Online</div>
                                <div class="shipping-option-desc">Barang akan diantar oleh kurir.</div>
                            </div>
                        </label>
                        
                        <label class="shipping-option-card" id="option-pickup-label">
                            <input type="radio" name="shipping_method" value="pickup" onchange="toggleShippingMethod('pickup')">
                            <div class="shipping-option-content">
                                <div class="shipping-option-title">Pick up di Toko</div>
                                <div class="shipping-option-desc">Langsung ambil di toko Defkan Computer</div>
                            </div>
                        </label>
                    </div>

                    {{-- Online Shipping Block --}}
                    <div id="shipping-online-block" class="shipping-block">
                        <div class="address-box-wrapper">
                            <div class="address-box-title">Tambah alamat pengiriman</div>
                            <div class="address-box-desc">Tambahkan alamat pengiriman Anda. Perubahan berlaku untuk pesanan ini saja.</div>
                            <button class="btn-address-link" onclick="openAddressModal()">
                                <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i> Tambah alamat pengiriman
                            </button>
                        </div>
                        
                        <div class="form-group" style="margin-top: 16px;">
                            <label class="form-label">Layanan Ekspedisi <span class="text-danger">*</span></label>
                            <select id="ekspedisi-select" class="form-control" onchange="updateShippingCost()">
                                <option value="">Memuat...</option>
                            </select>
                        </div>
                    </div>

                    {{-- Pickup Block --}}
                    <div id="shipping-pickup-block" class="shipping-block" style="display: none;">
                        <div class="pickup-store-card">
                            <div class="pickup-store-title">DEFKAN COMPUTER</div>
                            <div class="pickup-store-address">Jl. Gubernur Sewaka No.102, Sambongjaya, Kec. Mangkubumi, Kab. Tasikmalaya, Jawa Barat 46181</div>
                            <div class="pickup-store-info">
                                Ongkos kirim: <strong>Rp 0</strong>, Estimasi Waktu Pengambilan Barang <strong>1-3 hari</strong>, diproses setelah pembayaran dikonfirmasi.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Summary & Payment --}}
            <div class="checkout-summary-col">
                <div class="glass-panel cart-summary-panel checkout-summary-card" style="padding: 24px;">
                    <h3 class="cart-summary-title" style="margin-bottom: 16px;">Pesanan Anda</h3>
                    <div id="summary-items-list" class="summary-items-list">
                        {{-- Filled dynamically via JS --}}
                    </div>
                    
                    <div class="cart-summary-items" style="margin-top: 16px; border-top: 1px solid var(--glass-border); padding-top: 16px;">
                        <div class="cart-summary-row">
                            <span class="text-muted" style="font-size: 13.5px;">Ongkos Kirim</span>
                            <span id="summary-shipping-cost" style="font-weight: 600;">Belum dipilih</span>
                        </div>
                        <div class="cart-summary-row">
                            <span class="text-muted" style="font-size: 13.5px;">Asuransi</span>
                            <span id="summary-insurance" style="font-weight: 600;">Rp 0</span>
                        </div>
                        <div class="cart-summary-row" style="margin-top: 8px; border-top: 1px dashed var(--glass-border); padding-top: 8px;">
                            <span class="text-muted" style="font-size: 14px; font-weight: 700;">Total</span>
                            <span id="summary-total-harga" class="cart-summary-total-lbl" style="font-size: 18px; font-weight: 800;">Rp 0</span>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 16px; margin-bottom: 0;">
                        <label class="form-label" style="font-size: 13px;">Catatan (opsional)</label>
                        <textarea id="catatan-input" class="form-control" rows="3" placeholder="Catatan terkait pesanan Anda, misalnya catatan khusus untuk pengiriman."></textarea>
                    </div>

                    {{-- Divider --}}
                    <div style="border-top: 1px solid var(--glass-border); margin: 24px 0;"></div>

                    {{-- Pembayaran Section --}}
                    <h3 class="cart-summary-title" style="margin-bottom: 16px;">Metode Pembayaran</h3>
                    
                    <div class="payment-method-list">
                        {{-- QRIS --}}
                        <label class="bank-option-card">
                            <input type="radio" name="payment_type" value="QRIS" checked onchange="togglePaymentType('QRIS')">
                            <div class="bank-option-logo-wrapper">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg" alt="QRIS" style="height: 18px; object-fit: contain;">
                            </div>
                            <div class="bank-option-info">
                                <div class="bank-option-title">QRIS (Gopay, OVO, Dana, LinkAja)</div>
                                <div class="bank-option-desc">Pembayaran Instan via QR Code</div>
                            </div>
                        </label>

                        {{-- Transfer Virtual Account Option (with integrated dropdown) --}}
                        <div class="bank-option-card bank-option-card-va" style="margin-top: 4px; flex-direction: column; align-items: stretch; padding: 0; overflow: visible;">
                            <label style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; cursor: pointer; margin: 0;">
                                <input type="radio" name="payment_type" value="VA" onchange="togglePaymentType('VA')">
                                <div class="bank-option-logo-wrapper">
                                    <i data-lucide="credit-card" class="icon text-primary" style="width: 22px; height: 22px;"></i>
                                </div>
                                <div class="bank-option-info">
                                    <div class="bank-option-title">Transfer Virtual Account</div>
                                    <div class="bank-option-desc">Transfer via Bank Pilihan Anda</div>
                                </div>
                            </label>

                            {{-- Inline dropdown, hidden by default --}}
                            <div id="va-dropdown-container" style="display: none; padding: 0 16px 14px 52px;">
                                <div class="custom-select-wrapper" style="margin-top: 0;">
                                    <div class="custom-select-trigger" onclick="toggleVaDropdown(event)">
                                        <div class="custom-select-trigger-content">
                                            <img id="selected-bank-logo" src="/img/bca.svg" style="height: 16px; object-fit: contain; margin-right: 12px;">
                                            <span id="selected-bank-name" style="font-size: 13px; font-weight: 700; color: var(--text-main);">BCA Virtual Account</span>
                                        </div>
                                        <i data-lucide="chevron-down" class="icon icon-sm" id="dropdown-chevron" style="transition: transform 0.2s; color: var(--text-muted);"></i>
                                    </div>
                                    <div class="custom-select-options" id="va-options-list">
                                        <div class="custom-select-option active" data-value="BCA" onclick="changeSelectedBank('BCA', 'BCA Virtual Account', '/img/bca.svg', event)">
                                            <img src="/img/bca.svg" alt="BCA">
                                            <span>BCA Virtual Account</span>
                                        </div>
                                        <div class="custom-select-option" data-value="Mandiri" onclick="changeSelectedBank('Mandiri', 'Mandiri Virtual Account', '/img/mandiri.svg', event)">
                                            <img src="/img/mandiri.svg" alt="Mandiri">
                                            <span>Mandiri Virtual Account</span>
                                        </div>
                                        <div class="custom-select-option" data-value="BNI" onclick="changeSelectedBank('BNI', 'BNI Virtual Account', '/img/bni.svg', event)">
                                            <img src="/img/bni.svg" alt="BNI">
                                            <span>BNI Virtual Account</span>
                                        </div>
                                        <div class="custom-select-option" data-value="BRI" onclick="changeSelectedBank('BRI', 'BRI Virtual Account', '/img/bri.svg', event)">
                                            <img src="/img/bri.svg" alt="BRI">
                                            <span>BRI Virtual Account</span>
                                        </div>
                                        <div class="custom-select-option" data-value="CIMB" onclick="changeSelectedBank('CIMB', 'CIMB Niaga Virtual Account', '/img/cimb.svg', event)">
                                            <img src="/img/cimb.svg" alt="CIMB">
                                            <span>CIMB Niaga Virtual Account</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <button class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 15px; font-weight: 800; margin-top: 18px; display: inline-flex; align-items: center; justify-content: center; gap: 8px;" onclick="handleCheckout()" id="btn-checkout">
                        <span>Checkout & Bayar</span>
                        <i data-lucide="send" class="icon icon-sm" style="width: 16px; height: 16px;"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>

    {{-- Shipping Address Modal --}}
    <div id="address-modal" class="modal-overlay" style="display: none;" onclick="closeAddressModal(event)">
        <div class="glass-panel modal-panel" style="max-width: 600px; padding: 24px;" onclick="event.stopPropagation()">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="font-size: 18px; font-weight: 700; margin: 0; color: var(--text-main);">Tambah alamat pengiriman</h2>
                <button onclick="closeAddressModal(true)" class="modal-close-btn" style="padding: 0;"><i data-lucide="x" class="icon"></i></button>
            </div>
            
            <div class="form-group" style="position: relative;">
                <label class="form-label">Cari Lokasi Tujuan</label>
                <div style="position: relative;">
                    <i data-lucide="map-pin" class="icon text-muted" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px;"></i>
                    <input type="text" id="modal-loc-search" class="form-control checkout-location-input" style="padding-left: 52px !important;" placeholder="Masukkan Desa/Kelurahan/Kecamatan/Kode Pos" autocomplete="off" oninput="handleLocSearch(this.value)">
                </div>
                {{-- Autocomplete Dropdown --}}
                <div id="loc-autocomplete-dropdown" class="loc-autocomplete-dropdown" style="display: none;"></div>
                {{-- Selected Location Badge --}}
                <div id="loc-selected-badge" style="display: none; margin-top: 8px;">
                    <span class="loc-badge">
                        <i data-lucide="map-pin" style="width: 12px; height: 12px;"></i>
                        <span id="loc-badge-text"></span>
                        <button onclick="clearSelectedLocation()" style="background: none; border: none; cursor: pointer; padding: 0; margin-left: 4px; color: var(--text-muted); line-height: 1;">×</button>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Alamat <span class="text-danger">*</span></label>
                <input type="text" id="modal-address-main" class="form-control" placeholder="Alamat lengkap, contoh: Jl. Raya Jakarta No. 123" required>
                <input type="text" id="modal-address-unit" class="form-control" style="margin-top: 8px;" placeholder="Unit, apartemen, atau gedung (opsional), contoh: Unit 33, Tower 5">
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label class="form-label">Nomor HP Penerima <span class="text-danger">*</span></label>
                    <input type="text" id="modal-recipient-phone" class="form-control" placeholder="08xx-xxxx-xxxx" required>
                </div>
                <div class="form-group col-md-6">
                    <label class="form-label">Nama Penerima <span class="text-danger">*</span></label>
                    <input type="text" id="modal-recipient-name" class="form-control" placeholder="Nama lengkap penerima" required>
                </div>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px; justify-content: flex-end;">
                <button class="btn btn-outline" style="padding: 10px 24px;" onclick="closeAddressModal(true)">Batalkan</button>
                <button class="btn btn-primary" style="padding: 10px 24px; background: #2563eb;" onclick="saveShippingAddress()">Simpan alamat</button>
            </div>
        </div>
    </div>

    {{-- Checkout Success Modal --}}
    <div id="checkout-modal" class="modal-overlay" style="display: none;">
        <div class="glass-panel modal-panel" style="max-width: 520px; padding: 24px;">
            <div class="checkout-success-header" style="text-align: center; margin-bottom: 20px;">
                <div style="font-size: 56px; margin-bottom: 8px;"><i data-lucide="check-circle" class="icon text-success" style="width: 56px; height: 40px; margin: 0 auto;"></i></div>
                <h2 class="checkout-success-title" style="font-size: 20px; font-weight: 800; margin-bottom: 4px;">Pesanan Berhasil Dibuat!</h2>
                <p class="text-muted" style="font-size: 14px;">Silakan lakukan pembayaran untuk memproses pesanan Anda.</p>
            </div>
            <div id="payment-instructions" style="margin-bottom: 24px;"></div>
            <div style="display: flex; gap: 12px;">
                <a href="/pesanan" class="btn btn-primary" style="flex: 1; text-align: center;">Lihat Pesanan</a>
                <button class="btn btn-outline" style="flex: 1;" onclick="closeModal()">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .checkout-layout-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 24px;
        align-items: start;
    }
    @media (max-width: 991px) {
        .checkout-layout-grid {
            grid-template-columns: 1fr;
        }
    }
    .checkout-summary-col {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .cart-summary-panel {
        position: static !important;
    }
    .checkout-card {
        padding: 24px;
        border-radius: 12px;
        margin-bottom: 24px;
    }
    .checkout-card-title {
        font-size: 15px;
        font-weight: 700;
        color: #ef4444; /* matching the red title numbering from user design */
        margin-bottom: 20px;
        margin-top: 0;
    }
    .form-row {
        display: flex;
        gap: 16px;
        margin-bottom: 16px;
    }
    @media (max-width: 576px) {
        .form-row {
            flex-direction: column;
            gap: 12px;
            margin-bottom: 12px;
        }
    }
    .form-group.col-md-6 {
        flex: 1;
        margin-bottom: 0;
    }
    .shipping-options-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 20px;
    }
    @media (max-width: 576px) {
        .shipping-options-grid {
            grid-template-columns: 1fr;
        }
    }
    .shipping-option-card {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 16px;
        border-radius: 10px;
        border: 1.5px solid var(--glass-border);
        cursor: pointer;
        transition: all 0.2s ease;
        background: transparent;
    }
    .shipping-option-card:has(input:checked) {
        border-color: #2563eb;
        background: rgba(37, 99, 235, 0.05);
    }
    .shipping-option-card input[type="radio"] {
        accent-color: #2563eb;
        margin-top: 4px;
        cursor: pointer;
    }
    .shipping-option-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-main);
    }
    .shipping-option-desc {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 4px;
    }
    .address-box-wrapper {
        border: 1px dashed var(--glass-border);
        border-radius: 8px;
        padding: 16px;
        background: rgba(255,255,255,0.01);
    }
    .address-box-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-main);
    }
    .address-box-desc {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 4px;
        margin-bottom: 12px;
    }
    .btn-address-link {
        background: none;
        border: none;
        color: #2563eb;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0;
        transition: color 0.15s;
    }
    .btn-address-link:hover {
        color: #1d4ed8;
        text-decoration: underline;
    }
    .pickup-store-card {
        border: 1px solid var(--glass-border);
        border-radius: 8px;
        padding: 16px;
        background: rgba(255,255,255,0.02);
    }
    .pickup-store-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text-main);
    }
    .pickup-store-address {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 6px;
        line-height: 1.5;
    }
    .pickup-store-info {
        font-size: 13px;
        color: #10b981;
        margin-top: 12px;
        border-top: 1px solid var(--glass-border);
        padding-top: 10px;
    }
    .summary-items-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        max-height: 240px;
        overflow-y: auto;
        padding-right: 4px;
    }
    .summary-item-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
    }
    .summary-item-name {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-main);
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .summary-item-meta {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 2px;
    }
    .summary-item-price {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-main);
        white-space: nowrap;
    }
    .payment-card-panel {
        padding: 24px;
        border-radius: 12px;
    }
    .payment-accordion-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        background: var(--bg-surface-alt);
        border: 1px solid var(--glass-border);
        border-radius: 8px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        user-select: none;
    }
    .bank-option-card {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border: 1px solid var(--glass-border);
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        background: transparent;
    }
    .bank-option-card:has(input:checked) {
        border-color: #2563eb;
        background: rgba(37, 99, 235, 0.04);
    }
    .bank-option-card input[type="radio"] {
        accent-color: #2563eb;
        cursor: pointer;
    }
    .bank-option-logo-wrapper {
        width: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .bank-option-info {
        flex: 1;
    }
    .bank-option-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-main);
    }
    .bank-option-desc {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 2px;
    }
    /* Location Autocomplete */
    .loc-autocomplete-dropdown {
        position: absolute;
        left: 0;
        right: 0;
        top: 100%;
        z-index: 100;
        background: var(--bg-surface);
        border: 1px solid var(--glass-border);
        border-radius: 0 0 8px 8px;
        box-shadow: var(--shadow-lg);
        max-height: 220px;
        overflow-y: auto;
    }
    .loc-autocomplete-item {
        padding: 10px 14px;
        cursor: pointer;
        font-size: 13px;
        color: var(--text-main);
        border-bottom: 1px solid var(--glass-border);
        transition: background 0.12s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .loc-autocomplete-item:last-child {
        border-bottom: none;
    }
    .loc-autocomplete-item:hover {
        background: var(--primary-light);
    }
    .loc-autocomplete-item .loc-item-icon {
        color: var(--primary);
        flex-shrink: 0;
        width: 16px;
        height: 16px;
    }
    .loc-autocomplete-item .loc-item-main {
        font-weight: 600;
    }
    .loc-autocomplete-item .loc-item-sub {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 1px;
    }
    .loc-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--primary-light);
        color: var(--primary);
        font-size: 12px;
        font-weight: 600;
        padding: 5px 10px;
        border-radius: 6px;
        border: 1px solid rgba(37, 99, 235, 0.15);
    }
    
    /* Custom Dropdown Styling */
    .custom-select-wrapper {
        position: relative;
        user-select: none;
        width: 100%;
        margin-top: 8px;
    }
    .custom-select-trigger {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        border: 1px solid var(--glass-border);
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.02);
        cursor: pointer;
        transition: all 0.2s;
    }
    .custom-select-trigger:hover {
        border-color: #2563eb;
        background: rgba(37, 99, 235, 0.02);
    }
    .custom-select-trigger-content {
        display: flex;
        align-items: center;
    }
    .custom-select-options {
        position: absolute;
        top: 105%;
        left: 0;
        right: 0;
        background: var(--bg-surface);
        border: 1px solid var(--glass-border);
        border-radius: 8px;
        box-shadow: var(--shadow-lg);
        z-index: 99;
        display: none;
        flex-direction: column;
        overflow: hidden;
    }
    .custom-select-options.show {
        display: flex;
        animation: fadeIn 0.15s ease-out;
    }
    .custom-select-option {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        cursor: pointer;
        transition: all 0.15s;
        border-bottom: 1px solid rgba(255, 255, 255, 0.03);
    }
    .custom-select-option:last-child {
        border-bottom: none;
    }
    .custom-select-option img {
        width: 48px;
        height: auto;
        max-height: 18px;
        object-fit: contain;
        flex-shrink: 0;
    }
    .custom-select-option span {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-main);
    }
    .custom-select-option:hover {
        background: rgba(255, 255, 255, 0.05);
    }
    .custom-select-option.active {
        background: rgba(37, 99, 235, 0.08);
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* VA card variant — keep border highlight when radio inside label is checked */
    .bank-option-card-va:has(input:checked) {
        border-color: #2563eb;
        background: rgba(37, 99, 235, 0.04);
    }


        /* ==========================================================
           FINAL FIX CHECKOUT DESKTOP: COMPACT + NO CROP + NO SCROLL
           Patch ini hanya mengatur ukuran/posisi tampilan checkout.
           Logic JS, endpoint, VA dropdown, database, dan field ID tetap sama.
        ========================================================== */
        @media (min-width: 992px) {
            body {
                min-height: 100vh !important;
            }

            .page-container {
                width: min(100%, 1580px) !important;
                max-width: 1580px !important;
                min-height: calc(100vh - 92px) !important;
                padding-top: 24px !important;
                padding-bottom: 24px !important;
                display: flex !important;
                flex-direction: column !important;
            }

            .page-header {
                flex: 0 0 auto !important;
                margin: 0 0 16px 0 !important;
                padding: 0 !important;
            }

            .page-title {
                font-size: 31px !important;
                line-height: 1.1 !important;
                margin: 0 !important;
                letter-spacing: -0.035em !important;
            }

            .page-title-icon {
                width: 25px !important;
                height: 25px !important;
            }

            .page-subtitle {
                font-size: 14px !important;
                margin: 7px 0 0 0 !important;
                line-height: 1.25 !important;
            }

            #checkout-content {
                flex: 1 1 auto !important;
                min-height: 0 !important;
            }

            .checkout-layout-grid {
                display: grid !important;
                grid-template-columns: minmax(0, 1.52fr) minmax(360px, 0.72fr) !important;
                gap: 18px !important;
                align-items: start !important;
            }

            .checkout-forms-col,
            .checkout-summary-col {
                min-height: 0 !important;
            }

            .checkout-card,
            .checkout-summary-card,
            .cart-summary-panel {
                border-radius: 14px !important;
                padding: 24px !important;
                margin: 0 !important;
                box-sizing: border-box !important;
            }

            .checkout-card-title,
            .cart-summary-title {
                font-size: 14px !important;
                line-height: 1.15 !important;
                margin: 0 0 10px 0 !important;
            }

            .form-group {
                margin-bottom: 9px !important;
            }

            .form-row {
                gap: 12px !important;
                margin-bottom: 9px !important;
            }

            .form-label {
                font-size: 11.6px !important;
                line-height: 1.1 !important;
                margin-bottom: 5px !important;
                font-weight: 800 !important;
            }

            .form-control,
            select.form-control,
            input.form-control {
                height: 34px !important;
                min-height: 34px !important;
                padding: 7px 11px !important;
                border-radius: 9px !important;
                font-size: 13px !important;
                line-height: 1.2 !important;
            }

            textarea.form-control,
            #catatan-input {
                height: 42px !important;
                min-height: 42px !important;
                max-height: 42px !important;
                resize: none !important;
                overflow: hidden !important;
                padding: 8px 10px !important;
                font-size: 12.5px !important;
                line-height: 1.25 !important;
            }

            .checkout-card > div[style*="border-top"] {
                margin: 11px 0 !important;
            }

            .checkout-card > label.form-label {
                margin-bottom: 7px !important;
            }

            .shipping-options-grid {
                gap: 10px !important;
                margin-bottom: 10px !important;
            }

            .shipping-option-card {
                min-height: 52px !important;
                padding: 10px 12px !important;
                gap: 10px !important;
                border-radius: 11px !important;
            }

            .shipping-option-card input[type="radio"] {
                margin-top: 2px !important;
                transform: scale(0.92) !important;
            }

            .shipping-option-title,
            .bank-option-title {
                font-size: 13px !important;
                line-height: 1.15 !important;
            }

            .shipping-option-desc,
            .bank-option-desc {
                font-size: 10.8px !important;
                line-height: 1.25 !important;
                margin-top: 3px !important;
            }

            .address-box-wrapper {
                padding: 11px 13px !important;
                border-radius: 11px !important;
            }

            .address-box-title {
                font-size: 12.5px !important;
                line-height: 1.15 !important;
            }

            .address-box-desc {
                font-size: 10.8px !important;
                line-height: 1.25 !important;
                margin: 3px 0 8px 0 !important;
            }

            .btn-address-link {
                font-size: 11.5px !important;
                gap: 5px !important;
            }

            #shipping-online-block .form-group {
                margin-top: 9px !important;
                margin-bottom: 0 !important;
            }

            .pickup-store-card {
                padding: 11px 13px !important;
                border-radius: 11px !important;
            }

            .pickup-store-title {
                font-size: 12.5px !important;
            }

            .pickup-store-address {
                font-size: 10.8px !important;
                line-height: 1.3 !important;
                margin-top: 4px !important;
            }

            .pickup-store-info {
                font-size: 10.8px !important;
                line-height: 1.28 !important;
                margin-top: 7px !important;
                padding-top: 7px !important;
            }

            .checkout-summary-card {
                display: flex !important;
                flex-direction: column !important;
                min-height: 0 !important;
            }

            .summary-items-list {
                flex: 0 0 auto !important;
                max-height: 86px !important;
                gap: 6px !important;
                overflow: hidden !important;
                padding-right: 0 !important;
            }

            .summary-item-row {
                gap: 8px !important;
                min-height: 34px !important;
            }

            .summary-item-name {
                font-size: 12.2px !important;
                line-height: 1.2 !important;
                -webkit-line-clamp: 1 !important;
            }

            .summary-item-meta {
                font-size: 10.5px !important;
                line-height: 1.15 !important;
            }

            .summary-item-price {
                font-size: 12.2px !important;
                line-height: 1.2 !important;
            }

            .cart-summary-items {
                margin-top: 9px !important;
                padding-top: 9px !important;
            }

            .cart-summary-row {
                margin-bottom: 8px !important;
                line-height: 1.15 !important;
            }

            .cart-summary-row span,
            .cart-summary-row .text-muted {
                font-size: 11.8px !important;
            }

            #summary-total-harga {
                font-size: 16px !important;
                line-height: 1.15 !important;
            }

            .checkout-summary-card .form-group[style*="margin-top"] {
                margin-top: 9px !important;
                margin-bottom: 0 !important;
            }

            .checkout-summary-card > div[style*="border-top"] {
                margin: 12px 0 10px !important;
            }

            .payment-method-list {
                gap: 7px !important;
                min-height: 0 !important;
            }

            .compact-bank-option,
            .va-parent-label {
                min-height: 46px !important;
                padding: 8px 10px !important;
                gap: 9px !important;
                border-radius: 11px !important;
            }

            .compact-logo,
            .bank-option-logo-wrapper {
                width: 36px !important;
            }

            .compact-logo img {
                height: 14px !important;
                max-width: 34px !important;
            }

            .bank-option-card input[type="radio"] {
                transform: scale(0.9) !important;
            }

            #va-dropdown-container {
                padding: 0 10px 8px 42px !important;
            }

            .custom-select-trigger {
                min-height: 33px !important;
                padding: 7px 10px !important;
                border-radius: 8px !important;
            }

            #selected-bank-logo {
                height: 14px !important;
                max-width: 42px !important;
                margin-right: 8px !important;
            }

            #selected-bank-name {
                font-size: 11.8px !important;
            }

            .custom-select-option {
                padding: 8px 10px !important;
                gap: 10px !important;
            }

            .custom-select-option img {
                width: 38px !important;
                max-height: 15px !important;
            }

            .custom-select-option span {
                font-size: 11.8px !important;
            }

            .checkout-main-btn {
                height: 36px !important;
                min-height: 36px !important;
                padding: 0 14px !important;
                margin-top: 10px !important;
                border-radius: 10px !important;
                font-size: 13px !important;
                font-weight: 800 !important;
                flex: 0 0 auto !important;
            }

            .modal-overlay {
                overflow: hidden !important;
            }

            .address-modal-panel {
                max-height: 88vh !important;
                overflow: hidden !important;
                padding: 18px !important;
            }
        }

        @media (min-width: 1200px) and (max-height: 760px) {
            .page-container {
                padding-top: 12px !important;
            }

            .page-title {
                font-size: 27px !important;
            }

            .page-subtitle {
                font-size: 12.8px !important;
                margin-top: 5px !important;
            }

            .checkout-layout-grid {
                gap: 14px !important;
                grid-template-columns: minmax(0, 1.62fr) minmax(340px, 0.68fr) !important;
            }

            .checkout-card,
            .checkout-summary-card,
            .cart-summary-panel {
                padding: 12px 15px !important;
            }

            .form-control,
            select.form-control,
            input.form-control {
                height: 31px !important;
                min-height: 31px !important;
                font-size: 12px !important;
            }

            .shipping-option-card {
                min-height: 46px !important;
                padding: 8px 10px !important;
            }

            .address-box-wrapper {
                padding: 9px 11px !important;
            }

            .summary-items-list {
                max-height: 72px !important;
            }

            textarea.form-control,
            #catatan-input {
                height: 36px !important;
                min-height: 36px !important;
                max-height: 36px !important;
            }

            .compact-bank-option,
            .va-parent-label {
                min-height: 40px !important;
                padding: 7px 10px !important;
            }

            .checkout-main-btn {
                height: 33px !important;
                min-height: 33px !important;
                margin-top: 8px !important;
            }
        }



    /* ==========================================================
       ULTRA FINAL CHECKOUT: 1 HALAMAN DESKTOP + VA DROPDOWN KE ATAS
       Hanya mengubah ukuran tampilan. Logic checkout, endpoint, data, dan JS lama tetap sama.
    ========================================================== */
    @media (min-width: 992px) {
        body:has(#checkout-content) .container.page-container,
        body:has(#checkout-content) .page-container {
            width: min(100%, 1500px) !important;
            max-width: 1500px !important;
            padding: 10px 20px 8px !important;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            box-sizing: border-box !important;
        }

        body:has(#checkout-content) .page-header {
            margin: 0 0 12px 0 !important;
            padding: 0 !important;
        }

        body:has(#checkout-content) .page-title {
            font-size: 25px !important;
            line-height: 1.05 !important;
            margin: 0 !important;
        }

        body:has(#checkout-content) .page-title-icon {
            width: 21px !important;
            height: 21px !important;
        }

        body:has(#checkout-content) .page-subtitle {
            font-size: 12.3px !important;
            line-height: 1.18 !important;
            margin: 5px 0 0 0 !important;
        }

        body:has(#checkout-content) #checkout-content {
            display: flex !important;
        }

        body:has(#checkout-content) .checkout-layout-grid {
            width: 100% !important;
            display: grid !important;
            grid-template-columns: minmax(0, 1.48fr) minmax(330px, 0.62fr) !important;
            gap: 14px !important;
            align-items: start !important;
        }

        body:has(#checkout-content) .checkout-card,
        body:has(#checkout-content) .cart-summary-panel,
        body:has(#checkout-content) .checkout-summary-card {
            padding: 18px 22px !important;
            border-radius: 13px !important;
            margin: 0 !important;
            box-sizing: border-box !important;
        }

        body:has(#checkout-content) .checkout-card::-webkit-scrollbar,
        body:has(#checkout-content) .cart-summary-panel::-webkit-scrollbar,
        body:has(#checkout-content) .checkout-summary-card::-webkit-scrollbar {
            width: 5px !important;
            height: 5px !important;
        }
        body:has(#checkout-content) .checkout-card::-webkit-scrollbar-track,
        body:has(#checkout-content) .cart-summary-panel::-webkit-scrollbar-track,
        body:has(#checkout-content) .checkout-summary-card::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.03) !important;
            border-radius: 10px !important;
        }
        body:has(#checkout-content) .checkout-card::-webkit-scrollbar-thumb,
        body:has(#checkout-content) .cart-summary-panel::-webkit-scrollbar-thumb,
        body:has(#checkout-content) .checkout-summary-card::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.12) !important;
            border-radius: 10px !important;
        }
        body:has(#checkout-content) .checkout-card::-webkit-scrollbar-thumb:hover,
        body:has(#checkout-content) .cart-summary-panel::-webkit-scrollbar-thumb:hover,
        body:has(#checkout-content) .checkout-summary-card::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.22) !important;
        }

        body:has(#checkout-content) .checkout-card-title,
        body:has(#checkout-content) .cart-summary-title {
            font-size: 13px !important;
            line-height: 1.08 !important;
            margin: 0 0 7px 0 !important;
        }

        body:has(#checkout-content) .form-group {
            margin-bottom: 6px !important;
        }

        body:has(#checkout-content) .form-row {
            gap: 9px !important;
            margin-bottom: 6px !important;
        }

        body:has(#checkout-content) .form-label,
        body:has(#checkout-content) label.form-label {
            font-size: 10.8px !important;
            line-height: 1.05 !important;
            margin-bottom: 4px !important;
        }

        body:has(#checkout-content) .form-control,
        body:has(#checkout-content) select.form-control,
        body:has(#checkout-content) input.form-control {
            height: 29px !important;
            min-height: 29px !important;
            padding: 5px 9px !important;
            border-radius: 8px !important;
            font-size: 11.8px !important;
            line-height: 1.1 !important;
        }

        body:has(#checkout-content) textarea.form-control,
        body:has(#checkout-content) #catatan-input {
            height: 52px !important;
            min-height: 52px !important;
            max-height: 52px !important;
            padding: 6px 9px !important;
            font-size: 11px !important;
            line-height: 1.25 !important;
            resize: none !important;
            overflow-y: auto !important;
        }

        body:has(#checkout-content) .checkout-card > div[style*="border-top"] {
            margin: 8px 0 !important;
        }

        body:has(#checkout-content) .checkout-card > label.form-label {
            margin-bottom: 5px !important;
        }

        body:has(#checkout-content) .shipping-options-grid {
            gap: 8px !important;
            margin-bottom: 7px !important;
        }

        body:has(#checkout-content) .shipping-option-card {
            min-height: 40px !important;
            padding: 7px 9px !important;
            gap: 8px !important;
            border-radius: 10px !important;
            align-items: center !important;
        }

        body:has(#checkout-content) .shipping-option-card input[type="radio"],
        body:has(#checkout-content) .bank-option-card input[type="radio"] {
            transform: scale(0.82) !important;
            margin-top: 0 !important;
        }

        body:has(#checkout-content) .shipping-option-title,
        body:has(#checkout-content) .bank-option-title {
            font-size: 11.8px !important;
            line-height: 1.05 !important;
        }

        body:has(#checkout-content) .shipping-option-desc,
        body:has(#checkout-content) .bank-option-desc {
            font-size: 9.8px !important;
            line-height: 1.15 !important;
            margin-top: 2px !important;
        }

        body:has(#checkout-content) .address-box-wrapper {
            padding: 8px 10px !important;
            border-radius: 10px !important;
        }

        body:has(#checkout-content) .address-box-title {
            font-size: 11.5px !important;
            line-height: 1.08 !important;
        }

        body:has(#checkout-content) .address-box-desc {
            font-size: 9.7px !important;
            line-height: 1.15 !important;
            margin: 2px 0 5px 0 !important;
        }

        body:has(#checkout-content) .btn-address-link {
            font-size: 10.5px !important;
            gap: 4px !important;
        }

        body:has(#checkout-content) #shipping-online-block .form-group {
            margin-top: 6px !important;
            margin-bottom: 0 !important;
        }

        body:has(#checkout-content) .pickup-store-card {
            padding: 8px 10px !important;
            border-radius: 10px !important;
        }

        body:has(#checkout-content) .pickup-store-title {
            font-size: 11.5px !important;
        }

        body:has(#checkout-content) .pickup-store-address,
        body:has(#checkout-content) .pickup-store-info {
            font-size: 9.8px !important;
            line-height: 1.16 !important;
            margin-top: 4px !important;
        }

        body:has(#checkout-content) .pickup-store-info {
            padding-top: 5px !important;
        }

        body:has(#checkout-content) .checkout-summary-card,
        body:has(#checkout-content) .cart-summary-panel {
            display: flex !important;
            flex-direction: column !important;
        }

        body:has(#checkout-content) .summary-items-list {
            flex: 0 0 auto !important;
            max-height: 54px !important;
            min-height: 0 !important;
            gap: 3px !important;
            overflow: hidden !important;
            padding: 0 !important;
        }

        body:has(#checkout-content) .summary-item-row {
            min-height: 24px !important;
            gap: 6px !important;
        }

        body:has(#checkout-content) .summary-item-name {
            font-size: 10.8px !important;
            line-height: 1.1 !important;
            -webkit-line-clamp: 1 !important;
        }

        body:has(#checkout-content) .summary-item-meta {
            font-size: 9.2px !important;
            line-height: 1.05 !important;
        }

        body:has(#checkout-content) .summary-item-price {
            font-size: 10.8px !important;
        }

        body:has(#checkout-content) .cart-summary-items {
            margin-top: 6px !important;
            padding-top: 6px !important;
        }

        body:has(#checkout-content) .cart-summary-row {
            margin-bottom: 5px !important;
            line-height: 1.05 !important;
        }

        body:has(#checkout-content) .cart-summary-row span,
        body:has(#checkout-content) .cart-summary-row .text-muted {
            font-size: 10.8px !important;
        }

        body:has(#checkout-content) #summary-total-harga {
            font-size: 14.5px !important;
        }

        body:has(#checkout-content) .checkout-summary-card .form-group[style*="margin-top"],
        body:has(#checkout-content) .cart-summary-panel .form-group[style*="margin-top"] {
            margin-top: 6px !important;
            margin-bottom: 0 !important;
        }

        body:has(#checkout-content) .checkout-summary-card > div[style*="border-top"],
        body:has(#checkout-content) .cart-summary-panel > div[style*="border-top"] {
            margin: 8px 0 7px !important;
        }

        body:has(#checkout-content) .payment-method-list {
            display: flex !important;
            flex-direction: column !important;
            gap: 6px !important;
            min-height: 0 !important;
            overflow: visible !important;
            position: relative !important;
        }

        body:has(#checkout-content) .bank-option-card,
        body:has(#checkout-content) .bank-option-card > label,
        body:has(#checkout-content) .va-parent-label {
            min-height: 36px !important;
            padding: 6px 8px !important;
            gap: 7px !important;
            border-radius: 10px !important;
        }

        body:has(#checkout-content) .bank-option-logo-wrapper {
            width: 34px !important;
            min-width: 34px !important;
        }

        body:has(#checkout-content) .bank-option-logo-wrapper img {
            max-width: 34px !important;
            max-height: 13px !important;
        }

        body:has(#checkout-content) .bank-option-card-va {
            overflow: visible !important;
            position: relative !important;
        }

        body:has(#checkout-content) #va-dropdown-container {
            padding: 0 8px 6px 40px !important;
            position: relative !important;
            z-index: 20 !important;
        }

        body:has(#checkout-content) .custom-select-wrapper {
            position: relative !important;
            margin-top: 0 !important;
            z-index: 30 !important;
        }

        body:has(#checkout-content) .custom-select-trigger {
            min-height: 28px !important;
            height: 28px !important;
            padding: 4px 8px !important;
            border-radius: 8px !important;
        }

        body:has(#checkout-content) #selected-bank-logo {
            height: 13px !important;
            max-width: 38px !important;
            margin-right: 7px !important;
        }

        body:has(#checkout-content) #selected-bank-name {
            font-size: 10.8px !important;
            line-height: 1.05 !important;
        }

        body:has(#checkout-content) .custom-select-options {
            top: auto !important;
            bottom: calc(100% + 6px) !important;
            max-height: none !important;
            overflow: visible !important;
            z-index: 99999 !important;
            border-radius: 10px !important;
            box-shadow: 0 16px 38px rgba(15, 23, 42, 0.16) !important;
        }

        body:has(#checkout-content) .custom-select-options.show {
            display: flex !important;
            animation: checkoutDropUp 0.14s ease-out !important;
        }

        body:has(#checkout-content) .custom-select-option {
            padding: 7px 9px !important;
            gap: 9px !important;
            min-height: 34px !important;
        }

        body:has(#checkout-content) .custom-select-option img {
            width: 34px !important;
            max-height: 14px !important;
        }

        body:has(#checkout-content) .custom-select-option span {
            font-size: 10.9px !important;
            line-height: 1.05 !important;
        }

        body:has(#checkout-content) .checkout-main-btn,
        body:has(#checkout-content) #btn-checkout {
            height: 32px !important;
            min-height: 32px !important;
            padding: 0 12px !important;
            margin-top: 7px !important;
            border-radius: 9px !important;
            font-size: 12px !important;
            font-weight: 800 !important;
            flex: 0 0 auto !important;
        }

        @keyframes checkoutDropUp {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }
    }

    @media (min-width: 992px) and (max-height: 760px) {
        body:has(#checkout-content) .container.page-container,
        body:has(#checkout-content) .page-container {
            padding-top: 18px !important;
            padding-bottom: 10px !important;
        }

        body:has(#checkout-content) .page-header {
            flex-basis: auto !important;
            margin-bottom: 6px !important;
        }

        body:has(#checkout-content) .page-title {
            font-size: 22px !important;
        }

        body:has(#checkout-content) .page-subtitle {
            font-size: 11px !important;
            margin-top: 3px !important;
        }

        body:has(#checkout-content) .checkout-layout-grid {
            grid-template-columns: minmax(0, 1.55fr) minmax(315px, 0.58fr) !important;
            gap: 12px !important;
            align-items: start !important;
        }

        body:has(#checkout-content) .checkout-card,
        body:has(#checkout-content) .cart-summary-panel,
        body:has(#checkout-content) .checkout-summary-card {
            padding: 9px 12px !important;
        }

        body:has(#checkout-content) .form-control,
        body:has(#checkout-content) select.form-control,
        body:has(#checkout-content) input.form-control {
            height: 27px !important;
            min-height: 27px !important;
            font-size: 11.2px !important;
        }

        body:has(#checkout-content) .shipping-option-card {
            min-height: 35px !important;
            padding: 6px 8px !important;
        }

        body:has(#checkout-content) .address-box-wrapper {
            padding: 7px 9px !important;
        }

        body:has(#checkout-content) .summary-items-list {
            max-height: 180px !important;
        }

        body:has(#checkout-content) textarea.form-control,
        body:has(#checkout-content) #catatan-input {
            height: 48px !important;
            min-height: 48px !important;
            max-height: 48px !important;
        }

        body:has(#checkout-content) .bank-option-card,
        body:has(#checkout-content) .bank-option-card > label,
        body:has(#checkout-content) .va-parent-label {
            min-height: 33px !important;
            padding: 5px 8px !important;
        }

        body:has(#checkout-content) .custom-select-trigger {
            height: 25px !important;
            min-height: 25px !important;
        }

        body:has(#checkout-content) .custom-select-option {
            min-height: 30px !important;
            padding: 6px 8px !important;
        }

        body:has(#checkout-content) .checkout-main-btn,
        body:has(#checkout-content) #btn-checkout {
            height: 28px !important;
            min-height: 28px !important;
            margin-top: 5px !important;
        }
    }



    /* ==========================================================
       FINAL POSITION PATCH: TURUNKAN CHECKOUT AGAR ATAS-BAWAH SEIMBANG
       Hanya mengatur posisi visual halaman checkout. Logic, endpoint,
       database, pembayaran, ekspedisi, dan dropdown VA tidak diubah.
    ========================================================== */
    /* Height locking and vertical offsets removed for natural page scrolling */


    /* ==========================================================
       PATCH FINAL: HAPUS LOADING + PESANAN ANDA TIDAK TERPOTONG
       Hanya mengatur tampilan checkout. Logic checkout, ekspedisi,
       pembayaran, VA dropdown, dan endpoint tidak diubah.
    ========================================================== */
    #loading-state,
    #loading-state .loader {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
        height: 0 !important;
        min-height: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    @media (min-width: 992px) {
        body:has(#checkout-content) #loading-state {
            display: none !important;
        }

        body:has(#checkout-content) .container.page-container,
        body:has(#checkout-content) .page-container {
            padding-top: 24px !important;
            padding-bottom: 16px !important;
        }

        body:has(#checkout-content) #checkout-content {
            transform: translateY(28px) !important;
        }

        body:has(#checkout-content) .checkout-layout-grid {
            align-items: start !important;
        }

        body:has(#checkout-content) .checkout-summary-card,
        body:has(#checkout-content) .cart-summary-panel {
            display: flex !important;
            flex-direction: column !important;
            min-height: 0 !important;
            padding-top: 10px !important;
            padding-bottom: 20px !important;
        }

        body:has(#checkout-content) .checkout-summary-col {
            /* allow natural size */
        }

        body:has(#checkout-content) .cart-summary-title {
            flex: 0 0 auto !important;
            margin-bottom: 6px !important;
        }

        body:has(#checkout-content) .summary-items-list {
            flex: 0 0 auto !important;
            max-height: 180px !important;
            min-height: 58px !important;
            overflow-y: auto !important;
            gap: 5px !important;
            padding-right: 0 !important;
        }

        body:has(#checkout-content) .summary-item-row {
            min-height: 30px !important;
            align-items: flex-start !important;
        }

        body:has(#checkout-content) .summary-item-name {
            font-size: 11.2px !important;
            line-height: 1.16 !important;
            -webkit-line-clamp: 1 !important;
        }

        body:has(#checkout-content) .summary-item-meta {
            font-size: 9.6px !important;
            line-height: 1.1 !important;
        }

        body:has(#checkout-content) .summary-item-price {
            font-size: 11.2px !important;
            line-height: 1.16 !important;
        }

        body:has(#checkout-content) .cart-summary-items {
            flex: 0 0 auto !important;
            margin-top: 7px !important;
            padding-top: 7px !important;
        }

        body:has(#checkout-content) .cart-summary-row {
            margin-bottom: 5px !important;
        }

        body:has(#checkout-content) #catatan-input {
            height: 52px !important;
            min-height: 52px !important;
            max-height: 52px !important;
            overflow-y: auto !important;
            resize: none !important;
        }

        body:has(#checkout-content) .payment-method-list {
            flex: 0 0 auto !important;
            gap: 5px !important;
            overflow: visible !important;
        }

        body:has(#checkout-content) .custom-select-options {
            top: auto !important;
            bottom: calc(100% + 5px) !important;
            max-height: none !important;
            overflow: visible !important;
        }

        body:has(#checkout-content) .checkout-main-btn,
        body:has(#checkout-content) #btn-checkout {
            flex: 0 0 auto !important;
            height: 32px !important;
            min-height: 32px !important;
            margin-top: 7px !important;
            margin-bottom: 0 !important;
        }
    }

    @media (min-width: 992px) and (max-height: 760px) {
        body:has(#checkout-content) #checkout-content {
            transform: translateY(18px) !important;
            height: calc(100% - 18px) !important;
            max-height: calc(100% - 18px) !important;
        }

        body:has(#checkout-content) .summary-items-list {
            max-height: 78px !important;
            min-height: 54px !important;
        }

        body:has(#checkout-content) .summary-item-row {
            min-height: 27px !important;
        }

        body:has(#checkout-content) #catatan-input {
            height: 48px !important;
            min-height: 48px !important;
            max-height: 48px !important;
        }
    }


    /* PATCH CHECKOUT: geser placeholder Cari Lokasi Tujuan agar tidak menabrak ikon */
    #address-modal #modal-loc-search.checkout-location-input,
    #address-modal input#modal-loc-search {
        padding-left: 52px !important;
        text-indent: 0 !important;
    }

    #address-modal #modal-loc-search::placeholder {
        color: var(--text-muted, #94a3b8) !important;
        opacity: 1 !important;
    }

    /* Compact Checkout Success Modal Override */
    #checkout-modal .modal-panel {
        width: 90% !important;
        max-width: 400px !important;
        padding: 24px !important;
        display: flex !important;
        flex-direction: column !important;
        box-sizing: border-box !important;
        margin: auto !important;
        border-radius: 16px !important;
        height: max-content !important;
        overflow: hidden !important;
    }
    #checkout-modal .checkout-success-header {
        margin-bottom: 16px !important;
    }
    #checkout-modal .checkout-success-title {
        font-size: 18px !important;
        font-weight: 800 !important;
        margin-bottom: 2px !important;
    }
    #checkout-modal .text-muted {
        font-size: 13px !important;
    }
    #checkout-modal #payment-instructions {
        margin-bottom: 18px !important;
    }
    #checkout-modal .btn {
        height: 38px !important;
        line-height: 38px !important;
        padding: 0 16px !important;
        font-size: 13px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-weight: 700 !important;
    }

    /* Upload Bukti Pembayaran Style */
    .payment-proof-upload {
        min-height: 120px;
        border: 1.5px dashed var(--glass-border, #dbe3ef);
        border-radius: 12px;
        background: #f8fbff;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
        cursor: pointer;
        color: var(--text-muted, #64748b);
        overflow: hidden;
        padding: 12px;
        text-align: center;
        transition: all 0.2s ease;
    }
    .payment-proof-upload:hover {
        border-color: var(--primary, #2563eb);
        background: #eff6ff;
    }
    .upload-main-icon {
        width: 30px;
        height: 30px;
        color: var(--primary, #2563eb);
    }
    .payment-proof-upload span {
        font-weight: 800;
        color: var(--text-main, #0f172a);
        font-size: 12.5px;
    }
    .payment-proof-upload img {
        max-width: 100%;
        max-height: 180px;
        object-fit: contain;
        border-radius: 8px;
    }
</style>
@endpush

@push('scripts')
<script>
let cartData = [];
let layananEkspedisiList = [];
let shippingMethod = 'online';
let savedAddress = null;
let shippingCost = 0;
let insuranceCost = 0;
let paymentAccordionOpen = true;
let paymentType = 'QRIS';
const inlinePaymentVaBanks = {
    BCA: { name: 'BCA Virtual Account', short: 'BCA', number: '128' + Math.floor(1000000000 + Math.random() * 9000000000), logo: '/img/bca.svg' },
    Mandiri: { name: 'Mandiri Virtual Account', short: 'Mandiri', number: '896' + Math.floor(1000000000 + Math.random() * 9000000000), logo: '/img/mandiri.svg' },
    BNI: { name: 'BNI Virtual Account', short: 'BNI', number: '988' + Math.floor(1000000000 + Math.random() * 9000000000), logo: '/img/bni.svg' },
    BRI: { name: 'BRI Virtual Account', short: 'BRI', number: '112' + Math.floor(1000000000 + Math.random() * 9000000000), logo: '/img/bri.svg' },
    CIMB: { name: 'CIMB Niaga Virtual Account', short: 'CIMB', number: '8578' + Math.floor(1000000000 + Math.random() * 9000000000), logo: '/img/cimb.svg' }
};
let currentVaNumber = '';

function handlePaymentProofPreview(input) {
    const preview = document.getElementById('payment-proof-preview');
    const icon = document.getElementById('payment-proof-icon');
    const text = document.getElementById('payment-proof-text');
    const subtext = document.getElementById('payment-proof-subtext');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            if (icon) icon.style.display = 'none';
            if (text) text.style.display = 'none';
            if (subtext) subtext.style.display = 'none';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '';
        preview.style.display = 'none';
        if (icon) icon.style.display = 'block';
        if (text) text.style.display = 'block';
        if (subtext) subtext.style.display = 'block';
    }
}

function renderInlinePaymentInstruction() {
    const wrapper = document.getElementById('payment-instruction-inline');
    if (!wrapper) return;

    if (paymentType === 'QRIS') {
        wrapper.innerHTML = `
            <div class="glass qris-container" style="text-align: center; padding: 10px; border-radius: 8px; border: 1px solid var(--glass-border); background: var(--bg-surface-alt);">
                <div class="qris-title" style="font-weight: 800; font-size: 13.5px; color: var(--text-main); letter-spacing: 0.5px;">QRIS DEFKAN COMPUTER</div>
                <div style="font-size: 10px; color: var(--text-muted); margin-top: 3px; line-height: 1.35;">Pindai kode QR menggunakan GoPay, OVO, Dana, LinkAja, atau m-Banking Anda.</div>
                <img src="/img/qris.jpg" alt="QRIS Code" class="qris-img" style="width: 130px; height: 130px; margin: 8px auto; display: block; border-radius: 8px;" onerror="this.src='https://placehold.co/130x130?text=QRIS'">
                <div class="qris-holder" style="font-size: 11px; font-weight: 700; color: var(--text-main);">NMID: ID102650634862</div>
            </div>
        `;
    } else {
        const bank = inlinePaymentVaBanks[selectedBank];
        if (!currentVaNumber) {
            currentVaNumber = bank.number;
        } else if (currentVaNumber.substring(0, 3) !== bank.number.substring(0, 3)) {
            currentVaNumber = bank.number;
        }
        
        wrapper.innerHTML = `
            <div class="glass payment-account-card" style="border-left: 4px solid var(--primary); padding: 12px; border-radius: 8px; background: var(--bg-surface-alt);">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                    <img src="${bank.logo}" alt="${bank.short}" style="height: 14px; object-fit: contain;">
                    <div class="payment-account-name" style="font-weight: 700; color: var(--text-main); font-size: 12.5px;">${bank.name}</div>
                </div>
                <div class="payment-account-holder" style="font-size: 11px; color: var(--text-muted);">a/n DEFKAN COMPUTER</div>
                <div style="font-size: 10px; color: var(--text-muted); margin-top: 2px;">Harap transfer tepat sesuai total pembayaran.</div>
                <div class="payment-account-number" style="font-family: monospace; font-size: 16px; font-weight: 800; color: var(--primary); letter-spacing: 1px; margin-top: 6px; background: rgba(37,99,235,0.06); padding: 6px; text-align: center; border-radius: 6px;">${currentVaNumber}</div>
            </div>
        `;
    }
}

// Autocomplete Location Data
const indonesianLocations = [
    { main: "Cihideung", sub: "Kec. Cihideung, Kota Tasikmalaya, Jawa Barat" },
    { main: "Cipedes", sub: "Kec. Cipedes, Kota Tasikmalaya, Jawa Barat" },
    { main: "Tawang", sub: "Kec. Tawang, Kota Tasikmalaya, Jawa Barat" },
    { main: "Indihiang", sub: "Kec. Indihiang, Kota Tasikmalaya, Jawa Barat" },
    { main: "Kawalu", sub: "Kec. Kawalu, Kota Tasikmalaya, Jawa Barat" },
    { main: "Mangkubumi", sub: "Kec. Mangkubumi, Kota Tasikmalaya, Jawa Barat" },
    { main: "Tamansari", sub: "Kec. Tamansari, Kota Tasikmalaya, Jawa Barat" },
    { main: "Cibeureum", sub: "Kec. Cibeureum, Kota Tasikmalaya, Jawa Barat" },
    { main: "Purbaratu", sub: "Kec. Purbaratu, Kota Tasikmalaya, Jawa Barat" },
    { main: "Bungursari", sub: "Kec. Bungursari, Kota Tasikmalaya, Jawa Barat" },
    { main: "Taraju", sub: "Kec. Taraju, Kab. Tasikmalaya, Jawa Barat" },
    { main: "Singaparna", sub: "Kec. Singaparna, Kab. Tasikmalaya, Jawa Barat" },
    { main: "Makarwangi", sub: "Kec. Taraju, Kab. Tasikmalaya, Jawa Barat" },
    { main: "Ciawi", sub: "Kec. Ciawi, Kab. Tasikmalaya, Jawa Barat" },
    { main: "Manonjaya", sub: "Kec. Manonjaya, Kab. Tasikmalaya, Jawa Barat" },
    { main: "Rajapolah", sub: "Kec. Rajapolah, Kab. Tasikmalaya, Jawa Barat" },
    { main: "Bandung", sub: "Kota Bandung, Jawa Barat" },
    { main: "Garut", sub: "Kab. Garut, Jawa Barat" },
    { main: "Ciamis", sub: "Kab. Ciamis, Jawa Barat" },
    { main: "Jakarta", sub: "DKI Jakarta" }
];

let selectedLocationObj = null;

document.addEventListener('DOMContentLoaded', () => {
    if (!isLoggedIn()) {
        window.location.href = '/login';
        return;
    }
    
    // Autofill Billing details from user profile - ONLY full name (Nama Lengkap)
    const u = getUser();
    if (u && u.nama_lengkap) {
        document.getElementById('billing-fullname').value = u.nama_lengkap;
    }
    
    loadCheckoutDetails();
    loadEkspedisi();
    renderInlinePaymentInstruction();
});

// Autocomplete handlers
function handleLocSearch(query) {
    const dropdown = document.getElementById('loc-autocomplete-dropdown');
    if (!query || query.trim().length < 2) {
        dropdown.style.display = 'none';
        return;
    }

    const val = query.toLowerCase().trim();
    const filtered = indonesianLocations.filter(loc => 
        loc.main.toLowerCase().includes(val) || 
        loc.sub.toLowerCase().includes(val)
    );

    if (filtered.length === 0) {
        dropdown.innerHTML = `
            <div class="loc-autocomplete-item" style="cursor: default; font-style: italic;">
                <div style="color: var(--text-muted);">Lokasi tidak ditemukan. Silakan isi manual.</div>
            </div>
        `;
        dropdown.style.display = 'block';
        return;
    }

    dropdown.innerHTML = '';
    filtered.forEach(loc => {
        const item = document.createElement('div');
        item.className = 'loc-autocomplete-item';
        item.innerHTML = `
            <i data-lucide="map-pin" class="loc-item-icon" style="width: 14px; height: 14px;"></i>
            <div style="flex: 1;">
                <span class="loc-item-main">${loc.main}</span>, 
                <span class="loc-item-sub">${loc.sub}</span>
            </div>
        `;
        item.onclick = () => selectLocation(loc);
        dropdown.appendChild(item);
    });

    lucide.createIcons();
    dropdown.style.display = 'block';
}

function selectLocation(loc) {
    selectedLocationObj = loc;
    const input = document.getElementById('modal-loc-search');
    input.value = `${loc.main}, ${loc.sub}`;
    
    // Hide dropdown
    document.getElementById('loc-autocomplete-dropdown').style.display = 'none';
    
    // Show badge
    const badge = document.getElementById('loc-selected-badge');
    const badgeText = document.getElementById('loc-badge-text');
    badgeText.textContent = `${loc.main}, ${loc.sub}`;
    badge.style.display = 'block';
    
    // Auto-fill recipient name & phone from Billing details if empty
    const recName = document.getElementById('modal-recipient-name');
    const recPhone = document.getElementById('modal-recipient-phone');
    const billName = document.getElementById('billing-fullname').value.trim();
    const billPhone = document.getElementById('billing-phone').value.trim();
    
    if (!recName.value && billName) {
        recName.value = billName;
    }
    if (!recPhone.value && billPhone) {
        recPhone.value = billPhone;
    }
}

function clearSelectedLocation() {
    selectedLocationObj = null;
    document.getElementById('modal-loc-search').value = '';
    document.getElementById('loc-selected-badge').style.display = 'none';
}

// Click outside listener for dropdown
document.addEventListener('click', (e) => {
    const dropdown = document.getElementById('loc-autocomplete-dropdown');
    const searchInput = document.getElementById('modal-loc-search');
    if (dropdown && e.target !== dropdown && e.target !== searchInput) {
        dropdown.style.display = 'none';
    }
});

async function loadCheckoutDetails() {
    const loading = document.getElementById('loading-state');
    const content = document.getElementById('checkout-content');
    if (loading) loading.style.display = 'none';

    try {
        const res = await apiFetch('/keranjang');
        const allCart = res.data || [];
        
        // Filter by selected item IDs if stored
        const selectedIdsStr = localStorage.getItem('checkout_cart_ids');
        if (selectedIdsStr) {
            const selectedIds = JSON.parse(selectedIdsStr);
            if (Array.isArray(selectedIds) && selectedIds.length > 0) {
                cartData = allCart.filter(item => selectedIds.includes(item.id));
            } else {
                cartData = allCart;
            }
        } else {
            cartData = allCart;
        }

        loading.style.display = 'none';

        if (cartData.length === 0) {
            showToast('Keranjang belanja Anda kosong atau item terpilih tidak ditemukan, mengalihkan...', 'warning');
            setTimeout(() => {
                window.location.href = '/produk';
            }, 1500);
            return;
        }

        content.style.display = 'block';
        recalcSummary();
    } catch (err) {
        loading.style.display = 'none';
        showToast(err.message, 'error');
    }
}

function recalcSummary() {
    let totalItemsPrice = 0;
    const summaryList = document.getElementById('summary-items-list');
    summaryList.innerHTML = '';

    cartData.forEach(item => {
        const p = item.produk || {};
        const harga = parseFloat(p.harga) || 0;
        totalItemsPrice += harga * item.jumlah;

        const row = document.createElement('div');
        row.className = 'summary-item-row';
        row.innerHTML = `
            <div style="flex: 1; min-width: 0;">
                <div class="summary-item-name" title="${p.nama_produk}">${p.nama_produk}</div>
                <div class="summary-item-meta">${formatRupiah(harga)} × ${item.jumlah}</div>
            </div>
            <div class="summary-item-price">${formatRupiah(harga * item.jumlah)}</div>
        `;
        summaryList.appendChild(row);
    });

    // Calculate insurance as 0.2% of total items price ONLY if shipping method is online
    if (shippingMethod === 'pickup') {
        insuranceCost = 0;
    } else {
        insuranceCost = Math.round(totalItemsPrice * 0.002);
    }
    document.getElementById('summary-insurance').textContent = formatRupiah(insuranceCost);

    // Calculate overall total — ensure all values are numbers
    const grandTotal = Number(totalItemsPrice) + Number(shippingCost) + Number(insuranceCost);
    document.getElementById('summary-total-harga').textContent = formatRupiah(grandTotal);
}

async function loadEkspedisi() {
    const select = document.getElementById('ekspedisi-select');
    try {
        const res = await apiFetch('/ekspedisi');
        layananEkspedisiList = res.data || [];
        
        const deliveryList = layananEkspedisiList.filter(e => e.kode_layanan !== 'PICKUP');
        
        select.innerHTML = `<option value="">— Pilih Layanan —</option>`;
        deliveryList.forEach(e => {
            const biayaText = e.biaya_ongkir > 0 ? ` (+${formatRupiah(e.biaya_ongkir)})` : ' (Gratis)';
            select.innerHTML += `<option value="${e.id_layanan_ekspedisi}">${e.nama_layanan}${biayaText}</option>`;
        });
    } catch {
        layananEkspedisiList = [
            { id_layanan_ekspedisi: 1, nama_layanan: 'JNE Reguler', kode_layanan: 'JNE-REG', biaya_ongkir: 15000 },
            { id_layanan_ekspedisi: 2, nama_layanan: 'JNE YES (1 Hari)', kode_layanan: 'JNE-YES', biaya_ongkir: 35000 },
            { id_layanan_ekspedisi: 3, nama_layanan: 'J&T Express', kode_layanan: 'JNT', biaya_ongkir: 12000 },
            { id_layanan_ekspedisi: 4, nama_layanan: 'SiCepat Halu', kode_layanan: 'SICEPAT', biaya_ongkir: 10000 },
            { id_layanan_ekspedisi: 5, nama_layanan: 'AnterAja', kode_layanan: 'ANTERAJA', biaya_ongkir: 11000 },
            { id_layanan_ekspedisi: 6, nama_layanan: 'Ambil di Toko (Gratis)', kode_layanan: 'PICKUP', biaya_ongkir: 0 }
        ];
        const deliveryList = layananEkspedisiList.filter(e => e.kode_layanan !== 'PICKUP');
        select.innerHTML = `<option value="">— Pilih Layanan —</option>`;
        deliveryList.forEach(e => {
            select.innerHTML += `<option value="${e.id_layanan_ekspedisi}">${e.nama_layanan} (+${formatRupiah(e.biaya_ongkir)})</option>`;
        });
    }
}

function toggleShippingMethod(method) {
    shippingMethod = method;
    const onlineBlock = document.getElementById('shipping-online-block');
    const pickupBlock = document.getElementById('shipping-pickup-block');
    
    if (method === 'online') {
        onlineBlock.style.display = 'block';
        pickupBlock.style.display = 'none';
        updateShippingCost();
    } else {
        onlineBlock.style.display = 'none';
        pickupBlock.style.display = 'block';
        shippingCost = 0;
        document.getElementById('summary-shipping-cost').textContent = 'Rp 0';
        recalcSummary();
    }
}

function updateShippingCost() {
    if (shippingMethod === 'pickup') {
        shippingCost = 0;
        document.getElementById('summary-shipping-cost').textContent = 'Rp 0';
    } else {
        const select = document.getElementById('ekspedisi-select');
        const selectedId = parseInt(select.value);
        const selectedExp = layananEkspedisiList.find(e => e.id_layanan_ekspedisi === selectedId);
        
        if (selectedExp) {
            shippingCost = parseFloat(selectedExp.biaya_ongkir) || 0;
            document.getElementById('summary-shipping-cost').textContent = formatRupiah(shippingCost);
        } else {
            shippingCost = 0;
            document.getElementById('summary-shipping-cost').textContent = 'Belum dipilih';
        }
    }
    recalcSummary();
}

function openAddressModal() {
    document.getElementById('address-modal').style.display = 'flex';
}

function closeAddressModal(e) {
    if (e === true || e.target === document.getElementById('address-modal')) {
        document.getElementById('address-modal').style.display = 'none';
    }
}

function saveShippingAddress() {
    const searchLoc = document.getElementById('modal-loc-search').value.trim();
    const addressMain = document.getElementById('modal-address-main').value.trim();
    const addressUnit = document.getElementById('modal-address-unit').value.trim();
    const phone = document.getElementById('modal-recipient-phone').value.trim();
    const name = document.getElementById('modal-recipient-name').value.trim();

    if (!addressMain) {
        showToast('Mohon isi alamat lengkap', 'error');
        return;
    }
    if (!phone) {
        showToast('Mohon isi nomor HP penerima', 'error');
        return;
    }
    if (!name) {
        showToast('Mohon isi nama penerima', 'error');
        return;
    }

    savedAddress = {
        name,
        phone,
        searchLoc,
        addressMain,
        addressUnit
    };

    const wrapper = document.querySelector('.address-box-wrapper');
    wrapper.innerHTML = `
        <div class="address-box-title" style="color: var(--primary); display: flex; align-items: center; gap: 6px;">
            <i data-lucide="map-pin" style="width: 14px; height: 14px;"></i> Alamat Pengiriman Aktif
        </div>
        <div style="font-size: 13.5px; font-weight: 700; margin-top: 8px; color: var(--text-main);">${name} (${phone})</div>
        <div class="address-box-desc" style="margin-bottom: 8px; line-height: 1.5;">
            ${addressMain}${addressUnit ? ', ' + addressUnit : ''}
            ${searchLoc ? '<br><span style="color: var(--text-subtle); font-size: 11.5px;">' + searchLoc + '</span>' : ''}
        </div>
        <button class="btn-address-link" onclick="openAddressModal()">
            <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i> Ubah alamat pengiriman
        </button>
    `;
    lucide.createIcons();
    
    closeAddressModal(true);
    showToast('Alamat pengiriman berhasil disimpan!');
}

// Dropdown state for VA Selection
let selectedBank = 'BCA';

function togglePaymentType(type) {
    paymentType = type;
    const vaDropdownContainer = document.getElementById('va-dropdown-container');
    if (type === 'VA') {
        vaDropdownContainer.style.display = 'block';
    } else {
        vaDropdownContainer.style.display = 'none';
        // Close dropdown if open
        const optionsList = document.getElementById('va-options-list');
        if (optionsList) {
            optionsList.classList.remove('show');
        }
        const chevron = document.getElementById('dropdown-chevron');
        if (chevron) {
            chevron.style.transform = 'rotate(0deg)';
        }
    }
    renderInlinePaymentInstruction();
}

function toggleVaDropdown(event) {
    event.stopPropagation();
    const optionsList = document.getElementById('va-options-list');
    const chevron = document.getElementById('dropdown-chevron');
    if (optionsList.classList.contains('show')) {
        optionsList.classList.remove('show');
        chevron.style.transform = 'rotate(0deg)';
    } else {
        optionsList.classList.add('show');
        chevron.style.transform = 'rotate(180deg)';
    }
}

function changeSelectedBank(value, name, logoUrl, event) {
    if (event) event.stopPropagation();
    
    selectedBank = value;
    
    // Update trigger content
    document.getElementById('selected-bank-logo').src = logoUrl;
    document.getElementById('selected-bank-name').textContent = name;
    
    // Update active class in options list
    const options = document.querySelectorAll('.custom-select-option');
    options.forEach(opt => {
        if (opt.getAttribute('data-value') === value) {
            opt.classList.add('active');
        } else {
            opt.classList.remove('active');
        }
    });
    
    // Close dropdown
    const optionsList = document.getElementById('va-options-list');
    optionsList.classList.remove('show');
    
    const chevron = document.getElementById('dropdown-chevron');
    chevron.style.transform = 'rotate(0deg)';

    renderInlinePaymentInstruction();
}

// Close dropdown if user clicks outside
document.addEventListener('click', (event) => {
    const optionsList = document.getElementById('va-options-list');
    const chevron = document.getElementById('dropdown-chevron');
    const trigger = document.querySelector('.custom-select-trigger');
    if (optionsList && optionsList.classList.contains('show')) {
        if (trigger && !trigger.contains(event.target) && !optionsList.contains(event.target)) {
            optionsList.classList.remove('show');
            chevron.style.transform = 'rotate(0deg)';
        }
    }
});

async function handleCheckout() {
    // 1. Validate Billing Details
    const fullNameVal = document.getElementById('billing-fullname').value.trim();
    const phone = document.getElementById('billing-phone').value.trim();
    const email = document.getElementById('billing-email').value.trim();

    if (!fullNameVal) {
        showToast('Mohon isi nama lengkap billing', 'error');
        document.getElementById('billing-fullname').focus();
        return;
    }
    if (!phone) {
        showToast('Mohon isi nomor HP billing', 'error');
        document.getElementById('billing-phone').focus();
        return;
    }
    if (!email) {
        showToast('Mohon isi email billing', 'error');
        document.getElementById('billing-email').focus();
        return;
    }

    // 2. Validate Shipping Details
    let finalAlamat = '';
    let selectedLayananId = null;

    if (shippingMethod === 'pickup') {
        const pickupExp = layananEkspedisiList.find(e => e.kode_layanan === 'PICKUP');
        if (pickupExp) {
            selectedLayananId = pickupExp.id_layanan_ekspedisi;
        } else {
            selectedLayananId = 6; 
        }
        
        finalAlamat = `AMBIL DI TOKO - DEFKAN COMPUTER (Penerima: ${fullNameVal}, HP: ${phone}, Email: ${email})`;
    } else {
        if (!savedAddress) {
            showToast('Mohon isi alamat pengiriman', 'error');
            openAddressModal();
            return;
        }
        
        const select = document.getElementById('ekspedisi-select');
        selectedLayananId = parseInt(select.value);
        if (!selectedLayananId) {
            showToast('Mohon pilih layanan ekspedisi', 'error');
            return;
        }

        finalAlamat = `${savedAddress.name} (${savedAddress.phone}) - ${savedAddress.addressMain}${savedAddress.addressUnit ? ', ' + savedAddress.addressUnit : ''} [Lokasi: ${savedAddress.searchLoc}]`;
    }

    // 3. Get Selected Payment Bank
    const paymentTypeVal = document.querySelector('input[name="payment_type"]:checked').value;
    const finalPaymentMethod = paymentTypeVal === 'QRIS' ? 'QRIS' : inlinePaymentVaBanks[selectedBank].name;

    // 4. Get optional notes
    const catatan = document.getElementById('catatan-input').value.trim();
    if (catatan) {
        finalAlamat += ` | Catatan: ${catatan}`;
    }

    const btn = document.getElementById('btn-checkout');
    const origTxt = btn.innerHTML;
    btn.innerHTML = '<div class="loader" style="width:18px;height:18px;border-width:2px;border-top-color:white;"></div> Memproses...';
    btn.disabled = true;

    try {
        const bodyPayload = {
            id_layanan_ekspedisi: selectedLayananId,
            alamat_pengiriman: finalAlamat,
            dari_keranjang: true
        };
        
        const selectedIdsStr = localStorage.getItem('checkout_cart_ids');
        if (selectedIdsStr) {
            const selectedIds = JSON.parse(selectedIdsStr);
            if (Array.isArray(selectedIds) && selectedIds.length > 0) {
                bodyPayload.cart_item_ids = selectedIds;
            }
        }

        // Create Order
        const res = await apiFetch('/pesanan', {
            method: 'POST',
            body: bodyPayload
        });
        
        localStorage.removeItem('checkout_cart_ids');

        // Show success modal
        const modal = document.getElementById('checkout-modal');
        const titleEl = modal.querySelector('.checkout-success-title');
        const descEl = modal.querySelector('.text-muted');
        const instructions = document.getElementById('payment-instructions');

        if (titleEl) titleEl.textContent = 'Pesanan Berhasil Dibuat!';
        if (descEl) descEl.textContent = 'Silakan lakukan pembayaran untuk memproses pesanan Anda.';

        let html = '';
        if (paymentTypeVal === 'QRIS') {
            html = `
                <div class="glass" style="text-align: center; padding: 16px; border-radius: 12px; border: 1px solid var(--glass-border); background: var(--bg-surface-alt);">
                    <div style="font-weight: 800; font-size: 15px; color: var(--text-main); letter-spacing: 0.5px;">QRIS DEFKAN COMPUTER</div>
                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">Pindai kode QR menggunakan GoPay, OVO, Dana, LinkAja, atau m-Banking Anda.</div>
                    <img src="/img/qris.jpg" alt="QRIS Code" style="width: 140px; height: 140px; margin: 12px auto; display: block; border-radius: 10px; border: 2px solid var(--glass-border);" onerror="this.src='https://placehold.co/140x140?text=QRIS'">
                    <div style="font-size: 13px; font-weight: 700; color: var(--text-main);">NMID: ID102650634862</div>
                    <div style="margin-top: 10px; font-size: 14px; font-weight: 800; color: var(--accent);">Total: ${formatRupiah(res.data.total_harga || 0)}</div>
                </div>
            `;
        } else {
            const bank = inlinePaymentVaBanks[selectedBank];
            const vaNum = bank.number;
            html = `
                <div class="glass" style="border-left: 4px solid var(--primary); padding: 16px; border-radius: 12px; background: var(--bg-surface-alt); text-align: left;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                        <img src="${bank.logo}" alt="${bank.short}" style="height: 20px; object-fit: contain;">
                        <div style="font-weight: 700; color: var(--text-main); font-size: 15px;">${bank.name}</div>
                    </div>
                    <div style="font-size: 13px; color: var(--text-muted);">a/n DEFKAN COMPUTER</div>
                    <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">Harap transfer tepat sesuai total pembayaran.</div>
                    <div style="font-family: monospace; font-size: 20px; font-weight: 800; color: var(--primary); letter-spacing: 2px; margin-top: 12px; background: rgba(37,99,235,0.06); padding: 12px; text-align: center; border-radius: 8px; word-break: break-all;">${vaNum}</div>
                    <div style="margin-top: 12px; font-size: 14px; font-weight: 800; color: var(--accent); text-align: center;">Total: ${formatRupiah(res.data.total_harga || 0)}</div>
                </div>
            `;
        }
        
        instructions.innerHTML = html;
        modal.style.display = 'flex';
        showToast('Pesanan berhasil dibuat!');
    } catch (err) {
        showToast(err.message, 'error');
    } finally {
        btn.innerHTML = origTxt;
        btn.disabled = false;
        lucide.createIcons();
    }
}

function closeModal() {
    document.getElementById('checkout-modal').style.display = 'none';
    window.location.href = '/pesanan';
}
</script>
@endpush
