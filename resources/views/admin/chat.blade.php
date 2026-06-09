@extends('layouts.admin')
@section('title', 'Admin — Kelola Chat | Defkan Computer')
@section('admin-title', 'Operator Chat')
@section('tab-chat', 'active')

@section('content')
    <div class="operator-chat-page">
        <div class="chat-admin-shell">
            {{-- Room List --}}
            <div class="glass-panel chat-room-panel">
                <div class="chat-room-header">
                    <div>
                        <h2>Percakapan</h2>
                        <p>Pesan pelanggan masuk secara realtime.</p>
                    </div>
                    <button type="button" class="chat-refresh-btn" onclick="loadRooms()" title="Refresh">
                        <i data-lucide="refresh-cw" class="icon-sm"></i>
                    </button>
                </div>
                <div id="room-list" class="room-list-scroll">
                    <div class="flex-center" style="padding: 40px;">
                        <div class="loader"></div>
                    </div>
                </div>
            </div>

            {{-- Messages Panel --}}
            <div class="glass-panel chat-message-panel">
                <div id="chat-header" class="admin-chat-header empty">
                    <div class="admin-chat-avatar muted">
                        <i data-lucide="messages-square" class="icon-sm"></i>
                    </div>
                    <div>
                        <h3>Pilih percakapan</h3>
                        <p>Mulai balas chat pelanggan dari daftar percakapan.</p>
                    </div>
                </div>

                <div id="admin-messages" class="admin-messages">
                    <div class="chat-empty-admin">
                        <i data-lucide="message-circle" class="empty-chat-icon"></i>
                        <h4>Belum ada percakapan dipilih</h4>
                        <p>Pilih pelanggan di sebelah kiri untuk membaca dan membalas chat.</p>
                    </div>
                </div>

                <div id="reply-area" class="admin-reply-area" style="display: none;">
                    <div class="admin-quick-replies">
                        <button type="button"
                            onclick="sendQuickReply('Halo, terima kasih sudah menghubungi Defkan Computer. Ada yang bisa kami bantu?')">Salam</button>
                        <button type="button"
                            onclick="sendQuickReply('Untuk stok laptop, silakan sebutkan merek atau kebutuhan laptop yang Anda cari.')">Stok
                            Laptop</button>
                        <button type="button"
                            onclick="sendQuickReply('Untuk servis laptop, silakan jelaskan merek laptop dan kendala yang dialami.')">Servis
                            Laptop</button>
                        <button type="button"
                            onclick="sendQuickReply('Untuk rekomendasi laptop, boleh sebutkan budget dan software yang akan digunakan?')">Rekomendasi</button>
                    </div>
                    <div class="admin-reply-box">
                        <input type="text" id="reply-input" class="form-control" placeholder="Ketik balasan..."
                            onkeydown="if(event.key==='Enter') sendReply()">
                        <button onclick="sendReply()" class="btn btn-primary admin-send-btn" type="button">
                            <i data-lucide="send" class="icon-sm"></i>
                            Kirim
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .operator-chat-page {
            width: 100%;
        }

        .chat-admin-shell {
            display: grid;
            grid-template-columns: 300px minmax(0, 1fr);
            gap: 18px;
            height: calc(100vh - 150px);
            min-height: 540px;
        }

        .chat-room-panel,
        .chat-message-panel {
            overflow: hidden;
            border-radius: 16px;
            border: 1px solid var(--glass-border, #e5eaf3);
            background: var(--bg-surface, #ffffff);
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
        }

        .chat-room-panel {
            display: flex;
            flex-direction: column;
        }

        .chat-room-header {
            padding: 16px 16px 14px;
            border-bottom: 1px solid var(--glass-border, #e5eaf3);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
            flex-shrink: 0;
        }

        .chat-room-header h2 {
            font-size: 15px;
            margin: 0;
            font-weight: 900;
            color: var(--text-main, #0f172a);
        }

        .chat-room-header p {
            margin: 3px 0 0;
            color: var(--text-muted, #64748b);
            font-size: 11.5px;
        }

        .chat-refresh-btn {
            width: 30px;
            height: 30px;
            border-radius: 9px;
            border: 1px solid var(--glass-border, #dbe3ef);
            background: #ffffff;
            color: var(--primary, #2563eb);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .room-list-scroll {
            overflow-y: auto;
            flex: 1;
        }

        .room-item {
            padding: 13px 14px;
            border-bottom: 1px solid var(--glass-border, #eef2f7);
            cursor: pointer;
            transition: background 0.2s, border-color 0.2s;
            position: relative;
        }

        .room-item:hover {
            background: rgba(37, 99, 235, 0.04);
        }

        .room-item.active {
            background: rgba(37, 99, 235, 0.08);
            border-left: 3px solid var(--primary, #2563eb);
        }

        .room-topline {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: flex-start;
        }

        .room-user-wrap {
            display: flex;
            gap: 10px;
            min-width: 0;
        }

        .room-avatar {
            width: 34px;
            height: 34px;
            border-radius: 999px;
            background: linear-gradient(135deg, #2563eb, #0ea5e9);
            color: #ffffff;
            font-weight: 900;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .room-user-name {
            font-weight: 800;
            font-size: 12.8px;
            color: var(--text-main, #0f172a);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 160px;
        }

        .room-last-message {
            color: var(--text-muted, #64748b);
            font-size: 11px;
            margin-top: 3px;
            max-width: 175px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .room-right-tools {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 6px;
            flex-shrink: 0;
        }

        .room-unread-badge {
            background: var(--danger, #ef4444);
            color: #fff;
            font-size: 10px;
            font-weight: 900;
            padding: 2px 7px;
            border-radius: 10px;
        }

        .delete-chat-btn {
            border: none;
            background: none;
            color: var(--text-muted, #64748b);
            cursor: pointer;
            padding: 4px;
            border-radius: 7px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .delete-chat-btn:hover {
            color: var(--danger, #ef4444) !important;
            background: rgba(239, 68, 68, 0.1);
        }

        .chat-message-panel {
            display: flex;
            flex-direction: column;
            padding: 0;
        }

        .admin-chat-header {
            padding: 15px 18px;
            border-bottom: 1px solid var(--glass-border, #e5eaf3);
            display: flex;
            align-items: center;
            gap: 11px;
            background: #ffffff;
            flex-shrink: 0;
        }

        .admin-chat-header.empty {
            color: var(--text-muted, #64748b);
        }

        .admin-chat-avatar {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary, #2563eb);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .admin-chat-avatar.muted {
            background: #f1f5f9;
            color: #64748b;
        }

        .admin-chat-header h3 {
            margin: 0;
            font-size: 14.5px;
            font-weight: 900;
            color: var(--text-main, #0f172a);
        }

        .admin-chat-header p {
            margin: 3px 0 0;
            color: var(--text-muted, #64748b);
            font-size: 11.5px;
        }

        .admin-messages {
            flex: 1;
            overflow-y: auto;
            padding: 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            background: #f8fafc;
        }

        .chat-empty-admin {
            min-height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: var(--text-muted, #64748b);
        }

        .empty-chat-icon {
            width: 52px;
            height: 52px;
            stroke-width: 1.4;
            opacity: 0.5;
            margin-bottom: 12px;
        }

        .chat-empty-admin h4 {
            margin: 0;
            font-size: 15px;
            color: var(--text-main, #0f172a);
        }

        .chat-empty-admin p {
            margin: 6px 0 0;
            font-size: 12.5px;
        }

        .message-row-admin {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
        }

        .message-row-admin.admin {
            align-items: flex-end;
        }

        .message-bubble-admin {
            max-width: min(70%, 560px);
            padding: 10px 13px;
            border-radius: 15px;
            font-size: 13px;
            line-height: 1.5;
            word-break: break-word;
        }

        .message-bubble-admin.user {
            border-bottom-left-radius: 5px;
            background: #ffffff;
            color: var(--text-main, #0f172a);
            border: 1px solid rgba(37, 99, 235, 0.12);
        }

        .message-bubble-admin.admin {
            border-bottom-right-radius: 5px;
            background: var(--primary, #2563eb);
            color: #ffffff;
            border: 1px solid transparent;
        }

        .message-time-admin {
            font-size: 10px;
            color: var(--text-muted, #94a3b8);
            padding: 0 4px;
        }

        .admin-reply-area {
            padding: 12px 16px 14px;
            border-top: 1px solid var(--glass-border, #e5eaf3);
            background: #ffffff;
            flex-shrink: 0;
        }

        .admin-quick-replies {
            display: flex;
            gap: 7px;
            overflow-x: auto;
            padding-bottom: 10px;
            scrollbar-width: none;
        }

        .admin-quick-replies::-webkit-scrollbar {
            display: none;
        }

        .admin-quick-replies button {
            height: 29px;
            border-radius: 999px;
            border: 1px solid #bfdbfe;
            background: #eff6ff;
            color: var(--primary, #2563eb);
            padding: 0 12px;
            font-size: 11px;
            font-weight: 800;
            white-space: nowrap;
            cursor: pointer;
        }

        .admin-reply-box {
            display: flex;
            gap: 10px;
        }

        .admin-reply-box .form-control {
            height: 38px;
            border-radius: 12px;
            font-size: 13px;
        }

        .admin-send-btn {
            height: 38px;
            padding: 0 16px !important;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 12px;
            font-weight: 800;
        }

        @media (max-width: 992px) {
            .chat-admin-shell {
                grid-template-columns: 1fr;
                height: auto;
            }

            .chat-room-panel {
                height: 260px;
            }

            .chat-message-panel {
                height: 560px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        let activeRoomId = null;
        let msgPolling = null;
        let lastMsgCount = 0;

        function safeText(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (!isLoggedIn() || !isAdmin()) {
                window.location.href = '/';
                return;
            }
            loadRooms();
            setInterval(loadRooms, 8000);
        });

        async function loadRooms() {
            try {
                const res = await apiFetch('/admin/chat/rooms');
                const rooms = res.data || [];
                const list = document.getElementById('room-list');

                if (rooms.length === 0) {
                    list.innerHTML = '<p class="text-muted" style="text-align:center; padding: 30px; font-size: 13px;">Belum ada percakapan.</p>';
                    return;
                }

                const currentScroll = list.scrollTop;
                list.innerHTML = rooms.map(r => {
                    const nama = safeText(r.pengguna_nama || 'Pelanggan');
                    const initial = nama.charAt(0).toUpperCase() || 'P';
                    const lastMessage = safeText(r.last_message || 'Belum ada pesan');
                    return `
                    <div class="room-item ${r.id === activeRoomId ? 'active' : ''}" onclick="selectRoom(event, ${r.id}, '${nama.replaceAll("'", "\\'")}')">
                        <div class="room-topline">
                            <div class="room-user-wrap">
                                <div class="room-avatar">${initial}</div>
                                <div style="min-width:0;">
                                    <div class="room-user-name">${nama}</div>
                                    <div class="room-last-message">${lastMessage}</div>
                                </div>
                            </div>
                            <div class="room-right-tools">
                                ${Number(r.unread_count || 0) > 0 ? `<span class="room-unread-badge">${r.unread_count}</span>` : ''}
                                <button onclick="confirmDeleteRoom(event, ${r.id})" class="delete-chat-btn" title="Hapus Obrolan" type="button">
                                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                }).join('');
                list.scrollTop = currentScroll;
                lucide.createIcons();
            } catch (e) {
                console.log('Room list error', e);
            }
        }

        async function selectRoom(event, roomId, userName) {
            activeRoomId = roomId;
            lastMsgCount = 0;

            document.querySelectorAll('.room-item').forEach(el => el.classList.remove('active'));
            const currentItem = event?.currentTarget || event?.target?.closest('.room-item');
            if (currentItem) currentItem.classList.add('active');

            document.getElementById('chat-header').classList.remove('empty');
            document.getElementById('chat-header').innerHTML = `
            <div class="admin-chat-avatar"><i data-lucide="user" class="icon-sm"></i></div>
            <div>
                <h3>Percakapan dengan <span style="color: var(--primary);">${safeText(userName)}</span></h3>
                <p>Balas pesan pelanggan secara cepat dan jelas.</p>
            </div>
        `;
            document.getElementById('reply-area').style.display = 'block';
            document.getElementById('admin-messages').innerHTML = '<div class="flex-center" style="flex: 1;"><div class="loader"></div></div>';

            clearInterval(msgPolling);
            await loadMessages(roomId);
            if (typeof loadSidebarCounts === 'function') loadSidebarCounts();
            msgPolling = setInterval(() => loadMessages(roomId), 3000);
        }

        async function loadMessages(roomId) {
            try {
                const res = await apiFetch(`/chat/messages/${roomId}`);
                const messages = res.data || [];

                if (messages.length !== lastMsgCount) {
                    const container = document.getElementById('admin-messages');
                    const wasAtBottom = container.scrollTop + container.clientHeight >= container.scrollHeight - 10;

                    if (messages.length === 0) {
                        container.innerHTML = `
                        <div class="chat-empty-admin">
                            <i data-lucide="message-circle" class="empty-chat-icon"></i>
                            <h4>Belum ada pesan</h4>
                            <p>Kirim sapaan pertama kepada pelanggan.</p>
                        </div>
                    `;
                    } else {
                        container.innerHTML = messages.map(m => {
                            const time = m.created_at ? new Date(m.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '';
                            return `
                            <div class="message-row-admin ${m.is_admin ? 'admin' : 'user'}">
                                <div class="message-bubble-admin ${m.is_admin ? 'admin' : 'user'}">${safeText(m.pesan)}</div>
                                <span class="message-time-admin">${m.is_admin ? 'Admin · ' : 'Pelanggan · '}${time}</span>
                            </div>
                        `;
                        }).join('');
                    }

                    lastMsgCount = messages.length;
                    if (wasAtBottom || lastMsgCount === messages.length) container.scrollTop = container.scrollHeight;
                    if (typeof loadSidebarCounts === 'function') loadSidebarCounts();
                    lucide.createIcons();
                }
            } catch (e) {
                // silent
            }
        }

        function sendQuickReply(text) {
            const input = document.getElementById('reply-input');
            input.value = text;
            input.focus();
        }

        async function sendReply() {
            const input = document.getElementById('reply-input');
            const text = input.value.trim();
            if (!text || !activeRoomId) return;

            input.value = '';
            try {
                await apiFetch('/admin/chat/reply', {
                    method: 'POST',
                    body: { room_id: activeRoomId, pesan: text }
                });
                lastMsgCount = -1;
                loadMessages(activeRoomId);
                loadRooms();
                if (typeof loadSidebarCounts === 'function') loadSidebarCounts();
            } catch (e) {
                showToast('Gagal mengirim balasan', 'error');
            }
        }

        async function confirmDeleteRoom(event, roomId) {
            event.stopPropagation();
            if (!confirm('Apakah Anda yakin ingin menghapus obrolan ini beserta seluruh riwayat pesannya?')) return;

            try {
                const res = await apiFetch(`/admin/chat/rooms/${roomId}`, { method: 'DELETE' });
                if (res.status === 'success') {
                    showToast('Obrolan berhasil dihapus');
                    if (activeRoomId === roomId) {
                        activeRoomId = null;
                        clearInterval(msgPolling);
                        document.getElementById('chat-header').classList.add('empty');
                        document.getElementById('chat-header').innerHTML = `
                        <div class="admin-chat-avatar muted"><i data-lucide="messages-square" class="icon-sm"></i></div>
                        <div>
                            <h3>Pilih percakapan</h3>
                            <p>Mulai balas chat pelanggan dari daftar percakapan.</p>
                        </div>
                    `;
                        document.getElementById('admin-messages').innerHTML = `
                        <div class="chat-empty-admin">
                            <i data-lucide="message-circle" class="empty-chat-icon"></i>
                            <h4>Belum ada percakapan dipilih</h4>
                            <p>Pilih pelanggan di sebelah kiri untuk membaca dan membalas chat.</p>
                        </div>
                    `;
                        document.getElementById('reply-area').style.display = 'none';
                        lucide.createIcons();
                    }
                    loadRooms();
                    if (typeof loadSidebarCounts === 'function') loadSidebarCounts();
                } else {
                    showToast(res.message || 'Gagal menghapus obrolan', 'error');
                }
            } catch (err) {
                showToast(err.message || 'Gagal menghapus obrolan', 'error');
            }
        }
    </script>
@endpush