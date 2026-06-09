<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel — Defkan Computer')</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="{{ asset('img/logo.jpg') }}" type="image/jpeg">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            -webkit-font-smoothing: antialiased;
        }
        .admin-wrapper {
            display: grid;
            grid-template-columns: 220px 1fr;
            height: 100vh;
            overflow: hidden;
        }
        .admin-sidebar {
            background: var(--bg-surface);
            border-right: 1px solid var(--glass-border);
            padding: 16px 14px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            height: 100vh;
            overflow: hidden;
            z-index: 100;
            box-shadow: 1px 0 0 var(--glass-border);
        }
        .admin-brand {
            font-size: 18px;
            font-weight: 800;
            text-decoration: none;
            color: var(--primary);
            letter-spacing: -0.04em;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .admin-menu {
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex: 1;
        }
        .admin-menu-label {
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-subtle);
            padding: 0 10px;
            margin-bottom: 4px;
            margin-top: 4px;
        }
        .admin-menu-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            border-radius: var(--radius-sm);
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 600;
            font-size: 12.5px;
            transition: var(--transition);
            border: 1px solid transparent;
        }
        .admin-menu-link:hover {
            color: var(--primary);
            background: var(--primary-light);
            border-color: rgba(37, 99, 235, 0.1);
        }
        .admin-menu-link.active {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
            border-color: transparent;
        }
        .admin-menu-link .menu-icon {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        /* Sidebar Notification Badges */
        .sidebar-badge {
            margin-left: auto;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 10px;
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            min-width: 18px;
            height: 18px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        .badge-danger {
            background-color: var(--danger, #ef4444);
        }
        .badge-warning {
            background-color: var(--warning, #f59e0b);
        }
        .badge-primary {
            background-color: var(--primary, #2563eb);
        }
        .badge-accent {
            background-color: var(--accent, #06b6d4);
        }
        .admin-main {
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
        }
        .admin-navbar {
            height: 56px;
            min-height: 56px;
            border-bottom: 1px solid var(--glass-border);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: sticky;
            top: 0;
            z-index: 99;
            box-shadow: var(--shadow-xs);
        }
        .admin-content {
            padding: 20px 24px;
            flex-grow: 1;
            max-width: 1280px;
            width: 100%;
            margin: 0 auto;
        }
        .mobile-toggle {
            display: none;
            background: none;
            border: 1px solid var(--glass-border);
            color: var(--text-main);
            font-size: 18px;
            cursor: pointer;
            padding: 6px 10px;
            border-radius: var(--radius-sm);
        }
        .sidebar-divider {
            height: 1px;
            background: var(--glass-border);
            margin: 4px 0;
        }

        @media (max-width: 1200px) {
            .admin-wrapper { grid-template-columns: 1fr; }
            .admin-sidebar {
                display: none;
                position: fixed;
                inset: 0;
                width: 220px;
                z-index: 1000;
                box-shadow: var(--shadow-xl);
                overflow-y: auto;
            }
            .admin-sidebar.show { display: flex; }
            .admin-navbar { padding: 0 20px; }
            .mobile-toggle { display: block; }
            .admin-content { padding: 20px; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="admin-sidebar">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <a href="/admin" class="admin-brand">
                    <img src="{{ asset('img/logo.jpg') }}" alt="Logo" style="height: 26px; width: 26px; border-radius: 5px; object-fit: cover;">
                    <span>Defkan <span style="color: var(--text-muted); font-weight: 700;">Admin</span></span>
                </a>
                <button onclick="toggleSidebar()" class="mobile-toggle">✕</button>
            </div>

            <div class="admin-menu">
                <div class="admin-menu-label">Menu Utama</div>
                <a href="/admin" class="admin-menu-link @yield('tab-dashboard')">
                    <i data-lucide="bar-chart-3" class="icon icon-sm menu-icon"></i> Dashboard
                </a>
                <a href="/admin/produk" class="admin-menu-link @yield('tab-produk')">
                    <i data-lucide="package" class="icon icon-sm menu-icon"></i> Kelola Produk
                    <span class="sidebar-badge badge-danger" id="badge-produk" style="display: none;">0</span>
                </a>
                <a href="/admin/pesanan" class="admin-menu-link @yield('tab-pesanan')">
                    <i data-lucide="shopping-cart" class="icon icon-sm menu-icon"></i> Kelola Pesanan
                    <span class="sidebar-badge badge-danger" id="badge-pesanan" style="display: none;">0</span>
                </a>
                <a href="/admin/servis" class="admin-menu-link @yield('tab-servis')">
                    <i data-lucide="wrench" class="icon icon-sm menu-icon"></i> Kelola Servis
                    <span class="sidebar-badge badge-danger" id="badge-servis" style="display: none;">0</span>
                </a>
                <a href="/admin/chat" class="admin-menu-link @yield('tab-chat')">
                    <i data-lucide="message-square" class="icon icon-sm menu-icon"></i> Operator Chat
                    <span class="sidebar-badge badge-danger" id="badge-chat" style="display: none;">0</span>
                </a>

                <div class="admin-menu-label" style="margin-top: 16px;">Data & Laporan</div>
                <a href="/admin/pelanggan" class="admin-menu-link @yield('tab-pelanggan')">
                    <i data-lucide="users" class="icon icon-sm menu-icon"></i> Pelanggan
                </a>
                <a href="/admin/pendapatan" class="admin-menu-link @yield('tab-pendapatan')">
                    <i data-lucide="trending-up" class="icon icon-sm menu-icon"></i> Pendapatan
                </a>

                <div class="sidebar-divider" style="margin-top: auto;"></div>

                <a href="/" class="admin-menu-link" style="color: var(--accent);">
                    <i data-lucide="home" class="icon icon-sm menu-icon"></i> Kembali ke Toko
                </a>
                <a href="#" onclick="handleLogout(event)" class="admin-menu-link" style="color: var(--danger);">
                    <i data-lucide="log-out" class="icon icon-sm menu-icon"></i> Keluar
                </a>
            </div>
        </aside>

        <!-- Main Panel -->
        <div class="admin-main">
            <!-- Top Navbar -->
            <header class="admin-navbar">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <button onclick="toggleSidebar()" class="mobile-toggle">☰</button>
                    <div style="font-weight: 700; font-size: 16px; color: var(--text-main); letter-spacing: -0.02em;" id="admin-page-title">
                        @yield('admin-title', 'Dashboard')
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="text-align: right;" class="mobile-hide">
                        <div style="font-size: 13.5px; font-weight: 700; color: var(--text-main); line-height: 1.2;" id="admin-name">Admin</div>
                        <div style="font-size: 11px; color: var(--text-muted); font-weight: 500;">Operator Toko</div>
                    </div>
                    <div style="width: 38px; height: 38px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px; color: #fff; box-shadow: 0 2px 8px rgba(37,99,235,0.35);">
                        A
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="admin-content">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (!isLoggedIn() || !isAdmin()) {
                window.location.href = '/';
                return;
            }
            const user = getUser();
            if (user && user.nama) {
                document.getElementById('admin-name').textContent = user.nama;
            }
            lucide.createIcons();
            
            // Load sidebar notification bubbles
            loadSidebarCounts();
            // Poll every 30 seconds for new updates
            setInterval(loadSidebarCounts, 30000);
        });

        async function loadSidebarCounts() {
            try {
                const res = await apiFetch('/admin/sidebar-counts');
                if (res.status === 'success' && res.data) {
                    const d = res.data;
                    
                    updateSidebarBadge('badge-produk', d.produk_habis);
                    updateSidebarBadge('badge-pesanan', d.pesanan_baru);
                    updateSidebarBadge('badge-servis', d.servis_baru);
                    updateSidebarBadge('badge-chat', d.chat_unread);
                }
            } catch (err) {
                console.warn('Gagal memuat sidebar counts:', err);
            }
        }

        function updateSidebarBadge(id, count) {
            const el = document.getElementById(id);
            if (!el) return;
            if (count > 0) {
                el.textContent = count;
                el.style.display = 'inline-flex';
            } else {
                el.style.display = 'none';
            }
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('admin-sidebar');
            sidebar.classList.toggle('show');
        }
    </script>
    @stack('scripts')
</body>
</html>
