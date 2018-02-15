<?php

namespace App\Http\Controllers;

use App\Repository\Services\CoreService;
use Illuminate\Http\Request;

class LogController extends Controller
{
    protected $coreService;

    public function __construct(CoreService $coreService)
    {
        $this->coreService = $coreService;
    }

    public function containers()
    {
        $containers = $this->coreService->projectList();
        return view('containers', ['containers' => $containers]);
    }

    public function containerLog($id){
        $logs = $this->coreService->projectLogs($id);
        return view('containers-logs', ['logs' => $logs]);
    }
}
