<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DataVar
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next) {
        //return array('sample1' => 'sample1');
        return $next($request);
    }
    
    public function datadisplay() {
        return array('sample2' => 'sample2');
    }
}
