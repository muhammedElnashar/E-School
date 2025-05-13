<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ErrorHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum','isStudent']);
    }
    public function updateUserProfile(UpdateUserRequest $request)
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
                $user->image = $request->file('image')->store('images/users', 'public');
            }
            $user->save();
            return response()->json([
                'message' => 'Profile updated successfully',
                'data' => new UserResource($user),
            ],200);
        }catch (\Throwable $e){
            return ErrorHandler::handle($e);
        }

    }

    public function deleteUserProfile(DeleteUserRequest $request)
    {
        try {
            $data = $request->validated();

            $user = auth()->user();
            if (!Hash::check($data['password'], $user->password)) {
                return response()->json([
                    'message' => 'Password is incorrect',
                ], 422);
            }
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
            $user->delete();
            return response()->json([
                'message' => 'Profile deleted successfully',
            ], 200);
        } catch (\Throwable $e) {
            return ErrorHandler::handle($e);
        }

    }
}
