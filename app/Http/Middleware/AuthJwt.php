<?php

namespace App\Http\Middleware;

use App\Exceptions\AuthException;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthJwt
{
    public function __construct()
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws AuthException
     */
    public function handle($request, Closure $next)
    {
        $this->authenticate();
        return $next($request);
    }

    /**
     * @throws AuthException
     */
    private function authenticate()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                throw new AuthException('token not found', 401);
            }
        } catch (TokenExpiredException $e) {
            throw new AuthException('token expired', 401);
        } catch (TokenInvalidException $e) {
            throw new AuthException('invalid token', 401);
        } catch (JWTException $e) {
            throw new AuthException('cannot validate token', 401);
        }
    }
}
