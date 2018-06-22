<?php
/**
 * Created by PhpStorm.
 * User: sajjad
 * Date: 2/6/18
 * Time: 12:43 AM
 */

namespace App\Repository\Services;

use App\Permission;
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
        return DB::collection('roles')->get()->map(function ($item, $key) {
            $item['_id'] = (string)($item['_id']);
            $item['permissions'] = DB::collection('permissions')->whereIn('_id', $item['permissions'])->get()->map(function ($item, $key) {
                $item['_id'] = (string)($item['_id']);
                return $item;
            });
            return $item;
        });;
    }

    public function loadById($id)
    {
        $permission = DB::collection('permissions')->where('_id', $id)->first();
        if (!$permission)
            return [];
        $permission['_id'] = (string)($permission['_id']);
        return $permission;
    }
}