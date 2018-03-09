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
        $user = Auth::user();
        if ($project['owner']['_id'] != $user->id)
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
        $user = Auth::user();
        if ($scenario->user()->first()['id'] != $user->id)
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
        $user = Auth::user();
        if ($scenario->user()->first()['id'] != $user->id)
            abort(404);
        $scenario->load('project');
        $this->coreService->sendScenario($project, $scenario);
        return Response::body(compact('scenario'));
    }


    /**
     * @param Project $project
     * @return array
     */
    public function list(Project $project)
    {
        $user = Auth::user();
        if ($project['owner']['_id'] != $user->id)
            abort(404);
        $scenarios = $project->scenarios()->get();

        return Response::body(compact('scenarios'));
    }
}
