<?php

namespace App\Http\Middleware;

use Closure;

class AdminMiddleware
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
        $user =\Auth::user()->load('usertype');
        if(in_array($user->usertype->for, ['Backend', 'Both'])){
            if (!user_do_action(getUri(3), null, true)) {
                //return redirect('404');
                return response()->view('errors.404');
            }
            return $next($request);
        } else if(in_array($user->usertype->for, ['None'])){
            return redirect('/');
        } else {
            return redirect('member/account');
        }
    }
}
