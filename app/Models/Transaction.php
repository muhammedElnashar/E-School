<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['teacher_id','admin_id', 'amount', 'paid_at'];
    protected $dates = [
        'paid_at',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

}
