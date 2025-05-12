<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class LessonStudent extends Pivot
{
    use HasFactory;

    protected $fillable = ['lesson_id', 'user_id'];
}
