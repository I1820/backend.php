<?php

namespace App\Http\Controllers\v1;

use App\Repository\Helper\Response;
use App\Repository\Services\FileService;
use App\Repository\Services\UserService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    protected $userService;
    protected $fileService;

    /**
     * UserController constructor.
     * @param userService $userService
     * @param FileService $fileService
     */
    public function __construct(UserService $userService, FileService $fileService)
    {
        $this->userService = $userService;
        $this->fileService = $fileService;
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

    /**
     * @return array
     */
    public function profile()
    {
        $user = Auth::user();
        return Response::body(compact('user'));
    }

    /**
     * @param Request $request
     * @return array
     */
    public function upload(Request $request)
    {
        $user = Auth::user();
        $files = $request->allFiles();
        $paths = [];
        foreach ($files as $key => $file) {
            $paths[$key] = $this->fileService->saveFile($key, $file);
        }

        $files = $user['files'] ?: [];
        foreach ($paths as $key => $value) {
            $files[$key] = $value;
        }
        $user->files = $files;

        $user->save();
        return Response::body(compact('paths'));
    }
}
