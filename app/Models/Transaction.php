<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'amount', 'transaction_type', 'status', 'payment_method'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
