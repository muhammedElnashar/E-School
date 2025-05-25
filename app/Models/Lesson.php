<?php

namespace App\Models;

use App\Enums\Scopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = ['teacher_id', "subject_id","education_stage_id",'start_datetime', 'end_datetime', 'lesson_type',  'zoom_link',  'reschedules_count'];

    protected $casts = [
        'lesson_type' => Scopes::class,

    ];
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }


    public function ratings()
    {
        return $this->hasMany(LessonReview::class);
    }
    public function recurrence()
    {
        return $this->hasOne(LessonRecurrence::class);
    }

    public function occurrences()
    {
        return $this->hasMany(LessonOccurrence::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function educationStage()
    {
        return $this->belongsTo(EducationStage::class);
    }
}
