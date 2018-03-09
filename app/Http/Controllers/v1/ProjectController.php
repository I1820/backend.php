<?php

namespace App\Http\Controllers\v1;

use App\Exceptions\GeneralException;
use App\Exceptions\ProjectException;
use App\Project;
use App\Repository\Helper\Response;
use App\Repository\Services\CoreService;
use App\Repository\Services\PermissionService;
use App\Repository\Services\ProjectService;
use App\Permission;
use App\Thing;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    protected $projectService;
    protected $permissionService;
    protected $coreService;

    /**
     * ProjectController constructor.
     * @param ProjectService $projectService
     * @param PermissionService $permissionService
     * @param CoreService $coreService
     */
    public function __construct(ProjectService $projectService,
                                PermissionService $permissionService,
                                CoreService $coreService)
    {
        $this->projectService = $projectService;
        $this->permissionService = $permissionService;
        $this->coreService = $coreService;
    }


    /**
     * @param Project $project
     * @return array
     * @throws GeneralException
     * @throws \Exception
     */
    public function stop(Project $project)
    {
        $things = $project->things()->get();
        if(count($things))
            throw new GeneralException('Delete Things and then try',400);
        $response = $this->coreService->deleteProject($project->container['name']);
        $project->permissions()->delete();
        $project->delete();
        return Response::body(compact('response'));
    }


    /**
     * @param Request $request
     * @return array
     * @throws GeneralException
     * @throws ProjectException
     * @throws \App\Exceptions\LoraException
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $this->projectService->validateCreateProject($request);

        $project = $this->projectService->insertProject($request);
        $owner_permission = $this->permissionService->get('PROJECT-OWNER');
        $permission = Permission::create([
            'name' => $owner_permission['name'],
            'permission_id' => (string)$owner_permission['_id'],
            'item_type' => 'project',
        ]);
        $project->permissions()->save($permission);
        $user->permissions()->save($permission);

        return Response::body(compact('project'));
    }

    /**
     * @return array
     */
    public function all()
    {
        $projects = collect(Auth::user()->projects()->get());
        $projects = $projects->map(function ($item) {
            return $item['project'];
        });
        return Response::body(compact('projects'));
    }

    /**
     * @param Project $project
     * @return array
     */
    public function things(Project $project)
    {
        $things = $project->things()->get();
        return Response::body(compact('things'));
    }

    /**
     * @param Project $project
     * @return array
     */
    public function get(Project $project)
    {
        $user = Auth::user();
        if ($project['owner']['id'] != $user->id)
            abort(404);
        $project->load(['things', 'scenarios']);
        foreach ($project['scenarios'] as $scenario)
            if ($scenario['is_active'] == true) {
                $project['scenario'] = $scenario;
                unset($project['scenarios']);
                break;
            }

        return Response::body(compact('project'));
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
        $project->load('things');

        return Response::body(compact('project'));
    }

}
