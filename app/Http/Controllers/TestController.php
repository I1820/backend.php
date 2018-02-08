<?php

namespace App\Http\Controllers;

use App\Project;
use App\Repository\Services\LoraService;
use App\Repository\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    public function index(LoraService $loraService)
    {
        $user = Auth::user();
        dd($user->permissions()->where('item_type','project')->with('project')->get()->toArray());
        return $loraService->postDevice(collect([]));
    }

}
