<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'start_datetime', 'end_datetime', 'lesson_type', 'teacher_id', 'zoom_link', 'student_review', 'reschedules_count'];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'lesson_student');
    }

    public function ratings()
    {
        return $this->hasMany(LessonReview::class);
    }
}
