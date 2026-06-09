<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\ChatRoom;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatApiController extends Controller
{
    /**
     * GET /api/chat/rooms/{userId}
     * Ambil semua room chat milik user tertentu (buat otomatis jika belum ada).
     */
    public function listRooms(Request $request, $userId)
    {
        /** @var \App\Models\Pengguna $currentUser */
        $currentUser = $request->user();

        if (!$currentUser->isAdmin() && $currentUser->id_pengguna != $userId) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki akses ke data chat ini.',
            ], 403);
        }

        $rooms = ChatRoom::where('pengguna_id', $userId)
            ->withCount('messages')
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $rooms,
        ], 200);
    }

    /**
     * POST /api/chat/rooms
     * Buat room chat baru (atau kembalikan yang sudah ada).
     * Body: { "pengguna_id": 1 }
     */
    public function createRoom(Request $request)
    {
        /** @var \App\Models\Pengguna $currentUser */
        $currentUser = $request->user();

        $validator = Validator::make($request->all(), [
            'pengguna_id' => 'required|integer|exists:pengguna,id_pengguna',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        // Pengguna non-admin hanya boleh membuat/mengambil room untuk dirinya sendiri
        if (!$currentUser->isAdmin() && $currentUser->id_pengguna != $request->pengguna_id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki akses untuk membuat room untuk pengguna lain.',
            ], 403);
        }

        $room = ChatRoom::firstOrCreate(
            ['pengguna_id' => $request->pengguna_id, 'is_resolved' => false],
            ['judul' => 'Percakapan #' . date('YmdHis')]
        );

        return response()->json([
            'status' => 'success',
            'data'   => $room,
        ], 200);
    }

    /**
     * GET /api/chat/messages/{roomId}
     * Ambil semua pesan dari sebuah room chat.
     */
    public function getMessages(Request $request, $roomId)
    {
        $room = ChatRoom::find($roomId);

        if (!$room) {
            return response()->json(['status' => 'error', 'message' => 'Room tidak ditemukan'], 404);
        }

        /** @var \App\Models\Pengguna $currentUser */
        $currentUser = $request->user();

        // Hanya admin atau pemilik room chat tersebut yang bisa mengambil pesan
        if (!$currentUser->isAdmin() && $room->pengguna_id != $currentUser->id_pengguna) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki akses ke room chat ini.',
            ], 403);
        }

        // Jika yang mengakses adalah admin, tandai pesan user (is_admin = false) sebagai sudah dibaca
        if ($currentUser->isAdmin()) {
            ChatMessage::where('room_id', $roomId)
                ->where('is_admin', false)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        $messages = ChatMessage::where('room_id', $roomId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $messages,
        ], 200);
    }

    /**
     * POST /api/chat/messages
     * Kirim pesan baru dari user.
     * Body: { "room_id": 1, "pesan": "Halo" }
     */
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:chat_rooms,id',
            'pesan'   => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        $room = ChatRoom::find($request->room_id);
        if (!$room) {
            return response()->json(['status' => 'error', 'message' => 'Room tidak ditemukan'], 404);
        }

        /** @var \App\Models\Pengguna $currentUser */
        $currentUser = $request->user();

        // Pengguna hanya boleh mengirim pesan ke room miliknya sendiri
        if (!$currentUser->isAdmin() && $room->pengguna_id != $currentUser->id_pengguna) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki akses ke room chat ini.',
            ], 403);
        }

        $message = ChatMessage::create([
            'room_id'  => $request->room_id,
            'user_id'  => $currentUser->id_pengguna, // Set user_id secara aman dari pengguna yang terautentikasi
            'pesan'    => $request->pesan,
            'is_admin' => false,
        ]);

        ChatRoom::where('id', $request->room_id)->touch();

        return response()->json([
            'status' => 'success',
            'message' => 'Pesan terkirim',
            'data'   => $message,
        ], 201);
    }

    /**
     * PATCH /api/chat/messages/{roomId}/read
     * Tandai semua pesan admin di room sebagai sudah dibaca (oleh user)
     */
    public function markAsRead(Request $request, $roomId)
    {
        $room = ChatRoom::find($roomId);
        if (!$room) {
            return response()->json(['status' => 'error', 'message' => 'Room tidak ditemukan'], 404);
        }

        /** @var \App\Models\Pengguna $currentUser */
        $currentUser = $request->user();

        if (!$currentUser->isAdmin() && $room->pengguna_id != $currentUser->id_pengguna) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki akses ke room chat ini.',
            ], 403);
        }

        ChatMessage::where('room_id', $roomId)
            ->where('is_admin', true)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['status' => 'success']);
    }

    // ============================================================
    // ADMIN ONLY METHODS
    // ============================================================

    /**
     * GET /api/admin/chat/rooms
     * Admin: Ambil semua chat rooms dengan pesan terakhir & info user.
     */
    public function adminListRooms()
    {
        $rooms = ChatRoom::with('latestMessage')
            ->withCount(['messages as unread_count' => function ($q) {
                $q->where('is_admin', false)->where('is_read', false);
            }])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($room) {
                // Ambil data pengguna via DB karena relasi berbeda primary key
                $pengguna = \Illuminate\Support\Facades\DB::table('pengguna')
                    ->where('id_pengguna', $room->pengguna_id)
                    ->first();
                $room->pengguna_nama = $pengguna ? $pengguna->nama : 'Unknown';
                $room->last_message  = $room->latestMessage?->pesan;
                return $room;
            });

        return response()->json([
            'status' => 'success',
            'data'   => $rooms,
        ]);
    }

    /**
     * POST /api/admin/chat/reply
     * Admin: Balas pesan pelanggan.
     * Body: { "room_id": 1, "pesan": "Halo, ada yang bisa kami bantu?" }
     */
    public function adminReply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:chat_rooms,id',
            'pesan'   => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        $message = ChatMessage::create([
            'room_id'  => $request->room_id,
            'user_id'  => null,
            'pesan'    => $request->pesan,
            'is_admin' => true,
        ]);

        ChatRoom::where('id', $request->room_id)->touch();

        // Tandai pesan user di room ini sebagai sudah dibaca
        ChatMessage::where('room_id', $request->room_id)
            ->where('is_admin', false)
            ->update(['is_read' => true]);

        return response()->json([
            'status' => 'success',
            'data'   => $message,
        ], 201);
    }

    /**
     * DELETE /api/admin/chat/rooms/{id}
     * Admin: Hapus obrolan / room chat beserta semua pesannya.
     */
    public function deleteRoom($id)
    {
        $room = ChatRoom::find($id);
        if (!$room) {
            return response()->json(['status' => 'error', 'message' => 'Room tidak ditemukan'], 404);
        }

        // Hapus pesan terkait
        ChatMessage::where('room_id', $id)->delete();
        // Hapus room
        $room->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Obrolan berhasil dihapus.'
        ]);
    }
}
