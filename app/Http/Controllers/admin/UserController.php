<?php

namespace App\Http\Controllers\admin;

use App\Exceptions\GeneralException;
use App\Repository\Helper\Response;
use App\Repository\Services\UserService;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;


class UserController extends Controller
{
    protected $userService;
    protected $fileService;

    /**
     * UserController constructor.
     * @param userService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function list(Request $request)
    {
        $users = User::skip(intval($request->get('offset')))
            ->take(intval($request->get('limit')) ?: 10)
            ->get()->makeVisible(['_id', 'active']);
        return Response::body(compact('users'));
    }

    public function ban(User $user, Request $request)
    {
        $active = $request->get('active') ? true : false;
        $user->active = $active;
        $user->save();
        $user->makeVisible(['_id', 'active']);
        return Response::body(compact('user'));
    }

    public function get(User $user)
    {
        $user->makeVisible(['_id', 'active','is_admin']);
        return Response::body(compact('user'));
    }

    public function setPassword(User $user, Request $request)
    {
        $password = $request->get('password');
        if (!$password || strlen($password) < 6)
            throw  new GeneralException('لطفا رمز عبور را درست وارد کنید(حداقل ۶ کاراکتر)', GeneralException::VALIDATION_ERROR);
        $user->password = bcrypt($password);
        $user->save();
        $user->makeVisible(['_id', 'active']);
        return Response::body(compact('user'));
    }


}
