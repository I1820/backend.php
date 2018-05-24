<?php

namespace App\Http\Controllers;

use App\Discount;
use App\Http\Controllers\v1\ThingController;
use App\Project;
use App\Repository\Services\LoraService;
use App\Repository\Services\PermissionService;
use App\Thing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    public function index(Request $request)
    {
        Discount::create([
            'value' => 2000,
            'expired' => false,
            'code' => substr(uniqid(), 0, 10)
        ]);
        Discount::create([
            'value' => 2000,
            'expired' => false,
            'code' => substr(uniqid(), 0, 10)
        ]);

    }

}
