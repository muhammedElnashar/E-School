<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LessonType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Models\LessonStudent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $teachers = User::where('role', 'teacher')->paginate(5);
        return view('admin.teachers.index', compact('teachers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
        return view('admin.teachers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function store(StoreTeacherRequest $request)
    {
        $data = $request->validated();

        $data['password'] = Hash::make($data['password']);
        $data["user_code"] = random_int(1000000, 9999999);
        while (User::where('user_code', $data['user_code'])->exists()) {
            $data['user_code'] = random_int(1000000, 9999999);
        }
        $data['role'] = 'teacher';
        $data['email_verified_at'] = now();
        User::create($data);
        return redirect()->route('teacher.index')->with('success', 'Teacher created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     */

    public function show(User $teacher)
    {
        $totalLessons = LessonStudent::totalLessonsForTeacher($teacher->id);
        $paidLessons = LessonStudent::paidLessonsForTeacher($teacher->id);

        $teacher->load([
            'lessons.occurrences.students',
            'lessons.occurrences.lessonStudents.purchase'
        ]);

        $now = now();

        $individualCount = 0;
        $groupCount = 0;

        foreach ($teacher->lessons as $lesson) {

            foreach ($lesson->occurrences as $occurrence) {
                if ($occurrence->students->isEmpty()) {
                    continue;
                }

                $startDateTime = Carbon::parse(
                    $occurrence->occurrence_date->format('Y-m-d') . ' ' .
                    Carbon::parse($lesson->start_datetime)->format('H:i:s')
                );

                if ($now->lessThan($startDateTime)) {
                    continue;
                }

                $lessonStudents = $occurrence->lessonStudents;
                $allHavePurchases = $lessonStudents->every(function ($ls) {
                    return $ls->purchase !== null;
                });

                if (!$allHavePurchases) {
                    continue;
                }

                if ($lesson->lesson_type->value === LessonType::Individual->value) {
                    $individualCount++;
                } elseif ($lesson->lesson_type->value === LessonType::Group->value) {
                    $groupCount++;
                }
            }
        }

        $individualPrice = config("lesson_individual_price", 0);
        $groupPrice = config("lesson_group_price", 0);
        $total = ($individualCount * $individualPrice) + ($groupCount * $groupPrice);

        return view('admin.teachers.unpaid-lessons', compact(
            'teacher',
            'individualCount',
            'groupCount',
            'individualPrice',
            'groupPrice',
            'total',
            'totalLessons',
            'paidLessons'
        ));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     */
    public function update(UpdateTeacherRequest $request, $id)
    {
        $teacher = User::findOrFail($id);

        $teacher->name = $request->input('name');
        $teacher->email = $request->input('email');
        $teacher->phone = $request->input('phone');
        $teacher->iban = $request->input('iban');

        if ($request->filled('password')) {
            $teacher->password = Hash::make($request->input('password'));
        }

        $teacher->save();

        return redirect()
            ->route('teacher.index')
            ->with('success', 'Teacher updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     */
    public function destroy($id)
    {
        $teacher = User::findOrFail($id);
        $teacher->delete();
        return redirect()->route('teacher.index')->with('success', 'تم حذف المعلم بنجاح');
    }
}
