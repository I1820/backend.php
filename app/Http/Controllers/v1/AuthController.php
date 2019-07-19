<?php

namespace App\Http\Controllers\v1;

use App\Exceptions\AuthException;
use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Mail\EmailVerification;
use App\Repository\Helper\Response;
use App\Repository\Services\UserService;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @OA\Tag(
 *     name="Auth",
 *     description="Login/Register/Logout",
 * )
 */
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
     * @param RegisterRequest $request
     * @return array
     * @throws GeneralException
     * @OA\Post(
     *      path="/v1/register",
     *      operationId="register",
     *      tags={"Auth"},
     *      summary="Register the user",
     *      description="Returns the user and sends him/her verification email",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              schema={"$ref": "#/components/schemas/RegisterRequest"}
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @OA\Response(response=400, description="Bad request"),
     *       @OA\Response(response=500, description="Email operation failed (user is created but not activated)"),
     *     )
     */
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();
        $user = $this->userService->insertUser($validated['name'], $validated['email'], $validated['password']);

        Mail::to($user['email'])->send(new EmailVerification($user));

        return Response::body(['message' => 'ایمیل فعال سازی برای شما فرستاده شد']);
    }

    /**
     * @param LoginRequest $request
     * @return array
     * @throws AuthException
     * @throws GeneralException
     * @OA\Post(
     *      path="/v1/login",
     *      operationId="login",
     *      tags={"Auth"},
     *      summary="Log in the user",
     *      description="Check the user credentials and logs him/her in",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              schema={"$ref": "#/components/schemas/LoginRequest"}
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *      ),
     *      @OA\Response(response=400, description="Bad request"),
     *     )
     */
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();
        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password']
        ];
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
