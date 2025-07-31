<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
class UserMiddleware
{
    public function handle($request, Closure $next) {
        if(Auth::check()) {
            if(Auth::user()->is_admin == 0) {
                return $next($request);
            } else {
                Auth::logout();
                return redirect(url('/'));
            }
        

        } else {
            Auth::logout();
            return redirect(url('/'));
        }
        } 
    }
