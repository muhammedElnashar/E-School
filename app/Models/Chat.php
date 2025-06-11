<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = ['conversation_id', 'sender_id', 'message'];

    public function sender() {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function conversation() {
        return $this->belongsTo(Conversation::class);
    }
}
