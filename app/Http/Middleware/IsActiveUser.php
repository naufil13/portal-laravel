<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class IsActiveUser
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
        if (auth()->check()) {
            if (auth()->user()->status == 'Active') {
                return $next($request);
            }
            Auth::logout();
            return redirect(admin_url('login'))->withErrors('Inactive User');
        }
        return redirect(admin_url('login'))->withErrors('Enter your credentials!');
    }
}
