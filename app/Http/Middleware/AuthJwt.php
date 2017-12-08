<?php

namespace app\Http\Middleware;

use App\Exceptions\AuthException;
use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthJwt
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
                throw new AuthException(AuthException::M_UNF, AuthException::C_UNF);
            }
        } catch (TokenExpiredException $e) {
            throw new AuthException(AuthException::M_TE, AuthException::C_TE);
        } catch (TokenInvalidException $e) {
            throw new AuthException(AuthException::M_TI, AuthException::C_TI);
        } catch (JWTException $e){
            throw new AuthException(AuthException::M_UA, AuthException::C_UA);
        }
    }
}
