<?php

namespace App\Http\Controllers\TeacherApi;

use App\Enums\RoleEnum;
use App\Helpers\ErrorHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\StudentResource;
use App\Http\Resources\TeacherResource;
use App\Http\Resources\TeacherSubjectResource;
use App\Http\Resources\TeacherTransactionsResource;
use App\Models\TeacherSubject;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    public function updateTeacherProfile(UpdateUserRequest $request)
    {
        try {
            $user = auth()->user();

            if ($request->has('name')) {
                $user->name = $request->input('name');
            }
            if ($request->hasFile('image')) {
                if ($user->image) {
                    Storage::disk('public')->delete($user->image);
                }
                $user->image = $request->file('image')->store('users', 'images');
            }
            if ($request->has('phone')) {
                $user->phone = $request->input('phone');
            }
            if ($request->has('iban')) {
                $user->iban = $request->input('iban');
            }
            $user->save();
            return response()->json([
                'message' => 'Profile updated successfully',
                'data' => new TeacherResource($user),
            ],200);
        }catch (\Throwable $e){
            return ErrorHandler::handle($e);
        }

    }

    public function getTeacherSubject()
    {
        try {
            $user = auth()->user();
            if ($user->role->value !== RoleEnum::Teacher->value) {
                return response()->json([
                    'message' => 'Unauthorized',
                ], 403);
            }
            $subjects = TeacherSubject::with('subject','educationStage')->where('teacher_id',$user->id)->get();
            return response()->json([
                'message' => 'Subjects retrieved successfully',
                'data' => TeacherSubjectResource::collection($subjects),
            ], 200);
        } catch (\Throwable $e) {
            return ErrorHandler::handle($e);
        }
    }
    public function getUserInfo($id)
    {
        $user = User::whereNotIn('role', [RoleEnum::Admin, RoleEnum::SuperAdmin])
            ->where('id', $id)
            ->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json([
            'status' => 'success',
            "data"=> new StudentResource($user),
        ]);
    }

    public function getTeacherTransaction()
    {
        try {
            $user = auth()->user();
            if ($user->role->value !== RoleEnum::Teacher->value) {
                return response()->json([
                    'message' => 'Unauthorized',
                ], 403);
            }
            $transactions = $user->transactions()->get();
            return response()->json([
                'message' => 'Transactions retrieved successfully',
                'data' => TeacherTransactionsResource::collection($transactions),
            ], 200);
        } catch (\Throwable $e) {
            return ErrorHandler::handle($e);
        }
    }
}
