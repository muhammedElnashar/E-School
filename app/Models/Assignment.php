<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;
    protected $fillable = [
        'teacher_id',
        'text',
        'file_path',
        "lesson_occurrence_id"
    ];
    public function students()
    {
        return $this->belongsToMany(User::class, 'assignment_students', 'assignment_id', 'student_id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
    public function occurrence()
    {
        return $this->belongsTo(LessonOccurrence::class, 'lesson_occurrence_id');
    }

}
