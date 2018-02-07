<?php
/**
 * Created by PhpStorm.
 * User: sajjad
 * Date: 2/6/18
 * Time: 12:43 AM
 */

namespace App\Repository\Services;

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
        });;
    }

    public function rolesList()
    {
        return DB::collection('roles')->get()->map(function ($item, $key) {
            $item['_id'] = (string)($item['_id']);
            return $item;
        });;
    }
}