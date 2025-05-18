<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationStageSubject extends Model
{
    use HasFactory;
    public function educationStage()
    {
        return $this->belongsTo(EducationStage::class, 'education_stage_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}
