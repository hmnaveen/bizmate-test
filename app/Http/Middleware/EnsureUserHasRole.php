<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        $value = $request->session()->get('keysumb');
        $decrypted_value = decrypt($value);
        $userinfo = explode("|&",$decrypted_value);
        
        $allowed_roles = array_slice(func_get_args(), 2);
        
        if( in_array($userinfo[3], $allowed_roles) ) {
            return $next($request);
        }
        
        abort(403);
    }
}
