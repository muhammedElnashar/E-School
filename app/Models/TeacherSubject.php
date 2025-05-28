<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherSubject extends Model
{
    use HasFactory;
    protected $fillable = [
        'teacher_id',
        'subject_id',
        'education_stage_id',
    ];
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
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
