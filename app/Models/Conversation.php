<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['user_one_id', 'user_two_id','last_message'];

    public function userOne() {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function userTwo() {
        return $this->belongsTo(User::class, 'user_two_id');
    }
    public function messages()
    {
        return $this->hasMany(Chat::class);
    }
    public function otherUser($currentUserId)
    {
        return $this->user_one_id === $currentUserId ? $this->userTwo : $this->userOne;
    }
}
