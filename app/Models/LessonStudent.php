<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonStudent extends Model
{
    protected $table = 'lesson_students';

    protected $fillable = [
        'lesson_occurrence_id',
        'student_id',
        'purchase_id',
         'is_paid_to_teacher'
    ];
    public function lesson()
    {
        return $this->hasOneThrough(
            Lesson::class,
            LessonOccurrence::class,
            'id',              // FK on lesson_occurrences table
            'id',              // FK on lessons table
            'lesson_occurrence_id', // Local key on lesson_students
            'lesson_id'        // Local key on lesson_occurrences
        );
    }

    public function lessonOccurrence(): BelongsTo
    {
        return $this->belongsTo(LessonOccurrence::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public static function totalLessonsForTeacher(int $teacherId): int
    {
        return self::whereHas('lessonOccurrence.lesson', function ($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->count();
    }

    /**
     * إجمالي الحصص التي تم الدفع عليها للمعلم (حسب is_paid_to_teacher)
     */
    public static function paidLessonsForTeacher(int $teacherId): int
    {
        return self::where('is_paid_to_teacher', true)
            ->whereHas('lessonOccurrence.lesson', function ($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            })->count();
    }
}
