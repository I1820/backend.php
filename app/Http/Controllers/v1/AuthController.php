<?php

namespace App\Http\Controllers\v1;

use App\Exceptions\AuthException;
use App\Http\Controllers\Controller;
use App\Repository\Helper\Response;
use App\Repository\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    private $userService;

    /**
     * UserController constructor.
     * @param userService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param Request $request
     * @return array
     * @throws AuthException
     */
    public function register(Request $request)
    {
//        AuthValidation::register($request);

        $request->merge($request->json()->all());

        $this->userService->validateUser($request);

        $user = $this->userService->insertUser($request);

        # generate token
        $token = JWTAuth::fromUser($user);

        return Response::body(compact('token'));
    }

    /**
     * @param Request $request
     * @return array
     * @throws AuthException
     */
    public function login(Request $request)
    {
        $request->merge($request->json()->all());

        if ($this->loginValidator($request)->fails()) {
            throw new AuthException(AuthException::M_ER, AuthException::C_ER);
        }

        $token = $this->userService->generateToken($request);
        $user = Auth::user();
        if (!$user->active)
            throw new AuthException(AuthException::M_NA, AuthException::C_NA);

        return Response::body(compact('user', 'token'));
    }

    /**
     * @return array
     * @throws AuthException
     */
    public function refresh()
    {
        try {
            $token = $this->userService->refreshToken();
        } catch (\Exception $e) {
            return Response::body($e->getMessage(), $e->getCode());
        }
        return Response::body(compact('token'));
    }

    public function loginValidator($request)
    {
        $data = $request->only(['email', 'password']);
        return Validator::make($data, [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
    }
}
