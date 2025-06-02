<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentStudent extends Model
{

    use HasFactory;
    protected $fillable = [
        'assignment_id',
        'student_id',
    ];
}
