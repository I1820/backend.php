<?php

namespace App\Http\Controllers\v1;

use App\Exceptions\AuthException;
use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Mail\EmailVerification;
use App\Repository\Helper\Response;
use App\Repository\Services\UserService;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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
     * @throws GeneralException
     */
    public function register(Request $request)
    {
        //AuthValidation::register($request);


        $this->userService->validateRegisterUser($request);

        $user = $this->userService->insertUser($request);
        # generate token
        # $token = JWTAuth::fromUser($user);

        Mail::to($user['email'])->send(new EmailVerification($user));

        return Response::body(['message' => 'ایمیل فعال سازی برای شما فرستاده شد']);
    }

    /**
     * @param Request $request
     * @return array
     * @throws AuthException
     * @throws GeneralException
     */
    public function login(Request $request)
    {
        $request->merge($request->json()->all());
        $validator = $this->loginValidator($request);
        if ($validator->fails()) {
            throw new GeneralException($validator->errors()->first(), GeneralException::VALIDATION_ERROR);
        }

        $credentials = $request->only('email', 'password');
        $access_token = $this->userService->generateAccessTokenByCredentials($credentials);

        // check user activation status
        $user = auth()->user();
        if (!$user->active)
            throw new AuthException(AuthException::M_USER_NOT_ACTIVE, AuthException::UNAUTHORIZED);

        $refresh_token = $this->userService->generateRefreshToken($user->getKey());

        $config = ['portainer_url' => env('PORTAINER_URL'), 'prometheus_url' => env('PROMETHEUS_URL')];
        return Response::body(compact('user', 'access_token', 'refresh_token', 'config'));
    }


    /**
     * @param $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function loginValidator($request)
    {
        $data = $request->only(['email', 'password']);
        return Validator::make($data, [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
    }

    /**
     * refresh API
     * @return array
     * @throws GeneralException
     */
    public function refresh()
    {
        $user = auth()->user();
        $token = $this->userService->refreshToken($user->getKey());
        return Response::body(['token' => $token, 'user' => $user]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function logout(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::invalidate($token);
        } catch (Exception $e) {
        }
        $message = "با موفقیت خارج شدید";
        return Response::body($message);
    }

    /**
     * @param User $user
     * @param $token
     * @return void
     */
    public function verifyEmail(User $user, $token)
    {
        if (md5($user['email_token']) == $token) {
            $user['active'] = true;
            $user->unset('email_token');
            $user->save();
            return view('auth.verified', ['token' => JWTAuth::fromUser($user)]);
        }
        return view('auth.not_verified');
    }
}
