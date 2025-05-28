<?php

namespace App\Http\Controllers\TeacherApi;

use App\Helpers\ErrorHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeacherSubjectRequest;
use App\Http\Requests\UpdateTeacherSubjectRequest;
use App\Http\Resources\TeacherSubjectResource;
use App\Models\TeacherSubject;
use Illuminate\Http\Request;

class TeacherSubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */

    public function index()
    {
        $assignments = TeacherSubject::with(['teacher', 'subject', 'educationStage'])->paginate(15);
        return response()->json([
            'status' => 'success',
            'data' => TeacherSubjectResource::collection($assignments),
        ]);

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function store(StoreTeacherSubjectRequest $request )
    {
        try {
            $data = $request->validated();
            $data['teacher_id'] = auth()->id();
            $assignment = TeacherSubject::create($data);

            return response()->json([
                'status' => 'success',
                'data' => new TeacherSubjectResource($assignment),
            ]);
        }catch (\Throwable $e){
            return ErrorHandler::handle($e);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     */
    public function update()
    {
      //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     */
    public function destroy($id)
    {
        try {
            $subject = TeacherSubject::find($id);
            if (!$subject){
                return response()->json(['status' => 'error', 'message' => 'Subject not found'], 404);
            }
            if ($subject->teacher_id !== auth()->id()) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
            }
            $subject->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Subject deleted successfully',
            ]);
        }catch (\Throwable $e){
            return ErrorHandler::handle($e);
        }

    }
}
