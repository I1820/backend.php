<?php

namespace App\Http\Controllers;

use App\Repository\Services\Core\PMCoreService;

class LogController extends Controller
{
    protected $pmService;

    public function __construct(PMCoreService $pmService)
    {
        $this->pmService = $pmService;
    }

    public function projects()
    {
        $projects = $this->pmService->list();
        return view('projects', ['projects' => $projects]);
    }

    public function project(string $id)
    {
        $logs = $this->pmService->logs($id, 10);
        return view('project', ['logs' => $logs]);
    }
}
