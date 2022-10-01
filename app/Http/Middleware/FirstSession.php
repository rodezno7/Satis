<?php

namespace App\Http\Middleware;
use Auth;
use Closure;

class FirstSession
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
        if(Auth::check())
        {
            $user_status = Auth::user()->status;
            if($user_status == "pending")
            {
                return redirect('start');
            }
            return $next($request);
        }
    }
}