<?php
/**
 * Created by PhpStorm.
 * User: sajjad
 * Date: 2/6/18
 * Time: 12:43 AM
 */

namespace App\Repository\Services;

use App\Permission;
use App\Role;
use App\User;
use Illuminate\Support\Facades\DB;

class PermissionService
{
    public function __construct()
    {
    }

    public function get($name)
    {
        return DB::collection('permissions')->where('name', $name)->first();
    }

    public function permissionsList()
    {
        return DB::collection('permissions')->get()->map(function ($item, $key) {
            $item['_id'] = (string)($item['_id']);
            return $item;
        });
    }

    public function rolesList()
    {
        $roles = Role::all();
        foreach ($roles as $role) {
            $role['permissions'] = Permission::whereIn('_id', $role['permissions'])->get();
        }
        return $roles;
    }

    public function getRole($id)
    {
        $role = Role::wehre('_id', $id)->first();
        if($role)
            $role['permissions'] = Permission::whereIn('_id', $role['permissions'])->get();
        else return false;
        return $role;

    }

    public function loadById($id)
    {
        if (is_array($id))
            $permission = DB::collection('permissions')->whereIn('_id', $id)->get();
        else
            $permission = DB::collection('permissions')->where('_id', $id)->get();
        if (!$permission)
            return collect([]);
        $permission = $permission->map(function ($item) {
            $item['_id'] = (string)($item['_id']);
            return $item;
        });
        return $permission;
    }
}