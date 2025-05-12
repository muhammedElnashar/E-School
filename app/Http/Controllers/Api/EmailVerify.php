<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ErrorHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmailVerifyRequest;
use App\Mail\EmailVerificationOtp;
use App\Models\Otp;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class EmailVerify extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function verify(EmailVerifyRequest $request)
    {
        try {

            $user = auth()->user();

            if ($user->email_verified_at) {
                return response()->json(['message' => 'Your email is already verified. No need to verify again.'], 400);
            }

            $otpRecord = Otp::where('user_id', $user->id)
                ->where('type', 'email_verification')
                ->first();

            if (!$otpRecord) {
                return response()->json(['message' => 'Please check your OTP and try again.'], 404);
            }

            if (Carbon::now()->isAfter($otpRecord->expires_at)) {
                $otpRecord->delete();
                return response()->json(['message' => 'OTP has expired.'], 400);
            }

            if ($otpRecord->otp !== $request->otp) {
                $otpRecord->increment('attempts');
                if ($otpRecord->attempts >= 5) {
                    $otpRecord->delete();
                    return response()->json(['message' => 'Your OTP has expired due to too many attempts. Please request a new one.'], 400);
                }

                return response()->json(['message' => 'Invalid OTP.'], 400);
            }

            $user->email_verified_at = Carbon::now();
            $user->save();

            $otpRecord->delete();

            return response()->json(['message' => 'Email verified successfully.'], 200);
        }
        catch (\Throwable $e) {
            return ErrorHandler::handle($e);
        }

    }

    public function resendVerificationOtp()
    {
        try {
            $user = auth()->user();
            if ($user->email_verified_at) {
                return response()->json(['message' => 'Your email is already verified. No need to verify again.'], 400);
            }

            $otp = random_int(100000, 999999);
            Otp::updateOrCreate(
                ['user_id' => $user->id, 'type' => 'email_verification'],
                [
                    'otp' => $otp,
                    'expires_at' => Carbon::now()->addMinutes(10),
                    'attempts' => 0
                ]
            );
            Mail::to($user->email)->send(new EmailVerificationOtp($otp));
            return response()->json(['message' => 'Email Verification OTP sent successfully'], 200);

        }catch (\Throwable $e){
            return ErrorHandler::handle($e);
        }
    }
}
