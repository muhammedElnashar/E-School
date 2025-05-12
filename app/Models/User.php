<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class User extends Model
{
    use HasApiTokens,HasFactory;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'credits', 'iban'
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function lessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_student');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function ratings()
    {
        return $this->hasMany(LessonReview::class);
    }

    public function isTeacher()
    {
        return $this->role === 'teacher';
    }
}
