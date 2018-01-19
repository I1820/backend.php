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
        //AuthValidation::register($request);


        $this->userService->validateRegisterUser($request);

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
        $validator = $this->loginValidator($request);
        if ($validator->fails()) {
            throw new AuthException($validator->errors()->first(), AuthException::C_ER);
        }

        $token = $this->userService->generateToken($request);
        $user = Auth::user();
        if (!$user->active)
            throw new AuthException(AuthException::M_NA, AuthException::C_NA);

        return Response::body(compact('user', 'token'));
    }

    /**
     * @return array
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

    /*
     * @return Validator
     */
    public function loginValidator($request)
    {
        $captcha = env('CAPTCHA_ENABLE', 0);
        $data = $request->only(['email', 'password', 'g-recaptcha-response']);
        return Validator::make($data, [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'g-recaptcha-response' => $captcha ? 'required|captcha' : ''
        ]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function logout(Request $request)
    {
        $token = JWTAuth::getToken();
        JWTAuth::invalidate($token);
        $message = "با موفقیت خارج شدید";
        return Response::body($message);
    }
}
