const API_URL = '/api';

/**
 * Custom fetch wrapper to automatically inject the Bearer token.
 */
async function apiFetch(endpoint, options = {}) {
    const token = localStorage.getItem('auth_token');
    
    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        ...options.headers
    };

    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }

    // Don't stringify if it's FormData
    if (options.body && !(options.body instanceof FormData) && typeof options.body === 'object') {
        options.body = JSON.stringify(options.body);
    }
    
    // Remove Content-Type if FormData
    if (options.body instanceof FormData) {
        delete headers['Content-Type'];
    }

    const config = { ...options, headers };

    try {
        const response = await fetch(`${API_URL}${endpoint}`, config);
        
        // Cek jika response diarahkan ke login (karena sesi habis/tidak valid)
        // Jangan intercept jika endpoint adalah login/register karena 401 di sana berarti kredensial salah
        const isAuthRoute = endpoint.includes('/auth/login') || endpoint.includes('/auth/register');
        if (!isAuthRoute && (response.status === 401 || (response.url && response.url.includes('/login')))) {
            logout(true);
            throw new Error('Sesi Anda telah berakhir. Silakan login kembali.');
        }
        
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.indexOf("application/json") !== -1) {
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Terjadi kesalahan. Silakan coba lagi.');
            }
            return data;
        } else {
            if (!response.ok) {
                throw new Error(`Error ${response.status}: Terjadi kesalahan.`);
            }
            throw new Error('Server tidak mengembalikan format yang valid.');
        }
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

// =====================
// Authentication Helpers
// =====================

/**
 * Simpan token ke cookie (agar middleware PHP bisa membacanya
 * untuk proteksi server-side route /admin/*). SameSite=Strict
 * mencegah CSRF. Tidak ada HttpOnly agar JS bisa hapus saat logout.
 */
function setAuthCookie(token) {
    const maxAge = 60 * 60 * 24 * 7; // 7 hari (dalam detik)
    document.cookie = `admin_token=${token}; path=/; max-age=${maxAge}; SameSite=Strict`;
}

function clearAuthCookie() {
    document.cookie = 'admin_token=; path=/; max-age=0; SameSite=Strict';
}

function loginSuccess(token, user) {
    localStorage.setItem('auth_token', token);
    localStorage.setItem('user', JSON.stringify(user));
    // Simpan juga ke cookie agar middleware server bisa memvalidasi sesi admin
    setAuthCookie(token);
    window.location.href = '/';
}

function logout(redirect = true) {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user');
    // Hapus cookie sesi admin
    clearAuthCookie();
    if (redirect) {
        window.location.href = '/login';
    }
}

function isLoggedIn() {
    return !!localStorage.getItem('auth_token');
}

function getUser() {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
}

function isAdmin() {
    const user = getUser();
    return user && user.role === 'admin';
}

// =====================
// UI Helpers
// =====================
function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(angka);
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `glass`;
    Object.assign(toast.style, {
        position: 'fixed',
        bottom: '24px',
        left: '50%',
        transform: 'translateX(-50%) translateY(100px)',
        padding: '14px 22px',
        borderRadius: '40px',
        zIndex: '99999',
        borderLeft: `4px solid ${type === 'error' ? 'var(--danger)' : type === 'warning' ? 'var(--warning)' : 'var(--success)'}`,
        boxShadow: '0 10px 40px rgba(0,0,0,0.6)',
        opacity: '0',
        transition: 'all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1)',
        fontSize: '14px',
        fontWeight: '500',
        whiteSpace: 'nowrap',
        backdropFilter: 'blur(20px)',
    });
    
    const icon = type === 'error' ? '❌' : type === 'warning' ? '⚠️' : '✅';
    toast.textContent = `${icon} ${message}`;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.transform = 'translateX(-50%) translateY(0)';
        toast.style.opacity = '1';
    }, 10);
    
    setTimeout(() => {
        toast.style.transform = 'translateX(-50%) translateY(100px)';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 350);
    }, 3500);
}

// =====================
// DOM Setup
// =====================
document.addEventListener('DOMContentLoaded', () => {
    updateNavbarState();
    highlightActiveNav();
    showChatWidget();
});

function updateNavbarState() {
    const authLinks  = document.getElementById('auth-links');
    const userLinks  = document.getElementById('user-links');
    const userNameEl = document.getElementById('user-name');
    const adminLink  = document.getElementById('admin-link');
    
    if (isLoggedIn() && userLinks && authLinks) {
        authLinks.style.display = 'none';
        userLinks.style.display = 'flex';
        const user = getUser();
        if (user && userNameEl) {
            userNameEl.textContent = user.nama ? user.nama.split(' ')[0] : '';
        }
        // Tampilkan link admin jika role admin
        if (adminLink && isAdmin()) {
            adminLink.style.display = 'block';
        }
        updateCartBadge();
    }
}

function highlightActiveNav() {
    const path = window.location.pathname;
    document.querySelectorAll('.nav-link').forEach(link => {
        if (link.href && new URL(link.href, window.location.origin).pathname === path) {
            link.classList.add('active');
        }
    });
}

function showChatWidget() {
    // Tampilkan widget chat hanya jika user sudah login
    const widget = document.getElementById('chat-widget');
    if (widget && isLoggedIn()) {
        widget.style.display = 'block';
    }
}

window.handleLogout = async (e) => {
    e.preventDefault();
    try {
        await apiFetch('/auth/logout', { method: 'POST' });
    } catch (err) {
        console.log('Force logout');
    }
    logout();
};

// Scroll effect untuk navbar
function handleNavbarScroll() {
    const navbar = document.getElementById('main-navbar');
    if (navbar) {
        if (window.scrollY > 20) {
            navbar.style.padding = '10px 0';
            navbar.style.boxShadow = '0 2px 12px rgba(0, 0, 0, 0.06), 0 1px 3px rgba(0, 0, 0, 0.04)';
        } else {
            navbar.style.padding = '14px 0';
            navbar.style.boxShadow = '0 1px 2px rgba(0, 0, 0, 0.05)';
        }
    }
}

window.addEventListener('scroll', handleNavbarScroll);
document.addEventListener('DOMContentLoaded', handleNavbarScroll);

async function updateCartBadge() {
    if (!isLoggedIn()) return;
    const badge = document.getElementById('cart-badge');
    if (!badge) return;
    
    try {
        const res = await apiFetch('/keranjang');
        const items = res.data || [];
        const total = items.reduce((acc, item) => acc + (parseInt(item.jumlah) || 0), 0);
        
        if (total > 0) {
            badge.textContent = total;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    } catch (err) {
        console.error('Failed to update cart badge:', err);
    }
}
