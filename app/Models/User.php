<?php

namespace App\Models;

use App\Enums\RoleEnum;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'iban', 'user_code', 'image',
        'email_verified_at',
        'social_id',
        'social_type',
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'role' => RoleEnum::class,
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

    public function teacherSubjects()
    {
        return $this->hasMany(TeacherSubject::class, 'teacher_id');
    }
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
