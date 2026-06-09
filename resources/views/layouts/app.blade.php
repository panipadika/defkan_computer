<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Defkan Computer</title>
    <meta name="description"
        content="Pusat servis dan jual beli laptop bekas terbaik dengan pelayanan cepat, transparan, dan terpercaya di kota Anda.">
    <link rel="icon" href="{{ asset('img/logo.jpg') }}" type="image/jpeg">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="https://unpkg.com/lucide@latest"></script>
    @stack('styles')
    <style>
        .cart-badge {
            position: absolute;
            top: -4px;
            right: -6px;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            font-size: 10px;
            font-weight: 800;
            padding: 2px 6px;
            border-radius: 99px;
            line-height: 1;
            min-width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #0f172a;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }

        /* ============================
           LIVE CHAT CUSTOMER SUPPORT
        ============================ */
        #chat-widget {
            position: fixed;
            right: 24px;
            bottom: 24px;
            z-index: 9998;
            cursor: grab;
        }

        .chat-toggle-btn {
            width: 62px;
            height: 62px;
            border: none;
            border-radius: 999px;
            background: linear-gradient(135deg, #2563eb 0%, #0f7adf 100%);
            box-shadow: 0 14px 32px rgba(37, 99, 235, 0.35);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.22s ease;
        }

        .chat-toggle-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 38px rgba(37, 99, 235, 0.42);
        }

        .chat-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            min-width: 20px;
            height: 20px;
            border-radius: 999px;
            background: #ef4444;
            color: #ffffff;
            font-size: 11px;
            font-weight: 900;
            align-items: center;
            justify-content: center;
            border: 2px solid #ffffff;
        }

        .chat-panel {
            position: absolute;
            right: 0;
            bottom: 78px;
            width: 360px;
            max-width: calc(100vw - 32px);
            height: 540px;
            max-height: calc(100vh - 120px);
            background: #f8f8ff;
            border-radius: 18px;
            border: 1px solid rgba(226, 232, 240, 0.95);
            box-shadow: 0 22px 60px rgba(15, 23, 42, 0.24);
            overflow: hidden;
            flex-direction: column;
            font-family: inherit;
        }

        .chat-header {
            min-height: 74px;
            background: linear-gradient(135deg, #0b57d0 0%, #0b66e4 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            flex-shrink: 0;
        }

        .chat-header-avatar {
            width: 44px;
            height: 44px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.16);
            border: 2px solid rgba(255, 255, 255, 0.28);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
            flex-shrink: 0;
            overflow: hidden;
        }

        .chat-avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .chat-header-avatar::after {
            content: '';
            position: absolute;
            width: 10px;
            height: 10px;
            right: 0;
            bottom: 2px;
            border-radius: 999px;
            background: #22c55e;
            border: 2px solid #0b57d0;
        }

        .chat-title {
            font-size: 18px;
            line-height: 1.05;
            font-weight: 900;
            letter-spacing: -0.02em;
        }

        .chat-status {
            font-size: 12px;
            margin-top: 3px;
            color: rgba(255, 255, 255, 0.88);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
        }

        .chat-status-dot {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.24);
        }

        .chat-close-btn {
            margin-left: auto;
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.14);
            color: #ffffff;
            font-size: 24px;
            line-height: 1;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s ease;
        }

        .chat-close-btn:hover {
            background: rgba(255, 255, 255, 0.22);
        }

        .chat-login-prompt {
            padding: 42px 24px;
            text-align: center;
            flex: 1;
        }

        .chat-login-icon {
            margin-bottom: 14px;
        }

        .chat-login-text {
            color: var(--text-muted, #64748b);
            font-size: 14px;
            line-height: 1.55;
            margin: 0 0 18px;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 18px 16px 12px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            background: #f7f7fd;
        }

        .chat-day-label {
            align-self: center;
            color: #94a3b8;
            font-size: 11px;
            font-weight: 800;
            margin: 2px 0 8px;
        }

        .chat-message-row {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .chat-message-row.admin {
            align-items: flex-start;
        }

        .chat-message-row.user {
            align-items: flex-end;
        }

        .chat-message-with-avatar {
            display: flex;
            align-items: flex-end;
            gap: 8px;
            max-width: 88%;
        }

        .chat-mini-avatar {
            width: 24px;
            height: 24px;
            border-radius: 999px;
            background: #334155;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .chat-bubble {
            padding: 11px 13px;
            border-radius: 14px;
            font-size: 13px;
            line-height: 1.55;
            word-break: break-word;
        }

        .chat-bubble.admin {
            background: #ffffff;
            color: #1e293b;
            border: 1px solid #e7edf5;
            border-bottom-left-radius: 5px;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
        }

        .chat-bubble.user {
            max-width: 82%;
            background: #2563eb;
            color: #ffffff;
            border-bottom-right-radius: 5px;
        }

        .chat-time {
            font-size: 10px;
            color: #94a3b8;
            padding: 0 4px;
        }

        .chat-quick-topics {
            padding: 8px 16px 12px;
            background: #f7f7fd;
            border-top: 1px solid rgba(226, 232, 240, 0.7);
        }

        .chat-quick-title {
            font-size: 12px;
            color: #64748b;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .chat-topic-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .chat-topic-btn {
            min-height: 38px;
            border-radius: 10px;
            border: 1px solid #dbe7ff;
            background: #ffffff;
            color: #2563eb;
            font-size: 13px;
            font-weight: 900;
            text-align: left;
            padding: 0 14px;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .chat-topic-btn:hover {
            background: #eff6ff;
            transform: translateY(-1px);
        }

        .chat-input-area {
            padding: 12px 16px 10px;
            background: #f7f7fd;
            border-top: 1px solid rgba(226, 232, 240, 0.75);
            flex-shrink: 0;
        }

        .chat-input-container {
            min-height: 46px;
            border-radius: 999px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 0 8px 0 16px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        }

        .chat-input {
            flex: 1;
            min-width: 0;
            border: none;
            outline: none;
            background: transparent;
            color: #1e293b;
            font-size: 13px;
            font-family: inherit;
        }

        .chat-send-btn {
            width: 36px;
            height: 36px;
            border: none;
            border-radius: 999px;
            background: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
        }

        .chat-wa-footer {
            padding: 0 16px 14px;
            background: #f7f7fd;
            flex-shrink: 0;
        }

        .chat-wa-btn {
            height: 42px;
            border-radius: 999px;
            background: #435368ff;
            color: #ffffffff;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 900;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.18);
        }



        .chat-wa-logo {
            width: 20px;
            height: 20px;
            object-fit: contain;
            flex-shrink: 0;
            display: inline-block;
        }

        .chat-wa-btn span {
            display: inline-flex;
            align-items: center;
            line-height: 1;
        }

        .chat-wa-btn:hover {
            opacity: 0.92;
        }

        @media (max-width: 520px) {
            #chat-widget {
                right: 14px;
                bottom: 14px;
            }

            .chat-panel {
                width: calc(100vw - 28px);
                height: min(540px, calc(100vh - 98px));
                right: 0;
                bottom: 74px;
            }
        }


        /* =====================================================
           FINAL FIX LIVE CHAT USER: Panel Center + Input + WA
        ===================================================== */
        #chat-widget {
            z-index: 10020 !important;
        }

        #chat-widget.chat-open .chat-toggle-btn {
            opacity: 0 !important;
            pointer-events: none !important;
            transform: scale(0.92) !important;
        }

        #chat-panel.chat-panel {
            position: fixed !important;
            left: 50% !important;
            top: 50% !important;
            right: auto !important;
            bottom: auto !important;
            transform: translate(-50%, -50%) !important;
            width: min(390px, calc(100vw - 28px)) !important;
            height: min(620px, calc(100vh - 30px)) !important;
            max-height: calc(100vh - 30px) !important;
            border-radius: 18px !important;
            display: none;
            flex-direction: column !important;
            overflow: hidden !important;
            box-shadow: 0 24px 70px rgba(15, 23, 42, 0.32) !important;
        }

        .chat-header {
            min-height: 78px !important;
            padding: 13px 16px !important;
            flex-shrink: 0 !important;
        }

        .chat-title {
            font-size: 18px !important;
        }

        .chat-status {
            font-size: 12px !important;
            line-height: 1.35 !important;
        }

        .chat-messages {
            flex: 1 1 auto !important;
            min-height: 0 !important;
            overflow-y: auto !important;
            padding: 15px 16px 10px !important;
        }

        .chat-bubble {
            font-size: 13px !important;
            line-height: 1.52 !important;
            padding: 10px 13px !important;
        }

        .chat-quick-topics {
            flex-shrink: 0 !important;
            padding: 9px 16px 10px !important;
            max-height: 176px !important;
            overflow-y: auto !important;
        }

        .chat-topic-list {
            gap: 7px !important;
        }

        .chat-topic-btn {
            min-height: 35px !important;
            border-radius: 10px !important;
            font-size: 12.5px !important;
            padding: 0 13px !important;
        }

        .chat-input-area {
            display: none;
            flex-shrink: 0 !important;
            padding: 10px 16px 9px !important;
            background: #f7f7fd !important;
        }

        .chat-input-container {
            min-height: 44px !important;
            border-radius: 999px !important;
        }

        .chat-input {
            font-size: 13px !important;
        }

        .chat-wa-footer {
            display: none;
            flex-shrink: 0 !important;
            padding: 0 16px 13px !important;
            background: #f7f7fd !important;
        }

        .chat-wa-btn {
            height: 40px !important;
            font-size: 12.5px !important;
        }

        .chat-login-prompt {
            flex: 1 1 auto !important;
        }

        @media (max-height: 640px) {
            #chat-panel.chat-panel {
                height: calc(100vh - 24px) !important;
            }

            .chat-header {
                min-height: 66px !important;
                padding: 10px 14px !important;
            }

            .chat-header-avatar {
                width: 38px !important;
                height: 38px !important;
            }

            .chat-title {
                font-size: 16px !important;
            }

            .chat-status {
                font-size: 11px !important;
            }

            .chat-messages {
                padding: 12px 14px 8px !important;
            }

            .chat-quick-topics {
                max-height: 135px !important;
                padding: 8px 14px !important;
            }

            .chat-topic-btn {
                min-height: 31px !important;
                font-size: 12px !important;
            }

            .chat-input-area {
                padding: 8px 14px !important;
            }

            .chat-wa-footer {
                padding: 0 14px 10px !important;
            }
        }

        @media (max-width: 520px) {
            #chat-panel.chat-panel {
                width: calc(100vw - 22px) !important;
                height: min(610px, calc(100vh - 22px)) !important;
                left: 50% !important;
                top: 50% !important;
                right: auto !important;
                bottom: auto !important;
                transform: translate(-50%, -50%) !important;
            }
        }



        /* ===== Quick Topic Dropdown Final ===== */
        .chat-quick-topics {
            position: relative !important;
            padding: 8px 16px 10px !important;
            background: #f7f7fd !important;
            border-top: 1px solid rgba(226, 232, 240, 0.75) !important;
            flex-shrink: 0 !important;
            z-index: 25;
        }

        .chat-quick-title {
            display: none !important;
        }

        .chat-topic-dropdown-toggle {
            width: 100%;
            height: 38px;
            border-radius: 12px;
            border: 1px solid #dbe7ff;
            background: #ffffff;
            color: #2563eb;
            font-family: inherit;
            font-size: 13px;
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 0 13px;
            cursor: pointer;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.045);
            transition: 0.2s ease;
        }

        .chat-topic-dropdown-toggle:hover {
            background: #eff6ff;
            border-color: rgba(37, 99, 235, 0.26);
        }

        .chat-topic-dropdown-toggle span {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .chat-topic-chevron {
            width: 17px;
            height: 17px;
            flex-shrink: 0;
            transition: transform 0.22s ease;
        }

        .chat-quick-topics.open .chat-topic-chevron {
            transform: rotate(180deg);
        }

        .chat-topic-list {
            position: absolute !important;
            left: 16px;
            right: 16px;
            bottom: calc(100% + 8px);
            top: auto;
            display: none !important;
            flex-direction: column !important;
            gap: 8px !important;
            padding: 10px;
            border-radius: 16px;
            background: #ffffff;
            border: 1px solid #dbe7ff;
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.16);
            max-height: 232px;
            overflow-y: auto;
            z-index: 60;
        }

        .chat-quick-topics.open .chat-topic-list {
            display: flex !important;
            animation: quickTopicPopUp 0.18s ease both;
        }

        .chat-quick-topics.open.open-down .chat-topic-list {
            top: calc(100% + 8px);
            bottom: auto;
            animation-name: quickTopicPopDown;
        }

        .chat-topic-btn {
            min-height: 38px !important;
            border-radius: 11px !important;
            border: 1px solid #dbe7ff !important;
            background: #ffffff !important;
            color: #2563eb !important;
            font-size: 13px !important;
            font-weight: 900 !important;
            text-align: left !important;
            padding: 0 13px !important;
            cursor: pointer !important;
            transition: 0.2s ease !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }

        .chat-topic-btn:hover {
            background: #eff6ff !important;
            transform: translateY(-1px) !important;
        }

        @keyframes quickTopicPopUp {
            from {
                opacity: 0;
                transform: translateY(8px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes quickTopicPopDown {
            from {
                opacity: 0;
                transform: translateY(-8px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @media (max-width: 520px) {
            .chat-topic-list {
                left: 12px;
                right: 12px;
                max-height: 210px;
            }

            .chat-topic-dropdown-toggle,
            .chat-topic-btn {
                font-size: 12.5px !important;
            }
        }


        /* ===== FIX DROPDOWN TOPIK CEPAT: tampil di atas input, tidak ketutup/terpotong ===== */
        #chat-panel.chat-panel {
            overflow: hidden !important;
        }

        #chat-quick-topics.chat-quick-topics {
            position: relative !important;
            overflow: visible !important;
            max-height: none !important;
            z-index: 5000 !important;
        }

        #chat-topic-toggle.chat-topic-dropdown-toggle {
            position: relative !important;
            z-index: 5002 !important;
        }

        #chat-topic-list.chat-topic-list {
            position: absolute !important;
            left: 16px !important;
            right: 16px !important;
            bottom: calc(100% + 10px) !important;
            top: auto !important;
            display: none !important;
            flex-direction: column !important;
            gap: 8px !important;
            padding: 10px !important;
            border-radius: 16px !important;
            background: #ffffff !important;
            border: 1px solid #dbe7ff !important;
            box-shadow: 0 18px 38px rgba(15, 23, 42, 0.22) !important;
            max-height: 235px !important;
            overflow-y: auto !important;
            z-index: 99999 !important;
            opacity: 0;
            pointer-events: none;
            transform: translateY(8px) scale(0.98);
            transition: opacity .18s ease, transform .18s ease;
        }

        #chat-quick-topics.open #chat-topic-list.chat-topic-list {
            display: flex !important;
            opacity: 1 !important;
            pointer-events: auto !important;
            transform: translateY(0) scale(1) !important;
        }

        #chat-quick-topics.open.open-down #chat-topic-list.chat-topic-list {
            top: calc(100% + 10px) !important;
            bottom: auto !important;
            transform: translateY(-8px) scale(0.98);
        }

        #chat-quick-topics.open.open-down #chat-topic-list.chat-topic-list {
            transform: translateY(0) scale(1) !important;
        }

        #chat-topic-list .chat-topic-btn {
            width: 100% !important;
            flex-shrink: 0 !important;
        }

        @media (max-width: 520px) {
            #chat-topic-list.chat-topic-list {
                left: 12px !important;
                right: 12px !important;
                max-height: 220px !important;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar" id="main-navbar">
        <div class="container">
            <a href="/" class="nav-brand">
                <img src="{{ asset('img/logo.jpg') }}" alt="Logo Defkan Computer" class="nav-brand-img">
                Defkan Computer
            </a>
            <button id="mobile-menu-toggle" class="mobile-menu-toggle" aria-label="Toggle Menu">
                <i data-lucide="menu" id="mobile-menu-icon" class="icon icon-lg"></i>
            </button>
            <div class="nav-links" id="nav-links-menu">
                <a href="/" class="nav-link">Beranda</a>
                <a href="/produk" class="nav-link">Katalog</a>
                <a href="/servis" class="nav-link">Servis</a>
                <a href="/servis/track" class="nav-link" style="display: inline-flex; align-items: center; gap: 5px;"><i
                        data-lucide="search" class="icon icon-sm"></i> Lacak Servis</a>
                <a href="/rekomendasi" class="nav-link"
                    style="display: inline-flex; align-items: center; gap: 6px;">Rekomendasi Laptop</a>
                <div id="auth-links" class="nav-auth-links">
                    <a href="/login" class="nav-link">Masuk</a>
                    <a href="/register" class="btn btn-primary btn-nav-register">Daftar</a>
                </div>

                <div id="user-links" class="nav-user-links" style="display: none;">
                    <a href="/keranjang" class="nav-link" title="Keranjang"
                        style="display: inline-flex; position: relative;">
                        <i data-lucide="shopping-cart" class="icon"></i>
                        <span id="cart-badge" class="cart-badge" style="display: none;">0</span>
                    </a>
                    <a href="/pesanan" class="nav-link" title="Pesanan Saya" style="display: inline-flex;"><i
                            data-lucide="package" class="icon"></i></a>
                    <div id="admin-link" style="display: none;">
                        <a href="/admin" class="nav-link nav-admin-link"
                            style="display: inline-flex; align-items: center; gap: 6px;"><i data-lucide="settings"
                                class="icon icon-sm"></i> Admin</a>
                    </div>
                    <span id="user-name" class="nav-user-name"></span>
                    <a href="#" onclick="handleLogout(event)" class="btn btn-outline btn-nav-logout">Keluar</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-navbar min-h-screen">
        @yield('content')
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <div class="footer-brand">
                        <img src="{{ asset('img/logo.jpg') }}" alt="Logo" class="footer-brand-img">
                        <span
                            style="font-size: 18px; font-weight: 800; color: var(--primary); letter-spacing: -0.03em;">Defkan
                            Computer</span>
                    </div>
                    <p class="footer-desc">Pusat servis laptop profesional &amp; jual beli laptop bekas berkualitas.
                        Transparan, terpercaya, dan bergaransi.</p>
                    <div class="footer-tags">
                        <span class="footer-tag" style="display: inline-flex; align-items: center; gap: 4px;"><i
                                data-lucide="map-pin" class="icon icon-sm"></i> Tasikmalaya, Jawa Barat</span>
                    </div>
                </div>
                <div>
                    <h4 class="footer-title">Layanan</h4>
                    <div class="footer-links">
                        <a href="/produk" class="footer-link">Katalog Laptop</a>
                        <a href="/servis" class="footer-link">Ajukan Servis</a>
                        <a href="/servis/track" class="footer-link">Lacak Servis</a>
                        <a href="/rekomendasi" class="footer-link">Rekomendasi Laptop</a>
                    </div>
                </div>
                <div>
                    <h4 class="footer-title">Akun</h4>
                    <div class="footer-links">
                        <a href="/login" class="footer-link">Masuk</a>
                        <a href="/pesanan" class="footer-link">Pesanan Saya</a>
                        <a href="/keranjang" class="footer-link">Keranjang</a>
                    </div>
                </div>
                <div>
                    <h4 class="footer-title">Tentang Kami</h4>
                    <div class="footer-links">
                        <p style="font-size: 13px; color: var(--text-muted); line-height: 1.7; margin-bottom: 16px;">
                            Defkan Computer adalah toko laptop bekas terbaik & terpercaya di Tasikmalaya, Jawa Barat.
                            Kami menjual laptop berkualitas dengan harga terjangkau dan bergaransi.</p>
                        <a href="https://wa.me/6285322761245?text=Halo%20Defkan%20Computer%2C%20saya%20ingin%20bertanya."
                            target="_blank" class="footer-link"
                            style="display: inline-flex; align-items: center; gap: 6px; color: var(--success); font-weight: 600;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                            </svg>
                            Hubungi Kami via WhatsApp
                        </a>
                        <p style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">085322761245</p>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="footer-copyright">&copy; {{ date('Y') }} Defkan Computer — Tasikmalaya, Jawa Barat.</p>
            </div>
        </div>
    </footer>

    <!-- ===================== -->
    <!-- FLOATING LIVE CHAT    -->
    <!-- ===================== -->
    <div id="chat-widget" style="display: none;">
        <button id="chat-toggle" class="chat-toggle-btn" title="Live Chat" type="button">
            <i data-lucide="message-square" class="icon icon-lg" style="color: #fff;"></i>
        </button>
        <div id="chat-unread-badge" class="chat-badge" style="display: none;"></div>

        <div id="chat-panel" class="chat-panel" style="display: none;">
            <div class="chat-header">
                <div class="chat-header-avatar">
                    <img src="{{ asset('img/logo.jpg') }}" alt="Admin" class="chat-avatar-img"
                        onerror="this.style.display='none'">
                </div>
                <div>
                    <div class="chat-title">Customer Support</div>
                    <div class="chat-status"><span class="chat-status-dot"></span> Online | Membalas dalam hitungan
                        menit</div>
                </div>
                <button onclick="toggleChat()" class="chat-close-btn" type="button">&times;</button>
            </div>

            <div id="chat-login-prompt" class="chat-login-prompt" style="display: none;">
                <div class="chat-login-icon">
                    <i data-lucide="lock" class="icon icon-lg"
                        style="width: 48px; height: 48px; stroke-width: 1.5; color: var(--text-subtle);"></i>
                </div>
                <p class="chat-login-text">Silakan login untuk memulai chat dengan tim kami.</p>
                <a href="/login" class="btn btn-primary" style="padding: 10px 24px;">Masuk Sekarang</a>
            </div>

            <div id="chat-messages" class="chat-messages"></div>

            <div id="chat-quick-topics" class="chat-quick-topics" style="display: none;">
                <button type="button" id="chat-topic-toggle" class="chat-topic-dropdown-toggle"
                    onclick="toggleQuickTopicDropdown(event)">
                    <span>
                        <i data-lucide="list-plus" class="icon icon-xs"></i>
                        Pilih topik bantuan cepat
                    </span>
                    <i data-lucide="chevron-up" class="chat-topic-chevron"></i>
                </button>
                <div id="chat-topic-list" class="chat-topic-list">
                    <button type="button" class="chat-topic-btn"
                        onclick="sendQuickTopic('Saya ingin tanya stok laptop yang tersedia')">
                        <i data-lucide="package-check" class="icon icon-xs"></i>
                        Tanya stok laptop
                    </button>
                    <button type="button" class="chat-topic-btn"
                        onclick="sendQuickTopic('Saya ingin tanya servis laptop')">
                        <i data-lucide="wrench" class="icon icon-xs"></i>
                        Tanya servis laptop
                    </button>
                    <button type="button" class="chat-topic-btn"
                        onclick="sendQuickTopic('Saya ingin rekomendasi laptop sesuai kebutuhan')">
                        <i data-lucide="sparkles" class="icon icon-xs"></i>
                        Tanya rekomendasi laptop
                    </button>
                    <button type="button" class="chat-topic-btn"
                        onclick="sendQuickTopic('Saya ingin tanya garansi dan pembayaran')">
                        <i data-lucide="shield-check" class="icon icon-xs"></i>
                        Tanya garansi / pembayaran
                    </button>
                </div>
            </div>

            <div id="chat-input-area" class="chat-input-area" style="display: none;">
                <div class="chat-input-container">
                    <input type="text" id="chat-input" placeholder="Ketik pesan Anda..." class="chat-input"
                        onkeydown="if(event.key==='Enter') sendChatMessage()">
                    <button onclick="sendChatMessage()" class="chat-send-btn" type="button">
                        <i data-lucide="send" class="icon" style="color: #fff; margin-left: 2px;"></i>
                    </button>
                </div>
            </div>

            <div id="chat-wa-footer" class="chat-wa-footer" style="display: none;">
                <a id="chat-wa-link"
                    href="https://wa.me/6285322761245?text=Halo%20Defkan%20Computer%2C%20saya%20ingin%20bertanya%20tentang%20produk%20atau%20servis%20laptop."
                    target="_blank" class="chat-wa-btn">
                    <img src="data:image/png;base64,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"
                        alt="WhatsApp" class="chat-wa-logo">
                    <span>Lanjutkan via WhatsApp</span>
                </a>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>

    <!-- LIVE CHAT SCRIPT -->
    <script>
        let chatRoomId = null;
        let chatPolling = null;
        let chatOpen = false;
        let lastMsgCount = 0;
        let localWelcomeRendered = false;
        let lastRenderedChatSignature = '';
        let lastChatMessagesLength = 0;

        function todayChatLabel() {
            return `Hari ini, ${new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}`;
        }

        async function toggleChat() {
            const panel = document.getElementById('chat-panel');
            const widget = document.getElementById('chat-widget');
            chatOpen = !chatOpen;
            panel.style.display = chatOpen ? 'flex' : 'none';
            if (widget) widget.classList.toggle('chat-open', chatOpen);
            const btn = document.getElementById('chat-toggle');
            btn.innerHTML = chatOpen
                ? `<i data-lucide="x" class="icon icon-lg" style="color: #fff;"></i>`
                : `<i data-lucide="message-square" class="icon icon-lg" style="color: #fff;"></i>`;
            lucide.createIcons();

            if (chatOpen) {
                document.getElementById('chat-unread-badge').style.display = 'none';
                updatePanelPosition();
                if (chatRoomId) {
                    try { await apiFetch(`/chat/messages/${chatRoomId}/read`, { method: 'PATCH' }); } catch (e) { }
                }
                initChat();
                startPolling(3000);
            } else {
                closeQuickTopicDropdown();
                startPolling(10000);
            }
        }

        function updatePanelPosition() {
            const panel = document.getElementById('chat-panel');
            if (!panel) return;

            // Panel live chat dibuat selalu berada di tengah halaman,
            // bukan menempel ke posisi tombol floating di bawah.
            panel.style.left = '50%';
            panel.style.top = '50%';
            panel.style.right = 'auto';
            panel.style.bottom = 'auto';
            panel.style.transform = 'translate(-50%, -50%)';
        }

        function setChatLoggedInUI(isReady) {
            const loginPrompt = document.getElementById('chat-login-prompt');
            const messages = document.getElementById('chat-messages');
            const inputArea = document.getElementById('chat-input-area');
            const quickTopics = document.getElementById('chat-quick-topics');
            const waFooter = document.getElementById('chat-wa-footer');

            loginPrompt.style.display = isReady ? 'none' : 'block';
            messages.style.display = isReady ? 'flex' : 'none';
            quickTopics.style.display = isReady ? 'block' : 'none';
            inputArea.style.display = isReady ? 'block' : 'none';
            waFooter.style.display = isReady ? 'block' : 'none';

            // Pastikan area input manual dan tombol WhatsApp tetap terlihat di bagian bawah panel.
            if (isReady) {
                inputArea.hidden = false;
                waFooter.hidden = false;
            }
        }

        function renderLocalWelcome() {
            const container = document.getElementById('chat-messages');
            if (!container || localWelcomeRendered) return;
            container.innerHTML = `<div class="chat-day-label">${todayChatLabel()}</div>`;
            appendChatMsg('Halo! Selamat datang di DEFKAN COMPUTER. Ada yang bisa kami bantu hari ini mengenai produk atau servis laptop Anda?', true, 'Admin');
            localWelcomeRendered = true;
        }

        async function initChat() {
            if (!isLoggedIn()) {
                setChatLoggedInUI(false);
                return;
            }

            setChatLoggedInUI(true);
            renderLocalWelcome();

            let user = getUser();
            if (!user || !user.id_pengguna) {
                try {
                    const meRes = await apiFetch('/me');
                    if (meRes && meRes.data) {
                        user = meRes.data;
                        localStorage.setItem('user', JSON.stringify(user));
                    }
                } catch (err) { console.error('Failed to load user profile in chat:', err); }
            }

            if (!user || !user.id_pengguna) return;

            if (!chatRoomId) {
                try {
                    const res = await apiFetch('/chat/rooms', {
                        method: 'POST',
                        body: { pengguna_id: user.id_pengguna }
                    });
                    chatRoomId = res.data.id;
                    loadChatMessages();
                    startPolling(3000);
                } catch (e) {
                    try {
                        const rooms = await apiFetch(`/chat/rooms/${user.id_pengguna}`);
                        if (rooms.data && rooms.data.length > 0) {
                            chatRoomId = rooms.data[0].id;
                            loadChatMessages();
                            startPolling(3000);
                        }
                    } catch (e2) { console.error('Chat init fallback failed', e2); }
                }
            } else {
                loadChatMessages();
                startPolling(3000);
            }
        }

        async function initChatSilently() {
            if (!isLoggedIn()) return;
            let user = getUser();
            if (!user || !user.id_pengguna) {
                try {
                    const meRes = await apiFetch('/me');
                    if (meRes && meRes.data) {
                        user = meRes.data;
                        localStorage.setItem('user', JSON.stringify(user));
                    }
                } catch (err) { console.error('Failed to load user profile in silent chat init:', err); }
            }
            if (!user || !user.id_pengguna) return;
            try {
                const res = await apiFetch('/chat/rooms', { method: 'POST', body: { pengguna_id: user.id_pengguna } });
                chatRoomId = res.data.id;
                loadChatMessages();
                startPolling(10000);
            } catch (e) {
                try {
                    const rooms = await apiFetch(`/chat/rooms/${user.id_pengguna}`);
                    if (rooms.data && rooms.data.length > 0) {
                        chatRoomId = rooms.data[0].id;
                        loadChatMessages();
                        startPolling(10000);
                    }
                } catch (e2) { console.error('Chat init fallback failed', e2); }
            }
        }

        function normalizeChatAdminFlag(msg = {}) {
            return msg.is_admin === true ||
                msg.is_admin === 1 ||
                msg.is_admin === '1' ||
                msg.sender === 'admin' ||
                msg.sender_type === 'admin' ||
                msg.role === 'admin' ||
                msg.from === 'admin' ||
                msg.pengirim === 'admin';
        }

        function getChatMessageText(msg = {}) {
            return msg.pesan ?? msg.message ?? msg.text ?? msg.body ?? '';
        }

        function getChatMessageTime(msg = {}) {
            const raw = msg.created_at || msg.createdAt || msg.waktu || msg.tanggal;
            if (!raw) return new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

            const date = new Date(raw);
            if (isNaN(date.getTime())) {
                return new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            }

            return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        }

        function buildChatSignature(messages = []) {
            return messages.map((msg, index) => {
                const key = msg.id || msg.id_chat || msg.id_pesan || msg.created_at || index;
                return `${key}:${normalizeChatAdminFlag(msg) ? 'A' : 'U'}:${getChatMessageText(msg)}`;
            }).join('|');
        }

        function renderChatConversation(messages = []) {
            const container = document.getElementById('chat-messages');
            if (!container) return;

            const wasAtBottom = container.scrollTop + container.clientHeight >= container.scrollHeight - 20;
            container.innerHTML = `<div class="chat-day-label">${todayChatLabel()}</div>`;

            appendChatMsg(
                'Halo! Selamat datang di DEFKAN COMPUTER. Ada yang bisa kami bantu hari ini mengenai produk atau servis laptop Anda?',
                true,
                'Admin',
                null,
                true
            );
            localWelcomeRendered = true;

            messages.forEach(msg => {
                const isAdmin = normalizeChatAdminFlag(msg);
                appendChatMsg(
                    getChatMessageText(msg),
                    isAdmin,
                    isAdmin ? 'Admin' : null,
                    getChatMessageTime(msg),
                    true
                );
            });

            if (chatOpen || wasAtBottom) {
                container.scrollTop = container.scrollHeight;
            }
        }

        async function loadChatMessages() {
            if (!chatRoomId) return;
            try {
                const res = await apiFetch(`/chat/messages/${chatRoomId}`);
                const messages = Array.isArray(res.data) ? res.data : (res.data?.data || []);
                const unreadMessages = messages.filter(msg => normalizeChatAdminFlag(msg) && !msg.is_read);
                const unreadCount = unreadMessages.length;
                const badge = document.getElementById('chat-unread-badge');

                if (badge) {
                    if (unreadCount > 0 && !chatOpen) {
                        badge.textContent = unreadCount;
                        badge.style.display = 'flex';
                    } else {
                        badge.style.display = 'none';
                    }
                }

                const signature = buildChatSignature(messages);
                const container = document.getElementById('chat-messages');
                const needsRender = signature !== lastRenderedChatSignature ||
                    lastChatMessagesLength !== messages.length ||
                    !container ||
                    container.querySelectorAll('.chat-message-row').length === 0;

                if (needsRender) {
                    renderChatConversation(messages);
                    lastRenderedChatSignature = signature;
                    lastChatMessagesLength = messages.length;
                    lastMsgCount = messages.length;
                }

                if (chatOpen && unreadCount > 0) {
                    try { await apiFetch(`/chat/messages/${chatRoomId}/read`, { method: 'PATCH' }); } catch (e) { }
                }
            } catch (e) {
                console.error('Gagal memuat pesan chat user:', e);
            }
        }

        function appendChatMsg(text, isAdmin = false, senderName = null, createdTime = null, skipScroll = false) {
            const container = document.getElementById('chat-messages');
            if (!container) return;
            const now = createdTime || new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            const el = document.createElement('div');
            el.className = `chat-message-row ${isAdmin ? 'admin' : 'user'}`;

            if (isAdmin) {
                el.innerHTML = `
                <div class="chat-message-with-avatar">
                    <span class="chat-mini-avatar"><i data-lucide="user" style="width:14px;height:14px;"></i></span>
                    <div class="chat-bubble admin">${escapeHtml(text)}</div>
                </div>
                <span class="chat-time">${senderName ? senderName + ' · ' : ''}${now}</span>
            `;
            } else {
                el.innerHTML = `
                <div class="chat-bubble user">${escapeHtml(text)}</div>
                <span class="chat-time">${now}</span>
            `;
            }
            container.appendChild(el);
            if (!skipScroll) container.scrollTop = container.scrollHeight;
            lucide.createIcons();
        }

        function escapeHtml(str) {
            return String(str ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
        }



        function closeQuickTopicDropdown() {
            const quickTopics = document.getElementById('chat-quick-topics');
            const topicList = document.getElementById('chat-topic-list');
            if (!quickTopics) return;
            quickTopics.classList.remove('open', 'open-down');
            if (topicList) topicList.style.display = 'none';
        }

        function toggleQuickTopicDropdown(event = null) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            const quickTopics = document.getElementById('chat-quick-topics');
            const topicList = document.getElementById('chat-topic-list');
            const panel = document.getElementById('chat-panel');
            if (!quickTopics || !topicList) return;

            const willOpen = !quickTopics.classList.contains('open');

            if (!willOpen) {
                closeQuickTopicDropdown();
                return;
            }

            quickTopics.classList.add('open');
            quickTopics.classList.remove('open-down');
            topicList.style.display = 'flex';

            // Default dropdown membuka dari bawah ke atas.
            // Kalau ruang atas tidak cukup, otomatis dibuka ke bawah agar tidak terpotong.
            requestAnimationFrame(() => {
                const quickRect = quickTopics.getBoundingClientRect();
                const panelRect = panel ? panel.getBoundingClientRect() : null;
                const listHeight = Math.min(topicList.scrollHeight || 235, 235);
                const availableTop = panelRect ? quickRect.top - panelRect.top : quickRect.top;

                if (availableTop < listHeight + 12) {
                    quickTopics.classList.add('open-down');
                }

                topicList.style.display = 'flex';
                lucide.createIcons();
            });
        }

        document.addEventListener('click', function (e) {
            const quickTopics = document.getElementById('chat-quick-topics');
            if (!quickTopics) return;
            if (!quickTopics.contains(e.target)) closeQuickTopicDropdown();
        });

        async function sendQuickTopic(text) {
            if (!isLoggedIn()) {
                window.location.href = '/login';
                return;
            }
            closeQuickTopicDropdown();
            const input = document.getElementById('chat-input');
            input.value = text;
            if (!chatRoomId) await initChat();
            setTimeout(() => sendChatMessage(), 100);
        }

        async function sendChatMessage() {
            const input = document.getElementById('chat-input');
            const text = input.value.trim();
            if (!text) return;

            if (!chatRoomId) {
                showToast('Menghubungkan kembali ke chat support... Silakan coba lagi sebentar.', 'warning');
                await initChat();
                return;
            }

            const user = getUser();
            if (!user || !user.id_pengguna) {
                showToast('Gagal mengirim pesan: Sesi tidak valid. Silakan login kembali.', 'error');
                return;
            }

            input.value = '';
            appendChatMsg(text, false);

            try {
                await apiFetch('/chat/messages', {
                    method: 'POST',
                    body: { room_id: chatRoomId, user_id: user.id_pengguna, pesan: text }
                });

                // Jangan menaikkan lastMsgCount secara manual.
                // Ambil ulang dari database agar balasan admin tidak terskip.
                lastRenderedChatSignature = '';
                setTimeout(loadChatMessages, 250);
            } catch (e) {
                showToast('Gagal mengirim pesan', 'error');
            }
        }

        function startPolling(interval = 4000) {
            stopPolling();
            chatPolling = setInterval(loadChatMessages, interval);
        }

        function stopPolling() {
            if (chatPolling) { clearInterval(chatPolling); chatPolling = null; }
        }

        function setupDragging() {
            const chatWidget = document.getElementById('chat-widget');
            const chatToggle = document.getElementById('chat-toggle');
            if (!chatWidget || !chatToggle) return;
            let isDragging = false;
            let startX, startY, initialLeft, initialTop;
            let hasMoved = false;

            function getEventCoords(e) {
                if (e.touches && e.touches.length > 0) return { x: e.touches[0].clientX, y: e.touches[0].clientY };
                return { x: e.clientX, y: e.clientY };
            }

            function onDragStart(e) {
                if (e.target.closest('#chat-panel')) return;
                if (!e.target.closest('#chat-toggle') && e.target !== chatWidget) return;
                isDragging = true;
                hasMoved = false;
                const coords = getEventCoords(e);
                startX = coords.x;
                startY = coords.y;
                const rect = chatWidget.getBoundingClientRect();
                initialLeft = rect.left;
                initialTop = rect.top;
                chatWidget.style.cursor = 'grabbing';
                document.addEventListener('mousemove', onDragMove, { passive: false });
                document.addEventListener('touchmove', onDragMove, { passive: false });
                document.addEventListener('mouseup', onDragEnd);
                document.addEventListener('touchend', onDragEnd);
            }

            function onDragMove(e) {
                if (!isDragging) return;
                if (e.cancelable) e.preventDefault();
                const coords = getEventCoords(e);
                const dx = coords.x - startX;
                const dy = coords.y - startY;
                if (Math.abs(dx) > 5 || Math.abs(dy) > 5) hasMoved = true;
                let newLeft = initialLeft + dx;
                let newTop = initialTop + dy;
                const widgetWidth = chatWidget.offsetWidth;
                const widgetHeight = chatWidget.offsetHeight;
                const pad = 12;
                newLeft = Math.max(pad, Math.min(newLeft, window.innerWidth - widgetWidth - pad));
                newTop = Math.max(pad, Math.min(newTop, window.innerHeight - widgetHeight - pad));
                chatWidget.style.bottom = 'auto';
                chatWidget.style.right = 'auto';
                chatWidget.style.left = newLeft + 'px';
                chatWidget.style.top = newTop + 'px';
                if (chatOpen) updatePanelPosition();
            }

            function onDragEnd() {
                if (!isDragging) return;
                isDragging = false;
                chatWidget.style.cursor = 'grab';
                document.removeEventListener('mousemove', onDragMove);
                document.removeEventListener('touchmove', onDragMove);
                document.removeEventListener('mouseup', onDragEnd);
                document.removeEventListener('touchend', onDragEnd);
            }

            chatWidget.addEventListener('mousedown', onDragStart);
            chatWidget.addEventListener('touchstart', onDragStart, { passive: true });
            chatToggle.addEventListener('click', (e) => {
                if (hasMoved) {
                    e.preventDefault();
                    e.stopPropagation();
                    return;
                }
                toggleChat();
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
            const chatWidget = document.getElementById('chat-widget');
            if (chatWidget) chatWidget.style.display = 'block';
            setupDragging();
            if (isLoggedIn()) initChatSilently();

            // Mobile Menu Toggle
            const menuToggle = document.getElementById('mobile-menu-toggle');
            const navLinks = document.getElementById('nav-links-menu');
            const menuIcon = document.getElementById('mobile-menu-icon');
            if (menuToggle && navLinks) {
                menuToggle.addEventListener('click', () => {
                    navLinks.classList.toggle('active');
                    const isActive = navLinks.classList.contains('active');
                    if (isActive) {
                        menuIcon.setAttribute('data-lucide', 'x');
                    } else {
                        menuIcon.setAttribute('data-lucide', 'menu');
                    }
                    lucide.createIcons();
                });
            }
        });
    </script>

    @stack('scripts')
</body>

</html>