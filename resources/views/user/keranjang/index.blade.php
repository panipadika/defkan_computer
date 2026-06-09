@extends('layouts.app')

@section('title', 'Keranjang Belanja — Defkan Computer')

@section('content')
<div class="container page-container">

    {{-- Page Header --}}
    <div class="page-header">
        <h1 class="page-title">
            <i data-lucide="shopping-cart" class="icon icon-lg text-primary page-title-icon"></i> <span class="text-primary-gradient">Keranjang</span> Belanja
        </h1>
        <p class="text-muted page-subtitle">Kelola produk yang ingin Anda beli sebelum melakukan checkout.</p>
    </div>

    {{-- Loading --}}
    <div id="loading-state" class="flex-center empty-state">
        <div class="loader"></div>
    </div>

    {{-- Empty State --}}
    <div id="empty-state" class="empty-state" style="display: none;">
        <div class="empty-state-icon">
            <i data-lucide="shopping-cart" style="width: 64px; height: 64px; stroke-width: 1.25;"></i>
        </div>
        <h3 class="text-muted empty-state-title">Keranjang Anda Kosong</h3>
        <p class="text-muted empty-state-desc">Yuk, mulai belanja dan tambahkan produk ke keranjang!</p>
        <a href="/produk" class="btn btn-primary">Lihat Katalog</a>
    </div>

    {{-- Main Cart Content --}}
    <div id="cart-content" style="display: none;">
        <div class="cart-grid">

            {{-- Left: Cart Items --}}
            <div>
                <div class="cart-header-actions" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; background: rgba(30, 41, 59, 0.2); padding: 12px 16px; border-radius: var(--radius-md); border: 1px solid var(--glass-border);">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" id="select-all-checkbox" onchange="toggleSelectAll(this)" style="width: 18px; height: 18px; cursor: pointer; accent-color: var(--primary);" checked>
                        <label for="select-all-checkbox" style="font-size: 14px; font-weight: 600; color: var(--text-main); cursor: pointer; margin-bottom: 0; user-select: none;">Pilih Semua</label>
                    </div>
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <span class="text-muted" style="font-size: 13px;" id="item-count"></span>
                        <button class="btn btn-danger" style="padding: 6px 14px; font-size: 13px;" onclick="clearCart()">Kosongkan</button>
                    </div>
                </div>
                <div id="cart-items" class="flex-column gap-16"></div>
            </div>

            {{-- Right: Summary Panel --}}
            <div class="glass-panel cart-summary-panel" style="padding: 24px; border-radius: 12px; height: fit-content;">
                <h3 class="cart-summary-title" style="margin-top: 0; margin-bottom: 16px; font-size: 16px; font-weight: 700; color: var(--text-main);">Ringkasan Belanja</h3>
                <div class="cart-summary-items" style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px;">
                    <div class="cart-summary-row" style="display: flex; justify-content: space-between;">
                        <span class="text-muted" style="font-size: 14px;">Total Item</span>
                        <span id="summary-total-item" style="font-weight: 600; color: var(--text-main);">0</span>
                    </div>
                    <div class="cart-summary-row" style="display: flex; justify-content: space-between; border-top: 1px dashed var(--glass-border); padding-top: 12px;">
                        <span class="text-muted" style="font-size: 14px; font-weight: 700;">Total Harga</span>
                        <span id="summary-total-harga" class="cart-summary-total-lbl" style="font-size: 18px; font-weight: 800; color: var(--accent);">Rp 0</span>
                    </div>
                </div>

                <button class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 16px; font-weight: 700;" onclick="goToCheckout()">
                    Lanjut ke Checkout <i data-lucide="arrow-right" class="icon icon-sm" style="margin-left: 6px; display: inline-block; vertical-align: middle;"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    if (!isLoggedIn()) {
        window.location.href = '/login';
        return;
    }
    loadKeranjang();
});

let cartData = [];

async function loadKeranjang() {
    const loading = document.getElementById('loading-state');
    const empty = document.getElementById('empty-state');
    const content = document.getElementById('cart-content');

    try {
        const res = await apiFetch('/keranjang');
        cartData = res.data || [];

        loading.style.display = 'none';

        if (cartData.length === 0) {
            empty.style.display = 'block';
            content.style.display = 'none';
            return;
        }

        empty.style.display = 'none';
        content.style.display = 'block';

        renderCartItems(cartData);
        recalcSummary();
        updateCartBadge();
    } catch (err) {
        loading.style.display = 'none';
        empty.style.display = 'block';
        showToast(err.message, 'error');
    }
}

function toggleSelectAll(selectAllCb) {
    const checkboxes = document.querySelectorAll('.cart-item-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = selectAllCb.checked;
    });
    recalcSummary();
}

function handleItemCheck() {
    const checkboxes = document.querySelectorAll('.cart-item-checkbox');
    const checkedCount = document.querySelectorAll('.cart-item-checkbox:checked').length;
    const selectAllCb = document.getElementById('select-all-checkbox');
    
    if (selectAllCb) {
        selectAllCb.checked = (checkboxes.length === checkedCount);
    }
    recalcSummary();
}

function renderCartItems(items) {
    const container = document.getElementById('cart-items');
    document.getElementById('item-count').textContent = `${items.length} produk di keranjang`;
    container.innerHTML = '';

    items.forEach(item => {
        const p = item.produk || {};
        const maxStok = p.stok ?? 99;
        const el = document.createElement('div');
        el.className = 'glass cart-item';
        el.id = `cart-item-${item.id}`;
        el.style.display = 'flex';
        el.style.alignItems = 'center';
        el.style.gap = '16px';
        el.innerHTML = `
            <input type="checkbox" class="cart-item-checkbox" data-id="${item.id}" onchange="handleItemCheck()" style="width: 18px; height: 18px; cursor: pointer; accent-color: var(--primary); flex-shrink: 0;" checked>
            <img src="${p.foto_url || '/img/placeholder.png'}" alt="${p.nama_produk}" class="cart-item-img" style="flex-shrink: 0;" onerror="this.src='https://placehold.co/400x300/EFF8FF/0EA5E9?text=Laptop'">
            <div style="flex: 1; min-width: 0;">
                <h4 style="font-size: 16px; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--text-main);">${p.nama_produk || 'Produk'}</h4>
                <p class="text-muted" style="font-size: 13px; margin-bottom: 8px;">Stok tersedia: ${p.stok ?? '-'}</p>
                <span style="font-weight: 700; color: var(--accent); font-size: 17px;">${formatRupiah(p.harga || 0)}</span>
            </div>
            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 12px; flex-shrink: 0;">
                <div class="qty-control">
                    <button class="qty-btn" onclick="changeQty(${item.id}, -1, ${maxStok})">−</button>
                    <input type="number" class="qty-value" id="qty-input-${item.id}" value="${item.jumlah}" min="1" max="${maxStok}" style="text-align: center; -moz-appearance: textfield; width: 48px;" onchange="handleQtyInput(${item.id}, this, ${maxStok})" onkeydown="if(event.key==='Enter'){this.blur();}">
                    <button class="qty-btn" onclick="changeQty(${item.id}, 1, ${maxStok})">+</button>
                </div>
                <div style="font-size: 14px; font-weight: 600; color: var(--text-muted);">
                    Subtotal: <span style="color: var(--text-main); font-weight: 700;" id="subtotal-${item.id}">${formatRupiah((p.harga || 0) * item.jumlah)}</span>
                </div>
                <button class="btn btn-danger" style="padding: 4px 12px; font-size: 12px;" onclick="removeItem(${item.id})">Hapus</button>
            </div>
        `;
        container.appendChild(el);
    });
    
    // reset select-all status when loading new items
    const selectAllCb = document.getElementById('select-all-checkbox');
    if (selectAllCb) selectAllCb.checked = true;
}

function recalcSummary() {
    let totalItem = 0;
    let totalHarga = 0;
    
    const checkedCheckboxes = document.querySelectorAll('.cart-item-checkbox:checked');
    const checkedIds = Array.from(checkedCheckboxes).map(cb => parseInt(cb.dataset.id));
    
    cartData.forEach(item => {
        if (checkedIds.includes(item.id)) {
            const p = item.produk || {};
            totalItem += item.jumlah;
            totalHarga += (p.harga || 0) * item.jumlah;
        }
    });
    
    document.getElementById('summary-total-item').textContent = totalItem + ' item';
    document.getElementById('summary-total-harga').textContent = formatRupiah(totalHarga);
    
    // Disable/enable checkout button based on item selection
    const btnCheckout = document.querySelector('.cart-summary-panel button');
    if (btnCheckout) {
        btnCheckout.disabled = (totalItem === 0);
    }
}

function handleQtyInput(id, input, maxStok) {
    let val = parseInt(input.value);
    if (isNaN(val) || val < 1) {
        val = 1;
    }
    if (val > maxStok) {
        val = maxStok;
        showToast(`Stok tersedia hanya ${maxStok}`, 'error');
    }
    input.value = val;
    updateQty(id, val, maxStok);
}

async function changeQty(id, delta, maxStok) {
    const input = document.getElementById(`qty-input-${id}`);
    let currentQty = parseInt(input.value) || 1;
    let newQty = currentQty + delta;

    if (newQty < 1) {
        return;
    }
    if (newQty > maxStok) {
        showToast(`Stok tersedia hanya ${maxStok}`, 'error');
        return;
    }

    input.value = newQty;
    await updateQty(id, newQty, maxStok);
}

async function updateQty(id, newQty, maxStok) {
    if (newQty < 1) newQty = 1;
    if (newQty > maxStok) {
        showToast(`Stok tersedia hanya ${maxStok}`, 'error');
        return;
    }
    try {
        await apiFetch(`/keranjang/${id}`, { method: 'PATCH', body: { jumlah: newQty } });
        const item = cartData.find(i => i.id === id);
        if (item) {
            item.jumlah = newQty;
            const p = item.produk || {};
            const subtotalEl = document.getElementById(`subtotal-${id}`);
            if (subtotalEl) {
                subtotalEl.textContent = formatRupiah((p.harga || 0) * newQty);
            }
        }
        recalcSummary();
        updateCartBadge();
    } catch (err) {
        showToast(err.message, 'error');
        loadKeranjang();
    }
}

async function removeItem(id) {
    try {
        await apiFetch(`/keranjang/${id}`, { method: 'DELETE' });
        const el = document.getElementById(`cart-item-${id}`);
        if (el) {
            el.style.transition = 'all 0.3s ease';
            el.style.opacity = '0';
            el.style.transform = 'translateX(-40px)';
            setTimeout(() => {
                loadKeranjang();
                updateCartBadge();
            }, 300);
        } else {
            loadKeranjang();
            updateCartBadge();
        }
        showToast('Produk dihapus dari keranjang');
    } catch (err) {
        showToast(err.message, 'error');
    }
}

async function clearCart() {
    if (!confirm('Yakin ingin mengosongkan seluruh keranjang?')) return;
    try {
        await apiFetch('/keranjang', { method: 'DELETE' });
        loadKeranjang();
        updateCartBadge();
        showToast('Keranjang telah dikosongkan');
    } catch (err) {
        showToast(err.message, 'error');
    }
}

function goToCheckout() {
    const checkedCheckboxes = document.querySelectorAll('.cart-item-checkbox:checked');
    if (checkedCheckboxes.length === 0) {
        showToast('Pilih minimal satu produk untuk checkout', 'error');
        return;
    }
    const selectedIds = Array.from(checkedCheckboxes).map(cb => parseInt(cb.dataset.id));
    localStorage.setItem('checkout_cart_ids', JSON.stringify(selectedIds));
    window.location.href = '/checkout';
}
</script>
@endpush
