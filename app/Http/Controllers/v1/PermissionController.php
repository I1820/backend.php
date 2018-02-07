<?php

namespace App\Http\Controllers\v1;

use App\Repository\Services\PermissionService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PermissionController extends Controller
{
    public function permissionsList(PermissionService $permissionService){
        return $permissionService->permissionsList();
    }

    public function rolesList(PermissionService $permissionService){
        return $permissionService->rolesList();
    }
}
