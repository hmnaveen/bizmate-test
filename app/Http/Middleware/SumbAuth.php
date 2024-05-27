<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SumbAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next) {
        if ($request->session()->missing('keysumb')) {
            $oriform['err'] = 7;
            return redirect()->route('index', $oriform); die();
        }
        
        $value = $request->session()->get('keysumb');
        $decrypted_value = decrypt($value);
        $userinfo = explode("|&",$decrypted_value);
        $request->attributes->add(['userinfo' => $userinfo]);
        
        return $next($request);
        
    }
}
