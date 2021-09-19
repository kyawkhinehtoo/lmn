<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
        /**
     * Role 
     * 1 - super admin
     * 2 - admin 
     * 
     **/
    public function handle(Request $request, Closure $next)
    {
        if (Auth::user()->role == 1 || Auth::user()->role == 2) { // if the current role is Administrator
            return $next($request);
        }
        abort(403, "Cannot access to restricted page");
    }
}
