@extends('layouts.app')

@section('title', 'Ajukan Servis Laptop — Defkan Computer')

@section('content')
    <div class="container page-container servis-payment-page">

        {{-- Header --}}
        <div class="page-header-center servis-page-header">
            <h1 class="page-title-center">Ajukan Servis Laptop Anda</h1>
            <p class="text-muted page-subtitle-center">
                Solusi perbaikan laptop profesional dengan teknisi berpengalaman. Isi data servis, lalu lanjutkan ke
                pembayaran.
            </p>
        </div>

        {{-- Auth Check Message --}}
        <div id="auth-check-msg" class="glass-panel"
            style="display:none; text-align:center; max-width:520px; margin:0 auto 40px;">
            <div style="margin-bottom:20px;">
                <i data-lucide="lock"
                    style="width:48px;height:48px;stroke-width:1.5;color:var(--text-subtle);margin:0 auto;"></i>
            </div>
            <h3 style="font-size:20px;font-weight:800;margin-bottom:12px;">Harus Masuk Terlebih Dahulu</h3>
            <p class="text-muted" style="font-size:14.5px;margin-bottom:24px;">Silakan login terlebih dahulu untuk
                mengajukan perbaikan laptop.</p>
            <a href="/login" class="btn btn-primary" style="padding:12px 32px;">Masuk Sekarang</a>
        </div>

        {{-- Step Indicator --}}
        <div id="servis-stepper" class="servis-stepper" style="display:none;">
            <div class="stepper-item active" id="step-form">
                <span>1</span>
                <p>Data Servis</p>
            </div>
            <div class="stepper-line"></div>
            <div class="stepper-item" id="step-payment">
                <span>2</span>
                <p>Pembayaran</p>
            </div>
        </div>

        {{-- Form Section --}}
        <form id="form-daftar" onsubmit="goToPaymentStep(event)" style="display:none;">
            <div class="servis-new-layout">
                <div class="glass-panel servis-form-card">
                    <h3 class="servis-section-title">
                        <i data-lucide="clipboard-list" class="icon"></i>
                        Detail Pelanggan & Perangkat
                    </h3>

                    <div class="servis-form-grid two">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap</label>
                            <div style="position:relative;">
                                <input type="text" id="f-nama" class="form-control" placeholder="Nama dari akun Anda"
                                    readonly>
                                <i data-lucide="lock" class="field-lock-icon"></i>
                            </div>
                            <small>Diambil otomatis dari akun Anda</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Nomor WhatsApp <span class="required">*</span></label>
                            <input type="tel" id="f-wa" class="form-control" placeholder="Contoh: 08123456789" required>
                            <small>Kami akan menghubungi via nomor ini</small>
                        </div>
                    </div>

                    <div class="servis-form-grid two">
                        <div class="form-group">
                            <label class="form-label">Merek Laptop <span class="required">*</span></label>
                            <select id="merek-laptop" class="form-control" required>
                                <option value="">Pilih Merek</option>
                                <option>ASUS</option>
                                <option>Acer</option>
                                <option>Lenovo</option>
                                <option>HP</option>
                                <option>MSI</option>
                                <option>Dell</option>
                                <option>Apple (MacBook)</option>
                                <option>Lainnya</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Seri Laptop <span class="required">*</span></label>
                            <input type="text" id="seri-laptop" class="form-control" placeholder="Contoh: ROG Zephyrus G14"
                                required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jenis Kerusakan <span class="required">*</span></label>
                        <select id="jenis-kerusakan" class="form-control" onchange="updateEstimationDisplay()" required>
                            <option value="">Pilih Jenis Kerusakan</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Keluhan Lengkap <span class="required">*</span></label>
                        <textarea id="deskripsi-keluhan" class="form-control" rows="5"
                            placeholder="Ceritakan detail masalah yang dialami laptop Anda..." required></textarea>
                    </div>

                </div>

                {{-- Sidebar --}}
                <div class="servis-sidebar">
                    <div class="glass-panel servis-estimate-card">
                        <h3 class="servis-side-title">
                            <i data-lucide="receipt" class="icon"></i>
                            Estimasi Biaya
                        </h3>
                        <p class="text-muted">Perkiraan biaya perbaikan berdasarkan jenis kerusakan yang dipilih.</p>

                        <div class="estimate-box">
                            <div class="estimate-label">MULAI DARI</div>
                            <div id="estimasi-harga">Rp 0</div>
                            <small>*Biaya pasti diinformasikan setelah pengecekan teknisi.</small>
                        </div>

                        <ul class="servis-benefit-list">
                            <li><i data-lucide="check-circle-2"></i> Pengecekan Gratis</li>
                            <li><i data-lucide="check-circle-2"></i> Garansi Servis 30 Hari</li>
                            <li><i data-lucide="check-circle-2"></i> Sparepart Original</li>
                        </ul>

                        <button type="submit" id="btn-submit" class="btn btn-primary servis-main-btn">
                            <span>Lanjut ke Pembayaran</span>
                            <i data-lucide="arrow-right" class="icon icon-sm"></i>
                        </button>
                    </div>

                    <div class="glass-panel servis-help-card">
                        <div class="help-icon">
                            <i data-lucide="headphones" class="icon"></i>
                        </div>
                        <div>
                            <div class="help-title">Butuh Bantuan?</div>
                            <div class="help-desc">Tim kami siap membantu Anda melalui Live Chat.</div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- Payment Section --}}
        <div id="servis-payment-section" style="display:none;">
            <div class="checkout-service-layout">
                <div class="glass-panel service-payment-card">
                    <div class="payment-card-header">
                        <button type="button" class="back-btn" onclick="backToServiceForm()">
                            <i data-lucide="arrow-left" class="icon-sm"></i>
                            Kembali
                        </button>
                        <div>
                            <h2>Pembayaran Servis</h2>
                            <p>Periksa ringkasan servis Anda, pilih metode pembayaran, lalu upload bukti pembayaran.</p>
                        </div>
                    </div>

                    <div class="payment-info-grid">
                        <div class="payment-info-card">
                            <h4>Data Pelanggan</h4>
                            <p><strong id="pay-nama">—</strong></p>
                            <p>No. WA: <span id="pay-wa">—</span></p>
                        </div>
                        <div class="payment-info-card">
                            <h4>Data Perangkat</h4>
                            <p><strong id="pay-laptop">—</strong></p>
                            <p>Kerusakan: <span id="pay-kerusakan">—</span></p>
                        </div>
                    </div>

                    <div class="order-summary-box">
                        <h3>Ringkasan Servis</h3>
                        <div class="summary-line">
                            <span id="pay-service-title">Servis Laptop</span>
                            <strong id="pay-estimasi">Rp 0</strong>
                        </div>
                        <div class="summary-note" id="pay-keluhan">—</div>

                        <div class="summary-line muted">
                            <span>Biaya Pengecekan</span>
                            <strong>Gratis</strong>
                        </div>
                        <div class="summary-line muted">
                            <span>Garansi Servis</span>
                            <strong>30 Hari</strong>
                        </div>
                        <div class="summary-total">
                            <span>Total Pembayaran</span>
                            <strong id="pay-total">Rp 0</strong>
                        </div>
                    </div>

                    <div class="payment-method-section">
                        <h3>Metode Pembayaran</h3>

                        <label class="bank-option-card">
                            <input type="radio" name="servis_payment_type" value="QRIS" checked
                                onchange="toggleServicePaymentType('QRIS')">
                            <div class="bank-option-logo-wrapper">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg" alt="QRIS">
                            </div>
                            <div class="bank-option-info">
                                <div class="bank-option-title">QRIS</div>
                                <div class="bank-option-desc">GoPay, OVO, Dana, LinkAja, m-Banking.</div>
                            </div>
                        </label>

                        <div class="bank-option-card bank-option-va-card" id="service-va-card">
                            <label class="bank-option-card-head" for="servis-payment-va">
                                <input type="radio" id="servis-payment-va" name="servis_payment_type" value="VA"
                                    onchange="toggleServicePaymentType('VA')">
                                <div class="bank-option-logo-wrapper">
                                    <i data-lucide="credit-card" class="icon"></i>
                                </div>
                                <div class="bank-option-info">
                                    <div class="bank-option-title">Virtual Account</div>
                                    <div class="bank-option-desc">Transfer via bank pilihan Anda.</div>
                                </div>
                            </label>

                            <div id="service-va-dropdown-container" class="service-va-dropdown-container"
                                style="display:none;">
                                <div class="service-va-select-wrapper" id="service-va-select-wrapper">
                                    <button type="button" class="service-va-select-trigger"
                                        onclick="toggleServiceVaDropdown(event)">
                                        <span class="service-va-selected-left">
                                            <span class="service-bank-logo-box">
                                                <img id="service-selected-bank-logo-img" class="service-bank-logo-img"
                                                    src="{{ asset('img/bca.svg') }}" alt="Logo BCA"
                                                    onerror="this.classList.add('is-error')">
                                                <span class="service-bank-logo-text"
                                                    id="service-selected-bank-logo-text">BCA</span>
                                            </span>
                                            <strong id="service-selected-bank-name">BCA Virtual Account</strong>
                                        </span>
                                        <i data-lucide="chevron-down" class="icon-sm" id="service-va-chevron"></i>
                                    </button>

                                    <div class="service-va-options-list" id="service-va-options-list">
                                        <button type="button" class="service-va-option active"
                                            onclick="changeSelectedServiceBank('BCA', event)">
                                            <span class="service-bank-logo-box">
                                                <img class="service-bank-logo-img" src="{{ asset('img/bca.svg') }}"
                                                    alt="Logo BCA" onerror="this.classList.add('is-error')">
                                                <span class="service-bank-logo-text">BCA</span>
                                            </span>
                                            <strong>BCA Virtual Account</strong>
                                        </button>
                                        <button type="button" class="service-va-option"
                                            onclick="changeSelectedServiceBank('Mandiri', event)">
                                            <span class="service-bank-logo-box bank-mandiri">
                                                <img class="service-bank-logo-img" src="{{ asset('img/mandiri.svg') }}"
                                                    alt="Logo Mandiri" onerror="this.classList.add('is-error')">
                                                <span class="service-bank-logo-text mandiri">mandiri</span>
                                            </span>
                                            <strong>Mandiri Virtual Account</strong>
                                        </button>
                                        <button type="button" class="service-va-option"
                                            onclick="changeSelectedServiceBank('BNI', event)">
                                            <span class="service-bank-logo-box bank-bni">
                                                <img class="service-bank-logo-img" src="{{ asset('img/bni.svg') }}"
                                                    alt="Logo BNI" onerror="this.classList.add('is-error')">
                                                <span class="service-bank-logo-text bni">BNI</span>
                                            </span>
                                            <strong>BNI Virtual Account</strong>
                                        </button>
                                        <button type="button" class="service-va-option"
                                            onclick="changeSelectedServiceBank('BRI', event)">
                                            <span class="service-bank-logo-box bank-bri">
                                                <img class="service-bank-logo-img" src="{{ asset('img/bri.svg') }}"
                                                    alt="Logo BRI" onerror="this.classList.add('is-error')">
                                                <span class="service-bank-logo-text bri">BRI</span>
                                            </span>
                                            <strong>BRI Virtual Account</strong>
                                        </button>
                                        <button type="button" class="service-va-option"
                                            onclick="changeSelectedServiceBank('CIMB', event)">
                                            <span class="service-bank-logo-box bank-cimb">
                                                <img class="service-bank-logo-img" src="{{ asset('img/cimb.svg') }}"
                                                    alt="Logo CIMB Niaga" onerror="this.classList.add('is-error')">
                                                <span class="service-bank-logo-text cimb">CIMB</span>
                                            </span>
                                            <strong>CIMB Niaga Virtual Account</strong>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="payment-instruction-box" class="payment-instruction-box"></div>
                    </div>

                    <form id="servis-payment-form" onsubmit="submitServisWithPayment(event)">
                        <div class="form-group">
                            <label class="form-label">Upload Bukti Pembayaran <span class="required">*</span></label>
                            <label class="payment-proof-upload" for="payment-proof-file">
                                <img id="payment-proof-preview" src="" alt="Preview Bukti Pembayaran" style="display:none;">
                                <i id="payment-proof-icon" data-lucide="upload-cloud" class="upload-main-icon"></i>
                                <span id="payment-proof-text">Klik untuk upload bukti pembayaran</span>
                                <small id="payment-proof-subtext">Maks. 5MB (JPG, PNG)</small>
                            </label>
                            <input type="file" id="payment-proof-file" accept="image/*" hidden required
                                onchange="handlePaymentProofPreview(this)">
                        </div>

                        <button type="submit" id="btn-final-submit" class="btn btn-primary servis-final-btn">
                            <span>Bayar & Kirim Servis</span>
                            <i data-lucide="send" class="icon-sm"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Success Modal --}}
        <div id="success-modal" class="modal-overlay" style="display:none;">
            <div class="glass-panel modal-panel servis-success-modal-panel">
                <div class="servis-success-icon">
                    <i data-lucide="check-circle-2" class="icon text-success"></i>
                </div>
                <h2 class="servis-success-title">Pengajuan Servis & Pembayaran Dikirim!</h2>
                <p class="text-muted" style="font-size:14px;margin-bottom:20px;">Kode servis Anda:</p>

                <div class="glass servis-success-code-box">
                    <span id="new-srv-code">SRV-XXXXXX</span>
                    <button onclick="copyServiceCode()" class="btn-copy" title="Salin Kode">
                        <i data-lucide="copy" style="width:18px;height:18px;"></i>
                    </button>
                </div>

                <p class="text-muted servis-success-workshop-desc">
                    Admin akan memverifikasi bukti pembayaran servis Anda. Gunakan kode servis ini untuk melacak pengerjaan
                    laptop.
                </p>

                <div class="success-actions">
                    <button class="btn btn-primary" onclick="goToTracking()">Lacak Status</button>
                    <button class="btn btn-outline" onclick="closeModal()">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .servis-payment-page {
            padding-bottom: 42px;
        }

        .servis-page-header {
            margin-bottom: 28px;
        }

        .servis-page-header .page-title-center {
            font-weight: 900;
            font-size: 34px;
            margin-bottom: 12px;
        }

        .servis-page-header .page-subtitle-center {
            max-width: 660px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .servis-stepper {
            max-width: 520px;
            margin: 0 auto 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .stepper-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted, #64748b);
            font-weight: 800;
            font-size: 13px;
        }

        .stepper-item span {
            width: 28px;
            height: 28px;
            border-radius: 999px;
            border: 1px solid var(--glass-border, #dbe3ef);
            background: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .stepper-item.active span {
            background: var(--primary, #2563eb);
            color: #ffffff;
            border-color: var(--primary, #2563eb);
        }

        .stepper-item.active {
            color: var(--primary, #2563eb);
        }

        .stepper-line {
            width: 80px;
            height: 2px;
            background: var(--glass-border, #dbe3ef);
        }

        .servis-new-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 380px;
            gap: 24px;
            align-items: start;
        }

        .servis-form-card,
        .service-payment-card {
            padding: 30px;
            border-radius: 16px;
        }

        .servis-section-title,
        .servis-side-title {
            font-size: 18px;
            font-weight: 900;
            color: var(--text-main, #0f172a);
            margin: 0 0 22px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .servis-section-title {
            color: var(--primary, #2563eb);
            padding-bottom: 14px;
            border-bottom: 1px solid var(--glass-border, #e5eaf3);
            width: 100%;
        }

        .servis-form-grid.two {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .form-group small {
            display: block;
            color: var(--text-subtle, #94a3b8);
            font-size: 11.5px;
            margin-top: 6px;
        }

        .required {
            color: var(--danger, #ef4444);
        }

        .field-lock-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 15px;
            height: 15px;
            color: var(--text-subtle, #94a3b8);
        }

        #f-nama {
            background: var(--bg-surface-alt, #f8fafc);
            padding-right: 38px;
        }

        .servis-sidebar {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .servis-estimate-card {
            padding: 28px;
            border-radius: 16px;
        }

        .servis-estimate-card p {
            font-size: 13.5px;
            margin-bottom: 18px;
            line-height: 1.55;
        }

        .estimate-box {
            background: rgba(37, 99, 235, 0.08);
            border: 1px solid rgba(37, 99, 235, 0.12);
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 22px;
        }

        .estimate-label {
            font-size: 11px;
            font-weight: 900;
            color: var(--text-muted, #64748b);
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        #estimasi-harga {
            font-size: 26px;
            font-weight: 900;
            color: #1D4ED8;
            font-family: 'Outfit', sans-serif;
        }

        .estimate-box small {
            display: block;
            color: var(--text-subtle, #94a3b8);
            font-size: 11px;
            margin-top: 7px;
            line-height: 1.35;
        }

        .servis-benefit-list {
            list-style: none;
            padding: 0;
            margin: 0 0 22px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .servis-benefit-list li {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13.5px;
            color: var(--text-main, #0f172a);
            font-weight: 700;
        }

        .servis-benefit-list svg {
            width: 18px;
            height: 18px;
            color: #10b981;
        }

        .servis-main-btn,
        .servis-final-btn {
            width: 100%;
            padding: 14px 16px;
            font-size: 15px;
            border-radius: 10px;
            justify-content: space-between;
        }

        .servis-help-card {
            padding: 20px;
            border-radius: 16px;
            display: flex;
            gap: 14px;
            align-items: center;
        }

        .help-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #e0ecff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1D4ED8;
            flex-shrink: 0;
        }

        .help-title {
            font-size: 14px;
            font-weight: 900;
            margin-bottom: 4px;
        }

        .help-desc {
            font-size: 12.5px;
            color: var(--text-muted, #64748b);
            line-height: 1.4;
        }

        .servis-upload-box,
        .payment-proof-upload {
            min-height: 150px;
            border: 1.5px dashed var(--glass-border, #dbe3ef);
            border-radius: 14px;
            background: #f8fbff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            color: var(--text-muted, #64748b);
            overflow: hidden;
            padding: 16px;
            text-align: center;
        }

        .servis-upload-box:hover,
        .payment-proof-upload:hover {
            border-color: var(--primary, #2563eb);
            background: #eff6ff;
        }

        .upload-main-icon {
            width: 34px;
            height: 34px;
            color: var(--primary, #2563eb);
        }

        .servis-upload-box span,
        .payment-proof-upload span {
            font-weight: 800;
            color: var(--text-main, #0f172a);
            font-size: 13px;
        }

        .servis-upload-box img,
        .payment-proof-upload img {
            max-width: 100%;
            max-height: 210px;
            object-fit: contain;
            border-radius: 10px;
        }

        .checkout-service-layout {
            max-width: 840px;
            margin: 0 auto;
        }

        .payment-card-header {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            border-bottom: 1px solid var(--glass-border, #e5eaf3);
            padding-bottom: 18px;
            margin-bottom: 20px;
        }

        .payment-card-header h2 {
            margin: 0 0 4px;
            font-size: 22px;
            font-weight: 900;
            color: var(--text-main, #0f172a);
        }

        .payment-card-header p {
            margin: 0;
            color: var(--text-muted, #64748b);
            font-size: 13px;
            line-height: 1.45;
        }

        .back-btn {
            height: 34px;
            padding: 0 12px;
            border-radius: 9px;
            border: 1px solid var(--glass-border, #dbe3ef);
            background: #ffffff;
            color: var(--text-main, #334155);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
        }

        .payment-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 16px;
        }

        .payment-info-card,
        .order-summary-box {
            border: 1px solid var(--glass-border, #e5eaf3);
            border-radius: 14px;
            padding: 16px;
            background: #ffffff;
        }

        .payment-info-card h4 {
            margin: 0 0 9px;
            color: var(--text-muted, #64748b);
            font-size: 11px;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            font-weight: 900;
        }

        .payment-info-card p {
            margin: 4px 0;
            font-size: 13px;
            color: var(--text-main, #0f172a);
        }

        .order-summary-box h3,
        .payment-method-section h3 {
            margin: 0 0 14px;
            font-size: 17px;
            font-weight: 900;
            color: var(--text-main, #0f172a);
        }

        .summary-line,
        .summary-total {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 10px 0;
        }

        .summary-line strong,
        .summary-total strong {
            white-space: nowrap;
        }

        .summary-line.muted {
            color: var(--text-muted, #64748b);
            font-size: 13px;
        }

        .summary-note {
            color: var(--text-muted, #64748b);
            font-size: 13px;
            line-height: 1.45;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--glass-border, #e5eaf3);
        }

        .summary-total {
            border-top: 1px dashed var(--glass-border, #dbe3ef);
            margin-top: 8px;
            padding-top: 14px;
        }

        .summary-total span {
            color: var(--text-muted, #64748b);
            font-weight: 800;
        }

        .summary-total strong {
            color: var(--accent, #0ea5e9);
            font-size: 22px;
            font-weight: 900;
        }

        .payment-method-section {
            margin-top: 18px;
        }

        .bank-option-card {
            border: 1px solid var(--glass-border, #dbe3ef);
            border-radius: 13px;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            margin-bottom: 10px;
            transition: 0.2s;
            background: #ffffff;
        }

        .bank-option-card:has(input:checked) {
            border-color: var(--primary, #2563eb);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
        }

        .bank-option-logo-wrapper {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: #eff6ff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary, #2563eb);
            flex-shrink: 0;
        }

        .bank-option-logo-wrapper img {
            max-width: 28px;
            max-height: 20px;
            object-fit: contain;
        }

        .bank-option-title {
            font-weight: 900;
            color: var(--text-main, #0f172a);
            margin-bottom: 2px;
        }

        .bank-option-desc {
            font-size: 12px;
            color: var(--text-muted, #64748b);
        }

        .bank-option-va-card {
            display: block;
            padding: 0;
            overflow: visible;
        }

        .bank-option-va-card:has(input:checked) {
            border-color: var(--primary, #2563eb);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
        }

        .bank-option-card-head {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            cursor: pointer;
            margin: 0;
        }

        .service-va-dropdown-container {
            padding: 0 14px 13px 58px;
        }

        .service-va-select-wrapper {
            position: relative;
            max-width: 430px;
            z-index: 20;
        }

        .service-va-select-trigger {
            width: 100%;
            min-height: 45px;
            padding: 9px 12px;
            border-radius: 11px;
            border: 1px solid var(--glass-border, #dbe3ef);
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            cursor: pointer;
            font-family: inherit;
            color: var(--text-main, #0f172a);
        }

        .service-va-selected-left,
        .service-va-option {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .service-va-selected-left strong,
        .service-va-option strong {
            font-size: 13px;
            font-weight: 900;
            color: var(--text-main, #0f172a);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .service-bank-logo-box {
            width: 62px;
            height: 28px;
            border-radius: 8px;
            background: #f8fafc;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
        }

        .service-bank-logo-img {
            max-width: 54px;
            max-height: 24px;
            width: auto;
            height: auto;
            object-fit: contain;
            display: block;
        }

        .service-bank-logo-text {
            min-width: 45px;
            height: 24px;
            padding: 0 7px;
            border-radius: 7px;
            background: #eff6ff;
            color: #2563eb;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 10.5px;
            font-weight: 900;
            letter-spacing: -0.02em;
            flex-shrink: 0;
        }

        .service-bank-logo-img.is-error {
            display: none;
        }

        .service-bank-logo-img.is-error+.service-bank-logo-text {
            display: inline-flex;
        }

        .service-bank-logo-text.mandiri {
            color: #1d4ed8;
            background: #eff6ff;
            text-transform: lowercase;
        }

        .service-bank-logo-text.bni {
            color: #0891b2;
            background: #ecfeff;
        }

        .service-bank-logo-text.bri {
            color: #2563eb;
            background: #eff6ff;
        }

        .service-bank-logo-text.cimb {
            color: #9f1239;
            background: #fff1f2;
        }

        .service-va-options-list {
            display: none;
            position: absolute;
            left: 0;
            right: 0;
            top: calc(100% + 4px);
            background: #ffffff;
            border: 1px solid var(--glass-border, #dbe3ef);
            border-radius: 12px;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.14);
            overflow: hidden;
            z-index: 50;
        }

        .service-va-select-wrapper.open .service-va-options-list {
            display: block;
        }

        .service-va-select-wrapper.open #service-va-chevron {
            transform: rotate(180deg);
        }

        .service-va-option {
            width: 100%;
            min-height: 58px;
            padding: 11px 14px;
            border: 0;
            background: #ffffff;
            cursor: pointer;
            font-family: inherit;
            text-align: left;
        }

        .service-va-option:hover,
        .service-va-option.active {
            background: #f1f5f9;
        }

        .payment-instruction-box {
            margin-top: 8px;
        }

        .qris-payment-card,
        .va-payment-card {
            border: 1px solid var(--glass-border, #e5eaf3);
            background: #f8fafc;
            border-radius: 14px;
            padding: 16px;
            text-align: center;
        }

        .qris-payment-card img {
            width: 170px;
            height: 170px;
            object-fit: contain;
            border-radius: 10px;
            display: block;
            margin: 12px auto;
            background: #fff;
        }

        .payment-account-number {
            font-family: monospace;
            font-size: 22px;
            font-weight: 900;
            color: var(--primary, #2563eb);
            letter-spacing: 1px;
            background: rgba(37, 99, 235, 0.06);
            border-radius: 10px;
            padding: 10px;
            margin-top: 10px;
        }

        .servis-success-modal-panel {
            max-width: 500px;
            text-align: center;
            padding: 28px;
        }

        .servis-success-icon svg {
            width: 56px;
            height: 56px;
        }

        .servis-success-title {
            font-size: 22px;
            font-weight: 900;
            margin: 12px 0 8px;
        }

        .servis-success-code-box {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 14px;
            border-radius: 12px;
            font-weight: 900;
            color: var(--primary, #2563eb);
            font-size: 20px;
        }

        .btn-copy {
            background: rgba(37, 99, 235, 0.1);
            border: none;
            color: var(--primary, #2563eb);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px;
            border-radius: 8px;
        }

        .servis-success-workshop-desc {
            font-size: 13px;
            line-height: 1.55;
            margin: 18px 0 20px;
        }

        .success-actions {
            display: flex;
            gap: 12px;
        }

        .success-actions .btn {
            flex: 1;
        }

        @media (max-width: 900px) {
            .servis-new-layout {
                grid-template-columns: 1fr !important;
            }

            .payment-info-grid,
            .servis-form-grid.two {
                grid-template-columns: 1fr;
            }

            .payment-card-header {
                flex-direction: column;
            }

            .service-va-dropdown-container {
                padding-left: 14px;
            }

            .success-actions {
                flex-direction: column;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        let listEstimasi = [];
        let draftServisData = null;
        let selectedServicePaymentType = 'QRIS';
        let selectedServicePaymentMethod = 'QRIS';
        let selectedServiceBankCode = 'BCA';
        const serviceVaBanks = {
            BCA: { name: 'BCA Virtual Account', short: 'BCA', number: '3939001025205', logo: '{{ asset('img/bca.svg') }}' },
            Mandiri: { name: 'Mandiri Virtual Account', short: 'mandiri', number: '8870801025205', logo: '{{ asset('img/mandiri.svg') }}' },
            BNI: { name: 'BNI Virtual Account', short: 'BNI', number: '8808101025205', logo: '{{ asset('img/bni.svg') }}' },
            BRI: { name: 'BRI Virtual Account', short: 'BRI', number: '1109001025205', logo: '{{ asset('img/bri.svg') }}' },
            CIMB: { name: 'CIMB Niaga Virtual Account', short: 'CIMB', number: '8059101025205', logo: '{{ asset('img/cimb.svg') }}' }
        };

        document.addEventListener('DOMContentLoaded', () => {
            checkAuthAndSetup();
            loadEstimasiOptions();
            updateSelectedServiceBankUI();
            renderPaymentInstruction();

            document.addEventListener('click', (event) => {
                const wrapper = document.getElementById('service-va-select-wrapper');
                if (wrapper && !wrapper.contains(event.target)) {
                    wrapper.classList.remove('open');
                }
            });
        });

        function checkAuthAndSetup() {
            const authMsg = document.getElementById('auth-check-msg');
            const formDaftar = document.getElementById('form-daftar');
            const stepper = document.getElementById('servis-stepper');
            const user = getUser();

            if (isLoggedIn() && user) {
                authMsg.style.display = 'none';
                formDaftar.style.display = 'block';
                stepper.style.display = 'flex';

                document.getElementById('f-nama').value = user.nama_lengkap || user.nama || user.username || user.name || '';

                const noHp = user.no_hp || user.phone || user.telepon || '';
                const waInput = document.getElementById('f-wa');
                if (noHp && noHp !== '-') waInput.value = noHp;
            } else {
                authMsg.style.display = 'block';
                formDaftar.style.display = 'none';
                stepper.style.display = 'none';
            }
        }

        async function loadEstimasiOptions() {
            const select = document.getElementById('jenis-kerusakan');

            try {
                const res = await apiFetch('/servis/estimasi');
                listEstimasi = res.data || [];

                if (listEstimasi.length === 0) {
                    select.innerHTML = '<option value="">Tidak ada opsi estimasi tersedia</option>';
                    return;
                }

                let options = '<option value="">Pilih Jenis Kerusakan</option>';
                listEstimasi.forEach(item => {
                    options += `<option value="${escapeAttr(item.jenis_kerusakan)}">${escapeHtml(item.jenis_kerusakan)}</option>`;
                });

                select.innerHTML = options;
            } catch (err) {
                select.innerHTML = '<option value="">Gagal memuat opsi estimasi</option>';
                showToast('Gagal memuat estimasi: ' + err.message, 'error');
            }
        }

        function getSelectedEstimation() {
            const selectedVal = document.getElementById('jenis-kerusakan').value;
            if (!selectedVal) return 0;

            const matched = listEstimasi.find(item => item.jenis_kerusakan === selectedVal);
            return matched ? Number(matched.harga_estimasi || matched.estimasi_biaya || matched.harga || 0) : 0;
        }

        function updateEstimationDisplay() {
            document.getElementById('estimasi-harga').textContent = formatRupiah(getSelectedEstimation());
        }

        function collectServiceDraft() {
            const user = getUser();
            const merek = document.getElementById('merek-laptop').value.trim();
            const seri = document.getElementById('seri-laptop').value.trim();
            const jenis = document.getElementById('jenis-kerusakan').value;
            const deskripsi = document.getElementById('deskripsi-keluhan').value.trim();
            const noWa = document.getElementById('f-wa').value.trim();
            const total = getSelectedEstimation();

            return {
                user,
                nama: document.getElementById('f-nama').value.trim(),
                noWa,
                merek,
                seri,
                laptop: `${merek} ${seri}`.trim(),
                jenis,
                deskripsi,
                total
            };
        }

        function goToPaymentStep(e) {
            e.preventDefault();

            if (!isLoggedIn() || !getUser()) {
                showToast('Anda harus masuk terlebih dahulu', 'error');
                return;
            }

            const draft = collectServiceDraft();

            if (!draft.noWa) {
                showToast('Nomor WhatsApp wajib diisi', 'error');
                document.getElementById('f-wa').focus();
                return;
            }

            if (!draft.merek || !draft.seri || !draft.jenis || !draft.deskripsi) {
                showToast('Lengkapi semua data servis terlebih dahulu.', 'error');
                return;
            }

            draftServisData = draft;
            updatePaymentSummary(draft);
            document.getElementById('form-daftar').style.display = 'none';
            document.getElementById('servis-payment-section').style.display = 'block';

            document.getElementById('step-form').classList.remove('active');
            document.getElementById('step-payment').classList.add('active');

            window.scrollTo({ top: 0, behavior: 'smooth' });
            lucide.createIcons();
        }

        function backToServiceForm() {
            document.getElementById('servis-payment-section').style.display = 'none';
            document.getElementById('form-daftar').style.display = 'block';

            document.getElementById('step-payment').classList.remove('active');
            document.getElementById('step-form').classList.add('active');

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function updatePaymentSummary(draft) {
            document.getElementById('pay-nama').textContent = draft.nama || '—';
            document.getElementById('pay-wa').textContent = draft.noWa || '—';
            document.getElementById('pay-laptop').textContent = draft.laptop || '—';
            document.getElementById('pay-kerusakan').textContent = draft.jenis || '—';
            document.getElementById('pay-service-title').textContent = `Servis ${draft.laptop || 'Laptop'}`;
            document.getElementById('pay-estimasi').textContent = formatRupiah(draft.total || 0);
            document.getElementById('pay-total').textContent = formatRupiah(draft.total || 0);
            document.getElementById('pay-keluhan').textContent = draft.deskripsi || '—';
        }

        function getSelectedServiceBank() {
            return serviceVaBanks[selectedServiceBankCode] || serviceVaBanks.BCA;
        }

        function getSelectedServicePaymentMethod() {
            return selectedServicePaymentType === 'QRIS' ? 'QRIS' : getSelectedServiceBank().name;
        }

        function handleServiceBankLogoError(img) {
            if (!img) return;
            img.classList.add('is-error');
        }

        function updateSelectedServiceBankUI() {
            const bank = getSelectedServiceBank();
            const nameEl = document.getElementById('service-selected-bank-name');
            const logoTextEl = document.getElementById('service-selected-bank-logo-text');
            const logoImgEl = document.getElementById('service-selected-bank-logo-img');
            const container = document.getElementById('service-va-dropdown-container');
            const wrapper = document.getElementById('service-va-select-wrapper');

            if (nameEl) nameEl.textContent = bank.name;
            if (logoTextEl) logoTextEl.textContent = bank.short;
            if (logoImgEl) {
                logoImgEl.classList.remove('is-error');
                logoImgEl.src = bank.logo;
                logoImgEl.alt = `Logo ${bank.short}`;
            }
            if (container) container.style.display = selectedServicePaymentType === 'VA' ? 'block' : 'none';
            if (wrapper && selectedServicePaymentType !== 'VA') wrapper.classList.remove('open');

            document.querySelectorAll('.service-va-option').forEach(btn => btn.classList.remove('active'));
            const activeBtn = document.querySelector(`.service-va-option[onclick*="${selectedServiceBankCode}"]`);
            if (activeBtn) activeBtn.classList.add('active');
        }

        function toggleServicePaymentType(method) {
            selectedServicePaymentType = method === 'QRIS' ? 'QRIS' : 'VA';
            selectedServicePaymentMethod = getSelectedServicePaymentMethod();
            updateSelectedServiceBankUI();
            renderPaymentInstruction();
            lucide.createIcons();
        }

        function toggleServiceVaDropdown(event) {
            event.preventDefault();
            event.stopPropagation();

            selectedServicePaymentType = 'VA';
            const vaRadio = document.getElementById('servis-payment-va');
            if (vaRadio) vaRadio.checked = true;

            selectedServicePaymentMethod = getSelectedServicePaymentMethod();
            updateSelectedServiceBankUI();

            const wrapper = document.getElementById('service-va-select-wrapper');
            if (wrapper) wrapper.classList.toggle('open');

            renderPaymentInstruction();
            lucide.createIcons();
        }

        function changeSelectedServiceBank(code, event = null) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            selectedServiceBankCode = serviceVaBanks[code] ? code : 'BCA';
            selectedServicePaymentType = 'VA';
            const vaRadio = document.getElementById('servis-payment-va');
            if (vaRadio) vaRadio.checked = true;

            selectedServicePaymentMethod = getSelectedServicePaymentMethod();
            updateSelectedServiceBankUI();

            const wrapper = document.getElementById('service-va-select-wrapper');
            if (wrapper) wrapper.classList.remove('open');

            renderPaymentInstruction();
            lucide.createIcons();
        }

        function renderPaymentInstruction() {
            const wrapper = document.getElementById('payment-instruction-box');
            if (!wrapper) return;

            selectedServicePaymentMethod = getSelectedServicePaymentMethod();

            if (selectedServicePaymentType === 'QRIS') {
                wrapper.innerHTML = `
                    <div class="qris-payment-card">
                        <strong>QRIS DEFKAN COMPUTER</strong>
                        <p style="font-size:12px;color:#64748b;margin:5px 0 0;">Pindai kode QR menggunakan GoPay, OVO, Dana, LinkAja, atau m-Banking.</p>
                        <img src="/img/qris.jpg" alt="QRIS Code" onerror="this.src='https://placehold.co/170x170?text=QRIS'">
                        <div style="font-size:12px;font-weight:800;color:#0f172a;">NMID: ID102650634862</div>
                    </div>
                `;
            } else {
                const bank = getSelectedServiceBank();
                wrapper.innerHTML = `
                    <div class="va-payment-card">
                        <div style="display:flex;align-items:center;justify-content:center;gap:10px;margin-bottom:4px;">
                            <span class="service-bank-logo-box">
                                <img class="service-bank-logo-img" src="${escapeHtml(bank.logo)}" alt="Logo ${escapeHtml(bank.short)}" onerror="this.classList.add('is-error')">
                                <span class="service-bank-logo-text">${escapeHtml(bank.short)}</span>
                            </span>
                            <strong>${escapeHtml(bank.name)}</strong>
                        </div>
                        <p style="font-size:12px;color:#64748b;margin:5px 0 0;">Transfer tepat sesuai nominal total pembayaran.</p>
                        <div class="payment-account-number">${escapeHtml(bank.number)}</div>
                        <div style="font-size:12px;color:#64748b;margin-top:8px;">a/n DEFKAN COMPUTER</div>
                    </div>
                `;
            }
        }

        async function submitServisWithPayment(e) {
            e.preventDefault();

            const btn = document.getElementById('btn-final-submit');
            const proof = document.getElementById('payment-proof-file').files[0];

            if (!draftServisData) {
                showToast('Data servis belum lengkap. Silakan ulangi form servis.', 'error');
                backToServiceForm();
                return;
            }

            if (!proof) {
                showToast('Bukti pembayaran wajib diupload.', 'error');
                return;
            }

            if (proof.size > 5 * 1024 * 1024) {
                showToast('Ukuran bukti pembayaran terlalu besar. Maksimal 5MB.', 'error');
                return;
            }

            const origTxt = btn.innerHTML;
            btn.innerHTML = '<div class="loader" style="width:18px;height:18px;border-width:2px;border-top-color:white;"></div> Mengirim...';
            btn.disabled = true;

            try {
                const user = draftServisData.user || getUser();
                const formData = new FormData();

                formData.append('pengguna_id', user?.id_pengguna || user?.id || '');
                formData.append('merek_laptop', draftServisData.laptop);
                formData.append('nama_perangkat', draftServisData.laptop);
                formData.append('jenis_kerusakan', draftServisData.jenis);
                formData.append('deskripsi', draftServisData.deskripsi);
                formData.append('keluhan', draftServisData.deskripsi);
                formData.append('no_wa', draftServisData.noWa);
                formData.append('estimasi_biaya', draftServisData.total || 0);
                formData.append('total_biaya', draftServisData.total || 0);
                formData.append('metode_pembayaran', selectedServicePaymentMethod);
                formData.append('status_pembayaran', 'dibayar');
                formData.append('waktu_pembayaran', new Date().toISOString());
                formData.append('bukti_pembayaran', proof);

                const token = localStorage.getItem('auth_token');
                const response = await fetch('/api/servis', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const res = await response.json();

                if (!response.ok) {
                    throw new Error(res.message || 'Gagal mengirim pengajuan servis');
                }

                const newCode = res.data?.kode_servis || res.data?.kode || 'SRV-XXXXXX';
                const codeEl = document.getElementById('new-srv-code');
                if (codeEl) codeEl.textContent = newCode;

                showToast('Pengajuan servis dan bukti pembayaran berhasil dikirim!', 'success');
                resetServiceForms();

                setTimeout(() => {
                    window.location.href = '/pesanan?tab=services';
                }, 900);
            } catch (err) {
                showToast(err.message, 'error');
            } finally {
                btn.innerHTML = origTxt;
                btn.disabled = false;
                lucide.createIcons();
            }
        }

        function resetServiceForms() {
            const setValue = (id, value = '') => {
                const el = document.getElementById(id);
                if (el) el.value = value;
            };

            const setDisplay = (id, display) => {
                const el = document.getElementById(id);
                if (el) el.style.display = display;
            };

            const setSrc = (id, src = '') => {
                const el = document.getElementById(id);
                if (el) el.src = src;
            };

            const setText = (id, value) => {
                const el = document.getElementById(id);
                if (el) el.textContent = value;
            };

            setValue('merek-laptop');
            setValue('seri-laptop');
            setValue('jenis-kerusakan');
            setValue('deskripsi-keluhan');
            setValue('payment-proof-file');

            setDisplay('payment-proof-preview', 'none');
            setSrc('payment-proof-preview', '');
            setDisplay('payment-proof-icon', 'block');
            setText('payment-proof-text', 'Klik untuk upload bukti pembayaran');
            setText('payment-proof-subtext', 'Maks. 5MB (JPG, PNG)');

            selectedServicePaymentType = 'QRIS';
            selectedServiceBankCode = 'BCA';
            selectedServicePaymentMethod = 'QRIS';
            const qrisRadio = document.querySelector('input[name="servis_payment_type"][value="QRIS"]');
            if (qrisRadio) qrisRadio.checked = true;
            const vaRadio = document.getElementById('servis-payment-va');
            if (vaRadio) vaRadio.checked = false;

            draftServisData = null;
            updateSelectedServiceBankUI();
            renderPaymentInstruction();
            updateEstimationDisplay();
        }

        function handlePaymentProofPreview(input) {
            previewImageInput(input, 'payment-proof-preview', 'payment-proof-icon', 'payment-proof-text', 'payment-proof-subtext', 'Klik untuk mengubah bukti pembayaran');
        }

        function previewImageInput(input, imgId, iconId, textId, subTextId, selectedSubtext) {
            const file = input.files[0];
            if (!file) return;

            if (file.size > 5 * 1024 * 1024) {
                showToast('Ukuran foto terlalu besar (Maks 5MB)', 'error');
                input.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById(imgId).src = e.target.result;
                document.getElementById(imgId).style.display = 'block';
                document.getElementById(iconId).style.display = 'none';
                document.getElementById(textId).textContent = file.name;
                document.getElementById(subTextId).textContent = selectedSubtext;
            };
            reader.readAsDataURL(file);
        }

        function goToTracking() {
            const code = document.getElementById('new-srv-code').textContent;
            window.location.href = `/servis/track?code=${encodeURIComponent(code)}`;
        }

        function closeModal() {
            document.getElementById('success-modal').style.display = 'none';
            document.getElementById('servis-payment-section').style.display = 'none';
            document.getElementById('form-daftar').style.display = 'block';
            document.getElementById('step-payment').classList.remove('active');
            document.getElementById('step-form').classList.add('active');
        }

        function copyServiceCode() {
            const code = document.getElementById('new-srv-code').textContent;
            navigator.clipboard.writeText(code).then(() => {
                showToast('Kode servis berhasil disalin!', 'success');

                const btn = document.querySelector('.btn-copy');
                const icon = btn.querySelector('i');
                if (icon) {
                    icon.setAttribute('data-lucide', 'check');
                    btn.style.color = 'var(--success)';
                    btn.style.background = 'rgba(16,185,129,0.1)';
                    lucide.createIcons();

                    setTimeout(() => {
                        icon.setAttribute('data-lucide', 'copy');
                        btn.style.color = 'var(--primary)';
                        btn.style.background = 'rgba(37,99,235,0.1)';
                        lucide.createIcons();
                    }, 1800);
                }
            });
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function escapeAttr(value) {
            return escapeHtml(value).replaceAll('`', '&#096;');
        }
    </script>
@endpush