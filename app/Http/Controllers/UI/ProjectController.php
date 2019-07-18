<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Repository\Services\Core\PMCoreService;

class ProjectController extends Controller
{
    protected $pmService;

    public function __construct(PMCoreService $pmService)
    {
        $this->pmService = $pmService;
    }

    /**
     * @return \Illuminate\View\View
     * @throws \App\Exceptions\CoreException
     */
    public function projects()
    {
        $projects = $this->pmService->list();
        return view('projects', ['projects' => $projects]);
    }

    /**
     * @param string $id
     * @return \Illuminate\View\View
     * @throws \App\Exceptions\CoreException
     */
    public function project(string $id)
    {
        $logs = $this->pmService->logs($id, 10);
        return view('project', ['logs' => $logs]);
    }
}
