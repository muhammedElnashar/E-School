<?php

namespace App\Http\Middleware;

use App\Enums\RoleEnum;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!in_array($user->role->value, [
            RoleEnum::SuperAdmin->value,
            RoleEnum::Admin->value,
        ])) {
            Auth::logout();

            return redirect()->back()
                ->with('error', 'You do not have Admin or Super Admin access');

        }


        return $next($request);
    }
}
