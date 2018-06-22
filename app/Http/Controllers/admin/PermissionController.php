<?php

namespace App\Http\Controllers\admin;

use App\Exceptions\GeneralException;
use App\Permission;
use App\Repository\Helper\Response;
use App\Repository\Services\PermissionService;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    public function permissionsList(PermissionService $permissionService)
    {
        $permissions = $permissionService->permissionsList();
        return Response::body(compact('permissions'));
    }

    public function rolesList(PermissionService $permissionService)
    {
        $roles = $permissionService->rolesList();
        return Response::body(compact('roles'));
    }

    public function admin(User $user, Request $request)
    {
        if ($user['_id'] == Auth::user()['_id'])
            throw new GeneralException('انجام عملیات بر روی خود کاربر امکان پذیر نیست.', GeneralException::UNKNOWN_ERROR);
        $is_admin = $request->get('admin') ? true : false;
        $user['is_admin'] = $is_admin;
        $user->save();
        return Response::body(['success' => true]);
    }

    public function setPermission(User $user, $id, PermissionService $permissionService)
    {
        if ($user['_id'] == Auth::user()['_id'])
            throw new GeneralException('انجام عملیات بر روی خود کاربر امکان پذیر نیست.', GeneralException::UNKNOWN_ERROR);
        $permission = $permissionService->loadById($id);
        if (!$permission)
            throw new GeneralException('یافت نشد.', GeneralException::NOT_FOUND);
        Permission::create([
            'user_id' => $user['_id'],
            'permission' => $permission
        ]);
        return Response::body(['success' => true]);

    }

}
