<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuestMiddleware
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
        if($request->header('Accept') == false)
        {
            $bug = 'Accept Headers required';
            return sendErrorHelper(trans('msg.error'), $bug, error());
        }

        if($request->header('Accept') != 'application/json')
        {
            $bug = 'missing Accept : application/json';
            return sendErrorHelper(trans('msg.error'), $bug, error());
        }
        
        if (! $request->expectsJson()) {
            $bug = trans('msg.something_wrong');
            return sendErrorHelper(trans('msg.error'), $bug, error());
        }
        if (auth()->check() &&  auth()->user()->isGuest() && canGuest()) {
            
            if(auth()->user()->is_request != login_active())
            {
                // auth()->logout();
                return sendError('Account expired.', [], error()); 
            }

            return $next($request)
            ->header('Access-Control-Allow-Origin', "*")
            ->header('Access-Control-Allow-Methods', "GET, POST, PUT, PATCH, DELETE, OPTIONS")
            ->header('Access-Control-Allow-Credentials', true)
            ->header('Access-Control-Allow-Headers', "Origin, X-Requested-With, Accept, Content-Type, Authorization")
            ->header('Accept', 'application/json');  
        }else{
            if (request()->expectsJson()) {
                return sendError('Unauthenticated', [], unAuth());
            }   
            return $next($request);
        }
    }
}
