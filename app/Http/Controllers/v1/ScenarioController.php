<?php

namespace App\Http\Controllers\v1;

use App\Project;
use App\Repository\Helper\Response;
use App\Repository\Services\CoreService;
use App\Repository\Services\ScenarioService;
use App\Scenario;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\GeneralException;
use Illuminate\Support\Facades\Auth;

class ScenarioController extends Controller
{
    protected $scenarioService;
    protected $coreService;

    public function __construct(ScenarioService $scenarioService,
                                CoreService $coreService)
    {
        $this->scenarioService = $scenarioService;
        $this->coreService = $coreService;
    }

    /**
     * @param Request $request
     * @param Project $project
     * @return array
     * @throws GeneralException
     */
    public function create(Request $request, Project $project)
    {
        if (!$this->custom_authorize($project))
            abort(404);
        $this->scenarioService->validateCreateScenario($request);
        $scenario = $this->scenarioService->insertScenario($request, $project);
        $this->coreService->sendScenario($project, $scenario);
        return Response::body(compact('scenario'));
    }


    /**
     * @param Project $project
     * @param Scenario $scenario
     * @return array
     */
    public function get(Project $project, Scenario $scenario)
    {
        if (!$this->custom_authorize($project, $scenario))
            abort(404);
        $scenario->load('project');
        return Response::body(compact('scenario'));
    }

    /**
     * @param Project $project
     * @param Scenario $scenario
     * @return array
     * @throws GeneralException
     */
    public function activate(Project $project, Scenario $scenario)
    {
        if (!$this->custom_authorize($project, $scenario))
            abort(404);
        $scenario->load('project');
        $this->coreService->sendScenario($project, $scenario);
        $project->scenarios()->update(['is_active' => false]);
        $scenario->is_active = true;
        $scenario->save();
        return Response::body(compact('scenario'));
    }


    /**
     * @param Project $project
     * @param Scenario $scenario
     * @param Request $request
     * @return array
     */
    public function update(Project $project, Scenario $scenario, Request $request)
    {
        if (!$this->custom_authorize($project, $scenario))
            abort(404);
        if($request->get('name'))
            $scenario->name = $request->get('name');
        if($request->get('code'))
            $scenario->code = $request->get('code');
        $scenario->save();
        return Response::body(compact('scenario'));
    }

    /**
     * @param Project $project
     * @param Scenario $scenario
     * @return array
     * @throws GeneralException
     * @throws \Exception
     */
    public function delete(Project $project, Scenario $scenario)
    {
        if (!$this->custom_authorize($project, $scenario))
            abort(404);
        if ($scenario->is_active)
            throw new GeneralException('سناریو فعال است', 403);
        $scenario->delete();
        return Response::body(['success' => 'true']);
    }


    /**
     * @param Project $project
     * @return array
     */
    public function list(Project $project)
    {

        if (!$this->custom_authorize($project))
            abort(404);
        $scenarios = $project->scenarios()->get();

        return Response::body(compact('scenarios'));
    }

    public function custom_authorize(Project $project, Scenario $scenario = null)
    {
        $user = Auth::user();
        if ($project['owner']['_id'] != $user->id)
            return false;
        if ($scenario && $scenario->user()->first()['id'] != $user->id)
            return false;
        if ($scenario && $scenario['project_id'] != $project['_id'])
            return false;
        return true;


    }
}
