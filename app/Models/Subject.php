<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;
    protected $fillable = ['name'];
    public function stages()
    {
        return $this->belongsToMany(EducationStage::class, 'education_stage_subjects', 'subject_id', 'education_stage_id');
    }
}
