<?php

namespace App\Http\Controllers\StudentApi;


use App\Enums\RoleEnum;
use App\Helpers\ErrorHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\StudentResource;
use App\Http\Resources\UserResource;
use App\Mail\EmailVerificationOtp;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function studentRegister(RegisterRequest $request)
    {
        try {
            $data =$request->all();
            $data['password'] = Hash::make($request->password);
            $data['role']= RoleEnum::Student->value;
            $data["user_code"] = random_int(1000000, 9999999);
            while (User::where('user_code', $data['user_code'])->exists()) {
                $data['user_code'] = random_int(1000000, 9999999);
            }
            $user = User::create($data);
            $otp = random_int(100000, 999999);
            Otp::create([
                'user_id' => $user->id,
                'otp' => $otp,
                'type' => "email_verification",
                'expires_at' => Carbon::now()->addMinutes(10),
            ]);
            Mail::to($user->email)->send(new EmailVerificationOtp($otp));
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message' => 'User registered successfully',
                'user' => new StudentResource($user),
                'token' => $token,
            ], 201);
        }catch (\Throwable $e){
            return ErrorHandler::handle($e);
        }

    }


    public function studentLogin(LoginRequest $request)
    {
        $user= User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid Email Or Password , Please try again',
            ], 401);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'User logged in successfully',
            'user' => new StudentResource($user),
            'token' => $token
        ]);

    }
    public function studentLogout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'message' => 'User logged out successfully',
        ]);
    }

    public function handleGoogleCallback(Request $request)
    {
        $authorizationHeader = $request->header('Authorization');

        if (!$authorizationHeader || !str_starts_with($authorizationHeader, 'Bearer ')) {
            return response()->json(['error' => 'Token is required'], 400);
        }

        $googleToken = substr($authorizationHeader, 7);

        try {
            $user = Socialite::driver('google')->stateless()->userFromToken($googleToken);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid token or failed to authenticate'], 401);
        } catch (\Throwable $e) {
            Log::error('Google OAuth Error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong'], 500);
        }

        if (filter_var($user->email, FILTER_VALIDATE_EMAIL) === false) {
            return response()->json(['error' => 'Invalid email address'], 400);
        }

        $existingUser = User::where('social_id', $user->id)->first();
        if ($existingUser) {
            $token = $existingUser->createToken('auth_token')->plainTextToken;
            return response()->json([
                'user' => new StudentResource($existingUser),
                'token' => $token
            ], 200);
        } else {
            $existingUserByEmail = User::where('email', $user->email)->first();
            if ($existingUserByEmail) {
                return response()->json(['error' => 'Email already registered'], 400);
            }

            $userCode = random_int(1000000, 9999999);
            while (User::where('user_code', $userCode)->exists()) {
                $userCode = random_int(1000000, 9999999);
            }


            $newUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'user_code' => $userCode,
                'role' => RoleEnum::Student->value,
                'email_verified_at' => now(),
                'social_id' => $user->id,
                'social_type' => 'google',
            ]);

            $otp = random_int(100000, 999999);
            Otp::create([
                'user_id' => $newUser->id,
                'otp' => $otp,
                'type' => 'email_verification',
                'expires_at' => Carbon::now()->addMinutes(10),
            ]);
            Mail::to($newUser->email)->send(new EmailVerificationOtp($otp));

            $token = $newUser->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'User registered successfully. Please check your email to verify.',
                'user' => new StudentResource($newUser),
                'token' => $token
            ], 201);
        }
    }
}
