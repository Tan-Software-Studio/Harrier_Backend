<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WithoutTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request)
            ->header('Access-Control-Allow-Origin', "*")
            ->header('Access-Control-Allow-Methods', "GET, POST, PUT, PATCH, DELETE, OPTIONS")
            ->header('Access-Control-Allow-Credentials', true)
            ->header('Access-Control-Allow-Headers', "Origin, X-Requested-With, Accept, Content-Type, Authorization")
            ->header('Accept', 'application/json');  
    }
}
