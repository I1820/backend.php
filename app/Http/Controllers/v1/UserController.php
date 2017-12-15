<?php

namespace App\Http\Controllers\v1;

use App\Repository\Helper\Response;
use App\Repository\Services\UserService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class UserController extends Controller
{
    protected $userService;

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
     * @throws \App\Exceptions\UserException
     */
    public function update(Request $request)
    {
        $this->userService->validateUpdateUser($request);

        $user = $this->userService->updateUser($request);


        return Response::body(compact('user'));
    }
}
