<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class AuthApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::guard('api')->user();
        if($user) {
            if($user->active) {
                return $next($request);
            }
            else {
                return response()->json(array(
                    'success' => false,
                    'errors' => ["Unauthorized access. Use a valid api_key"],
                    'data' => null
                ));
            }
        }
        else {
            return response()->json(array(
                'success' => false,
                'errors' => ["User has not been enabled. Please check your email"],
                'data' => null
            ));
        }
       
    }
}
