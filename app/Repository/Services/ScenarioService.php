<?php
/**
 * Created by PhpStorm.
 * User: sajjad
 * Date: 09/2/18
 * Time: 2:56 PM
 */

namespace App\Repository\Services;


use App\Exceptions\GeneralException;
use App\Project;
use App\Scenario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ScenarioService
{

    /**
     * @param Request $request
     * @return void
     * @throws GeneralException
     */
    public function validateCreateScenario(Request $request)
    {
        $messages = [
            'name.required' => 'لطفا نام سناریو را وارد کنید',
            'code.required' => 'لطفا سناریو را وارد کنید',

        ];

        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'name' => 'required|max:255'
        ], $messages);

        if ($validator->fails())
            throw new  GeneralException($validator->errors()->first(), 700);
    }

    /**
     * @param Request $request
     * @param Project $project
     * @return void
     */
    public function insertScenario(Request $request, Project $project)
    {
        $user = Auth::user();
        $scenario = Scenario::create([
            'name' => $request->get('name'),
            'code' => $request->get('code')
        ]);

        $scenario->user()->associate($user);
        $scenario->project()->associate($project);
        $scenario->save();
        $project->activeScenario($scenario);
        return $scenario;
    }


    /**
     * @param Request $request
     * @param Scenario $scenario
     * @return $this|Model
     */
    public function updateScenario(Request $request, Scenario $scenario)
    {
        $data = $request->only(['name', 'code']);
        $scenario->code = $data['code'];
        $scenario->name = $data['name'];
        $scenario->save();
        return $scenario;
    }
}