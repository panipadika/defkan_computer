<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory;

    protected $table = 'chat_rooms';
    protected $guarded = ['id'];

    // satu room memiliki banyak pesan
    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'room_id');
    }

    // pesan terakhir di room
    public function latestMessage()
    {
        return $this->hasOne(ChatMessage::class, 'room_id')->latestOfMany();
    }

    // optional: peserta room (user) – asumsi ada tabel users
    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_room_user', 'room_id', 'user_id');
    }
}
?>
