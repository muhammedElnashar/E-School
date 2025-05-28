<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonOccurrence extends Model
{
    use HasFactory;
    protected $fillable = [
        'lesson_id',
        'occurrence_date',
    ];

    protected $dates = ['occurrence_date'];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
