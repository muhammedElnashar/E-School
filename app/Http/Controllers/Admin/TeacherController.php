<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Models\User;
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
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(StoreTeacherRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['user_code'] = Str::random(8);
        while (User::where('user_code', $data['user_code'])->exists()) {
            $data['user_code'] = Str::random(8);
        }
        $data['role'] = 'teacher';
        $data['email_verified_at'] = now();
        User::create($data);
        return redirect()->route('teacher.index')->with('success', 'Teacher created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function update(UpdateTeacherRequest $request, $id)
    {
        $teacher = User::findOrFail($id);

        $teacher->name = $request->input('name');
        $teacher->email = $request->input('email');

        if ($request->filled('password')) {
            $teacher->password = Hash::make($request->input('password'));
        }
        $teacher->save();
        return redirect()->route('teacher.index')->with('success', 'تم تحديث بيانات المعلم بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id)
    {
        $teacher = User::findOrFail($id);
        $teacher->delete();
        return redirect()->route('teacher.index')->with('success', 'تم حذف المعلم بنجاح');
    }
}
