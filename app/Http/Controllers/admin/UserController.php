<?php

namespace App\Http\Controllers\admin;

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
        $users = User::skip(intval($request->get('offset')))->take(intval($request->get('limit')) ?: 10)->get();
        return Response::body(compact('users'));

    }

}
