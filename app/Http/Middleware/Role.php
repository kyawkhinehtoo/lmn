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
     * 0 - super admin
     * 1 - admin 
     * 2 - technical 
     * 3 - sale
     * 4 - billing
     * 5 - subcom
     * 
     **/
    public function handle(Request $request, Closure $next)
    {
        if (Auth::user()->role == 0 || Auth::user()->role == 1) { // if the current role is Administrator
            return $next($request);
        }
        abort(403, "Cannot access to restricted page");
    }
}
