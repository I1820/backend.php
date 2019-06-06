<?php

namespace app\Http\Middleware;

use App\Exceptions\AuthException;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthJwt
{
    protected $loggerService;

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
                throw new AuthException('user not found', AuthException::UNAUTHORIZED);
            }
        } catch (TokenExpiredException $e) {
            throw new AuthException('token expired', AuthException::UNAUTHORIZED);
        } catch (TokenInvalidException $e) {
            throw new AuthException('token invalid', AuthException::UNAUTHORIZED);
        } catch (JWTException $e) {
            throw new AuthException('unauthorized', AuthException::UNAUTHORIZED);
        }
    }
}
