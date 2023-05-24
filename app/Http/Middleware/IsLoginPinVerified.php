<?php

namespace App\Http\Middleware;

use Closure;

class IsLoginPinVerified
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

        if(auth()->check()) {
            if(auth()->user()->two_factor_auth) {
                if(auth()->user()->pin_verified) {
                    return $next($request);
                }
                return redirect(admin_url('user_info/pin_confirmation'))->withErrors('Please Enter Pin code to proceed!');
            }
            return $next($request);

        }
        return redirect(admin_url('login'))->withErrors('Enter your creadentails!');
    }
}
