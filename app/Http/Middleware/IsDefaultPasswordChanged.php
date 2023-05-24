<?php

namespace App\Http\Middleware;

use Closure;

class IsDefaultPasswordChanged
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
            if(auth()->user()->default_password_updated) {
                return $next($request);
            }
            return redirect(admin_url('user_info/change_pass_first_attempt'))->withErrors('Please change your password to access dashboard');
        }
        return redirect(admin_url('login'))->withErrors('Enter your creadentails!');
    }
}
