<?php

namespace App\Http\Controllers\v1;

use App\Exceptions\ProjectException;
use App\Repository\Helper\Response;
use App\Repository\Services\ProjectService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    protected $projectService;

    /**
     * ProjectController constructor.
     * @param ProjectService $projectService
     */
    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }


    /**
     * @param Request $request
     * @return array
     * @throws ProjectException
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $this->projectService->validateCreateProject($request);

        $project = $this->projectService->insertProject($request);

        $user->projects()->save($project);
        return Response::body(compact('project'));
    }
}
