<?php

namespace App\Http\Controllers;

use App\Http\Controllers\v1\ThingController;
use App\Project;
use App\Repository\Services\LoraService;
use App\Repository\Services\PermissionService;
use App\Thing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    public function delete()
    {
        $things = Thing::all();
        $a = app('App\Http\Controllers\v1\ThingController');
        foreach ($things as $thing) {
            try {
                $a->delete($thing);
            } catch (\Exception $e) {
            }
        }
    }

    public function index(Request $request){

    }

}
