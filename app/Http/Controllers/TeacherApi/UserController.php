<?php

namespace App\Http\Controllers\TeacherApi;

use App\Helpers\ErrorHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\TeacherResource;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
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
                $user->image = $request->file('image')->store('images/teacher', 'public');
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

}
