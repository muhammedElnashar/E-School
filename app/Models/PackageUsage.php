<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageUsage extends Model
{
    use HasFactory;
    protected $fillable = [
        'purchase_id',
        'lesson_id',
    ];
}
