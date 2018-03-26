<?php

namespace App\Http\Controllers\v1;

use App\Exceptions\GeneralException;
use App\Project;
use App\Repository\Helper\Response;
use App\Repository\Services\CoreService;
use App\Repository\Services\LoraService;
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
    protected $loraService;

    /**
     * ProjectController constructor.
     * @param ProjectService $projectService
     * @param PermissionService $permissionService
     * @param CoreService $coreService
     * @param LoraService $loraService
     */
    public function __construct(ProjectService $projectService,
                                PermissionService $permissionService,
                                CoreService $coreService,
                                LoraService $loraService)
    {
        $this->projectService = $projectService;
        $this->permissionService = $permissionService;
        $this->coreService = $coreService;
        $this->loraService = $loraService;

        $this->middleware('can:view,project')->only(['get']);
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
        $this->loraService->deleteApp($project['application_id']);
        $project->permissions()->delete();
        $project->delete();
        return Response::body($response);
    }


    /**
     * @param Request $request
     * @return array
     * @throws GeneralException
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
//        $user = Auth::user();
//        if ($project['owner']['id'] != $user->id)
//            abort(404);
        $project->load(['things', 'scenarios']);
        $project['scenarios']->forget('code');
        $project['scenarios'] = $project['scenarios']->map(function ($item, $key) {
            unset($item['code']);
            return $item;
        });

        return Response::body(compact('project'));
    }


    /**
     * @param Request $request
     * @param Project $project
     * @return array
     * @throws GeneralException
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
