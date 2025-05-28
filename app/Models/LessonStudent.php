<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonStudent extends Model
{
    protected $table = 'lesson_students';

    protected $fillable = [
        'lesson_occurrence_id',
        'student_id',
        'purchase_id',
    ];

    public function lessonOccurrence(): BelongsTo
    {
        return $this->belongsTo(LessonOccurrence::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }
}
