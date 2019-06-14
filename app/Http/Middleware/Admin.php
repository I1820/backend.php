<?php

namespace App\Http\Middleware;

use App\Exceptions\GeneralException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws GeneralException
     */
    public function handle($request, Closure $next)
    {
        $this->authenticate();

        return $next($request);
    }

    /**
     * @throws GeneralException
     */
    private function authenticate()
    {
        /**
         * @var \App\User|null
         */
        $user = Auth::user();
        if (!$user || !$user->isAdmin())
            throw new GeneralException(GeneralException::M_ACCESS_DENIED, GeneralException::ACCESS_DENIED);

    }
}
