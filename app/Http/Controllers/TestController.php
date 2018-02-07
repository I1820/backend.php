<?php

namespace App\Http\Controllers;

use App\Project;
use App\Repository\Services\LoraService;
use App\Repository\Services\PermissionService;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index(LoraService $loraService)
    {
        $project = Project::first()->get()->first();
        dd($project->toArray());
        return $loraService->postDevice(collect([]));
    }

}
