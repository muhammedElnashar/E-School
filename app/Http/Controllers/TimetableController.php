<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Semester;
use App\Models\Timetable;
use App\Models\ClassSchool;
use App\Models\ClassSection;
use App\Models\ClassSubject;
use App\Models\ClassTeacher;
use Illuminate\Http\Request;
use App\Models\SubjectTeacher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TimetableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (!Auth::user()->can('timetable-list') || !Auth::user()->can('class-timetable')) {
            $response = array(
                'message' => trans('no_permission_message')
            );
            return redirect(route('home'))->withErrors($response);
        }
        $class_sections = ClassSection::with('class.medium', 'section','streams')->get();
        return view('timetable.index', compact('class_sections'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Auth::user()->can('timetable-create') || !Auth::user()->can('timetable-edit')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }

        $request->validate([
            'day' => 'required',
            'class_section_id' => 'required',
        ]);
        try {

            $currentSemester = Semester::get()->first(function ($semester) {
                return $semester->current;
            });

            $day_name = $request->day;
            $class_section_id = $request->class_section_id;

            $class = ClassSection::where('id', $class_section_id)->with('class')->get();

            if ($day_name == 'monday') {
                $day = 1;
            } elseif ($day_name == 'tuesday') {
                $day = 2;
            } elseif ($day_name == 'wednesday') {
                $day = 3;
            } elseif ($day_name == 'thursday') {
                $day = 4;
            } elseif ($day_name == 'friday') {
                $day = 5;
            } elseif ($day_name == 'saturday') {
                $day = 6;
            } elseif ($day_name == 'sunday') {
                $day = 7;
            }
            $a = $day_name . "_group";
            foreach ($request->$a as $data) {
                if (isset($data['id']) && $data['id'] != '') {
                    $timetable = Timetable::find($data['id']);
                } else {
                    $timetable = new Timetable();
                }
                $subject_teacher_id = SubjectTeacher::select('id')->where('subject_id', $data['subject_id'])->where('teacher_id', $data['teacher_id'])->pluck('id')->first();
                $timetable->subject_teacher_id = ($subject_teacher_id) ? ($subject_teacher_id) : 0;
                $timetable->class_section_id = $class_section_id;
                $timetable->semester_id = $currentSemester->id ?? null;
                $timetable->start_time = $data['start_time'];
                $timetable->end_time = $data['end_time'];
                $timetable->day = $day;
                $timetable->day_name = $day_name;
                $timetable->live_class_url = $data['live_class_url'];
                $timetable->link_name = $data['link_name'];
                $timetable->save();
            }

            return redirect()->back()->with('success', trans('data_store_successfully'));
        } catch (Throwable $e) {
            return redirect()->back()->with('error', trans('error_occurred'));
        }
    }

    public function getSubjectByClassSection(Request $request)
    {
        $class = ClassSchool::where('id', $request->class_id)->first();
        $currentSemester = Semester::get()->first(function ($semester) {
            return $semester->current;
        });

        if($class->include_semesters == 1 && $currentSemester)
        {
            $subjects = ClassSubject::SubjectTeacher()->where('class_id', $request->class_id)->where('semester_id', $currentSemester->id)->with('subject')->get();
        }else{
            $subjects = ClassSubject::SubjectTeacher()->where('class_id', $request->class_id)->with('subject')->get();
        }
            return response($subjects);
    }

    public function getteacherbysubject(Request $request)
    {
        $teacher = SubjectTeacher::where(['class_section_id' => $request->class_section_id, 'subject_id' => $request->subject_id])->with('teacher')->get();
        return response($teacher);
    }

    public function checkTimetable(Request $request)
    {
        $timetable = Timetable::with('subject_teacher')->where(['class_section_id' => $request->class_section_id, 'day' => $request->day])->get();
        return response($timetable);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Auth::user()->can('timetable-delete')) {
            $response = array(
                'error' => true,
                'message' => trans('no_permission_message')
            );
            return response()->json($response);
        }
        try {
            Timetable::find($id)->delete();
            $response = [
                'error' => false,
                'message' => trans('data_delete_successfully')
            ];
        } catch (Throwable $e) {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }


    public function class_timetable()
    {
        // check the user if teacher exists
        $user = Auth::user()->teacher;
        if ($user) {
            // if teacher exists then send the timetable data directly to view by its credentials
            $class_section_ids = ClassTeacher::where('class_teacher_id', $user->id)->pluck('class_section_id');
            $class_sections = ClassSection::with('class','class.medium', 'section','class.streams')->whereIn('id',$class_section_ids)->get();
            // $timetable = Timetable::whereIn('class_section_id', $class_section_ids)->with('subject_teacher')->orderBy('day', 'asc')->get();
            // $day = Timetable::select('day', 'day_name')->whereIn('class_section_id', $class_section_ids)->groupBy('day', 'day_name')->get();
            // $teacher_data = [
            //     'timetable' => $timetable->toArray(),
            //     'days' => $day->toArray(),
            //     'class_sections' => $class_sections->toArray(),
            // ];
            return view('timetable.class_timetable', compact('class_sections'));
        } else {
            // if teacher doesn't exists then send the class section data for select option directly to view
            if (!Auth::user()->can('timetable-list') || !Auth::user()->can('class-timetable')) {
                $response = array(
                    'error' => true,
                    'message' => trans('no_permission_message')
                );
                return response()->json($response);
            }
            $class_sections = ClassSection::ClassTeacher()->with('class.medium', 'section')->get();
            return view('timetable.class_timetable', compact('class_sections'));
        }
    }

    public function gettimetablebyclass(Request $request)
    {
        Session::put('class_timetable', $request->class_section_id);

        $timetable = Timetable::where('class_section_id', $request->class_section_id)->with('subject_teacher')->orderBy('day', 'asc')->get();

        $day = Timetable::select('day', 'day_name')->where('class_section_id', $request->class_section_id)->groupBy('day', 'day_name')->get();

        return $data = [
            'timetable' => $timetable,
            'days' => $day
        ];
    }

    public function teacher_timetable()
    {
        // check the user if teacher exists
        $user = Auth::user()->teacher;
        if ($user) {
            // if teacher exists then send the timetable data directly to view by its credentials
            $class_sections = ClassSection::SubjectTeacher()->with('class.medium', 'section')->get();
            // $subject_teacher = SubjectTeacher::where('teacher_id', $user->id)->pluck('id');
            // $timetable = Timetable::with('subject_teacher', 'class_section')->whereIn('subject_teacher_id', $subject_teacher)->get()->toArray();
            // $day = Timetable::select('day', 'day_name')->whereIn('subject_teacher_id', $subject_teacher)->groupBy('day', 'day_name')->get()->toArray();
            // $teacher_data = [
            //     'timetable' => $timetable,
            //     'days' => $day
            // ];
            return view('timetable.teacher_timetable', compact('class_sections'));
        } else {
            // if teacher doesn't exists then send the class section data for select option directly to view
            if (!Auth::user()->can('timetable-list') || !Auth::user()->can('teacher-timetable')) {
                $response = array(
                    'error' => true,
                    'message' => trans('no_permission_message')
                );
                return response()->json($response);
            }

            $teacher = Teacher::with('user')->teachers()->get();

            return view('timetable.teacher_timetable', compact('teacher'));
        }
    }

    public function gettimetablebyteacher(Request $request)
    {
        $subject_teacher = SubjectTeacher::select('id')->where('teacher_id', $request->teacher_id)->pluck('id');
        $timetable = array();
        $day = array();
        for ($i = 0; $i < count($subject_teacher); $i++) {
            $timetable[] = Timetable::with('subject_teacher', 'class_section')
                ->where('subject_teacher_id', $subject_teacher[$i])->get();
            // $day[] = Timetable::select('day', 'day_name')->where('subject_teacher_id', $subject_teacher[$i])->groupBy('day', 'day_name')->get();
        }
        $day[] = Timetable::select('day', 'day_name')->whereIn('subject_teacher_id', $subject_teacher)->groupBy('day', 'day_name')->get();
        return $data = [
            'timetable' => $timetable,
            'days' => $day
        ];
    }

    public function getTimetableBySubjectTeacherClass(Request $request)
    {
        $teacher = Auth::user()->teacher;
        $subject_teacher_id = SubjectTeacher::where('teacher_id',$teacher->id)->pluck('id')->toArray();

        // Session::put('class_timetable', $request->class_section_id);
        if($request->class_section_id){

            $timetable = Timetable::whereIn('subject_teacher_id',$subject_teacher_id)->where('class_section_id' , $request->class_section_id)->with('subject_teacher','class_section')->orderBy('day', 'asc')->get();

            $day = Timetable::select('day', 'day_name')->whereIn('subject_teacher_id',$subject_teacher_id)->where('class_section_id' , $request->class_section_id)->groupBy('day', 'day_name')->get();
            // dd($timetable->toArray());
        }else{
            $timetable = Timetable::whereIn('subject_teacher_id', $subject_teacher_id)->with('subject_teacher','class_section')->orderBy('day', 'asc')->get();

            $day = Timetable::select('day', 'day_name')->whereIn('subject_teacher_id', $subject_teacher_id)->groupBy('day', 'day_name')->get();
        }
        return $data = [
            'timetable' => $timetable,
            'days' => $day
        ];
    }

    public function linkUpdate(Request $request)
    {
        try{
            $timetable = Timetable::where('id', $request->edit_id)->first();
            $timetable->live_class_url = $request->live_class_url;
            $timetable->link_name = $request->link_name;
            $timetable->save();

            $response = [
                'error' => false,
                'message' => trans('data_store_successfully')
            ];
            
        }catch(Throwable $e)
        {
            $response = array(
                'error' => true,
                'message' => trans('error_occurred')
            );
        }
        return response()->json($response);
    }

    public function getTimeatableDetails($id)
    {
        $timetable = Timetable::where('id', $id)->first();
        return response($timetable);
    }
}
