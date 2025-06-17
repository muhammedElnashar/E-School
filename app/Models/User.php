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
        'phone',
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

    public function occurrences()
    {
        return $this->belongsToMany(LessonOccurrence::class, 'lesson_students','student_id');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'teacher_id');
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class,'teacher_id');
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
    public function submissions()
    {
        return $this->hasMany(Submission::class, 'student_id');
    }
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_subjects', 'teacher_id', 'subject_id');
    }


    public function sentMessages() {
        return $this->hasMany(Chat::class, 'sender_id');
    }

    public function conversationsAsUserOne() {
        return $this->hasMany(Conversation::class, 'user_one_id');
    }

    public function conversationsAsUserTwo() {
        return $this->hasMany(Conversation::class, 'user_two_id');
    }

    public function conversations() {
        return $this->conversationsAsUserOne->merge($this->conversationsAsUserTwo);
    }

    public function transactionsAsTeacher()
    {
        return $this->hasMany(Transaction::class, 'teacher_id');
    }

    public function transactionsAsAdmin()
    {
        return $this->hasMany(Transaction::class, 'admin_id');
    }
}
