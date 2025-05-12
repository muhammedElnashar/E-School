<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class ErrorHandler
{
    public static function handle(\Throwable $e)
    {

        Log::error('حدث خطأ في الدالة', [
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
            'user_id' => auth()->id() ,
        ]);

        return response()->json([
            'message' => 'Something went wrong. Please try again later.',
            'error' => config('app.debug') ? $e->getMessage() : null,
        ], 500);
    }
}
