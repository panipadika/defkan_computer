<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $table = 'chat_messages';
    protected $guarded = ['id']; // allow mass assign for other fields

    /**
     * Message belongs to a chat room
     */
    public function room()
    {
        return $this->belongsTo(ChatRoom::class, 'room_id');
    }

    /**
     * Optional: link to user who sent the message.
     * Assuming a Users model exists.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
?>
