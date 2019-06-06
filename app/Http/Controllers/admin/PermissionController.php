<?php

namespace App\Http\Controllers\admin;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Permission;
use App\Repository\Helper\Response;
use App\Repository\Services\PermissionService;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    public function permissionsList()
    {
        $permissions = Permission::all();
        return Response::body(compact('permissions'));
    }

    public function rolesList(PermissionService $permissionService)
    {
        $roles = $permissionService->rolesList();
        return Response::body(compact('roles'));
    }


    public function createRole(Request $request)
    {
        $permission_ids = json_decode($request->get('permissions_ids'));
        $name = $request->get('name');
        if (!$permission_ids)
            throw new GeneralException('پرمیشن‌ها رو وارد کنید', GeneralException::VALIDATION_ERROR);
        if (!$name || $name == 'admin')
            throw new GeneralException('نام گروه کاربری مجاز نمی‌باشد.', GeneralException::VALIDATION_ERROR);
        $permissions = Permission::whereIn('_id', $permission_ids)->get();
        if (!$permissions)
            throw new GeneralException('ایجاد گروه کاربری بدون پرمیشن امکان پذیر نیست.', GeneralException::VALIDATION_ERROR);
        $role = Role::create([
            'name' => $name,
            'permissions' => $permissions->pluck('_id')->toArray(),
        ]);
        return Response::body(compact('role'));

    }

    public function setRole(User $user, Role $role = null)
    {
        if ($user['_id'] == Auth::user()['_id'])
            throw new GeneralException('انجام عملیات بر روی خود کاربر امکان پذیر نیست.', GeneralException::UNKNOWN_ERROR);
        if (!$role) {
            $user['is_admin'] = true;
            $user['role_id'] = null;
        } else {
            $user['is_admin'] = false;
            $user['role_id'] = $role['_id'];
        }
        $user->save();
        return Response::body(['success' => true]);
    }

    public function deleteRole(Role $role)
    {
        if ($role['default']) {
            throw new GeneralException('نقش پیش فرض را نمیتوان حذف کرد', GeneralException::ACCESS_DENIED);
        }
        if ($role->users()->first())
            throw new GeneralException('ابتدا نقش کاربران دارای این نقش را تغییر دهید.', GeneralException::ACCESS_DENIED);
        $role->delete();
        return Response::body(['success' => true]);
    }

    public function updateRole(Role $role, Request $request)
    {
        $permission_ids = json_decode($request->get('permissions_ids'));
        $name = $request->get('name');
        if (!$permission_ids)
            throw new GeneralException('پرمیشن‌ها رو وارد کنید', GeneralException::VALIDATION_ERROR);
        if (!$name)
            throw new GeneralException('لطفا نام گروه کاربری را وارد کنید.', GeneralException::VALIDATION_ERROR);
        $permissions = Permission::whereIn('_id', $permission_ids)->get();
        if (!$permissions)
            throw new GeneralException('به روز رسانی گروه کاربری بدون پرمیشن امکان پذیر نیست.', GeneralException::VALIDATION_ERROR);
        $role['name'] = $name;
        $role['permissions'] = $permissions->pluck('_id')->toArray();
        $role->save();
        return Response::body(compact('role'));
    }

}
