<?php

namespace App\Http\Middleware;

use Closure;

class CrossMiddleware
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
        if(env('CLIENT_ORIGIN'))
            return $next($request)
                ->header('Access-Control-Allow-Origin', env('CLIENT_ORIGIN'))
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS, PATCH')
                ->header('Access-Control-Allow-Headers',$request->header('Access-Control-Request-Headers'));
        return $next($request);
    }
}
