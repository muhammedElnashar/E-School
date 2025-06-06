<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonRecurrence extends Model
{
    use HasFactory;
    protected $fillable = [
        'lesson_id',
        'weeks_count',
        'exception_dates',
    ];
    protected $casts = [
        'exception_dates' => 'array',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
