<?php

namespace App\Http\Controllers\StudentApi;

use App\Helpers\ErrorHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendOtpResetPasswordRequest;
use App\Http\Requests\VerifyResetPasswordOtpRequest;
use App\Mail\ResetPasswordOtp;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ResetPassword extends Controller
{
    public function sendResetPasswordOtp(SendOtpResetPasswordRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $otp = rand(100000, 999999);

            Otp::updateOrCreate(
                ['user_id' => $user->id, 'type' => 'reset_password'],
                [
                    'otp' => $otp,
                    'expires_at' => Carbon::now()->addMinutes(10),
                    'attempts' => 0
                ]
            );
            Mail::to($user->email)->send(new ResetPasswordOtp($otp));
            return response()->json(['message' => 'Reset Password OTP sent successfully'], 200);
        } catch (\Throwable $e) {
            return ErrorHandler::handle($e);

        }
    }

    public function verifyResetPasswordOtp(VerifyResetPasswordOtpRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            $otpRecord = Otp::where('user_id', $user->id)
                ->where('type', 'reset_password')
                ->first();

            if ($otpRecord->otp != $request->otp) {
                $otpRecord->increment('attempts');
                if ($otpRecord->attempts >= 5) {
                    $otpRecord->delete();
                    return response()->json(['message' => 'Your OTP has expired. Please request a new one.'], 400);
                }
                return response()->json(['message' => 'Invalid OTP'], 400);
            }
            if (Carbon::now()->isAfter($otpRecord->expires_at)) {
                $otpRecord->delete();
                return response()->json(['message' => 'OTP has expired'], 400);
            }

            return response()->json(['message' => 'OTP verified successfully'], 200);

        } catch (\Throwable $e) {
            return ErrorHandler::handle($e);
        }
    }
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $user->update(['password' => Hash::make($request->password)]);
            Otp::where('user_id', $user->id)->where('type', "reset_password")->delete();

            return response()->json(['message' => 'Password reset successfully'], 200);
        }catch (\Throwable $e){
            return ErrorHandler::handle($e);
        }

    }
}
