<?php

namespace App\Http\Controllers\v1;

use App\Exceptions\ProjectException;
use App\Project;
use App\Repository\Helper\Response;
use App\Repository\Services\ProjectService;
use App\Role;
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
        $role = Role::create(['permissions' => ['owner' => '']]);
        $project->roles()->save($role);
        $user->roles()->save($role);

        return Response::body(compact('project'));
    }

    /**
     * @return array
     */
    public function all()
    {
        $projects = collect(Auth::user()->roles()->with(['project.roles'])->get());
        $projects = $projects->map(function ($item) {
            return $item['project'];
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
        if ($project['owner']['id'] == $user->id)
            return Response::body(compact('project'));
        abort(404);
    }


    /**
     * @param Project $project
     * @param Request $request
     * @return array
     * @throws ProjectException
     */
    public function update(Request $request, Project $project)
    {
        $user = Auth::user();
        if ($project['owner']['id'] != $user->id)
            abort(404);

        $this->projectService->validateUpdateProject($request);

        $project = $this->projectService->updateProject($request, $project);


        return Response::body(compact('project'));
    }
}
