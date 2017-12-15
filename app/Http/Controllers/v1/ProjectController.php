<?php

namespace App\Http\Controllers\v1;

use App\Exceptions\ProjectException;
use App\Project;
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

    /**
     * @return array
     */
    public function all()
    {
        $projects = Auth::user()->projects()->get();
        $projects = $projects->map(function ($item) {
            return $item->only(['_id', 'name']);
        });
        return Response::body(compact('projects'));
    }

    /**
     * @param Project $project
     * @return array
     */
    public function get(Project $project)
    {
        $user = Auth::user();
        if ($project->user_id == $user->id)
            return Response::body(compact('project'));
        abort(404);
    }
}
