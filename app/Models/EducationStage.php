<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationStage extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
    ];
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'education_stage_subjects');
    }


}
