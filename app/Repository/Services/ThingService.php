<?php
/**
 * Created by PhpStorm.
 * User: sajjad
 * Date: 12/20/17
 * Time: 4:50 PM
 */

namespace App\Repository\Services;


use App\Exceptions\GeneralException;
use App\Exceptions\LoraException;
use App\Exceptions\ThingException;
use App\Project;
use App\Thing;
use App\ThingProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ThingService
{
    protected $loraService;
    protected $coreService;
    protected $projectService;

    public function __construct(LoraService $loraService,
                                CoreService $coreService,
                                ProjectService $projectService)
    {
        $this->loraService = $loraService;
        $this->coreService = $coreService;
        $this->projectService = $projectService;
    }

    /**
     * @param $request
     * @return void
     * @throws ThingException
     */
    public function validateCreateThing($request)
    {
        $messages = [
            'name.required' => 'لطفا نام شی را وارد کنید',
            'type.required' => 'نوع اینترفیس شی را وارد کنید',
            'lat.numeric' => 'لطفا محل سنسور را درست وارد کنید',
            'lat.required' => 'لطفا محل سنسور را درست کنید',
            'long.numeric' => 'لطفا محل سنسور را درست وارد کنید',
            'long.required' => 'لطفا محل سنسور را وارد کنید',
            'period.required' => 'لطفا بازه ارسال داده سنسور را وارد کنید',
            'period.numeric' => 'لطفا بازه ارسال داده سنسور را درست وارد کنید',
            'thing_profile_slug.required' => 'لطفا شناسه پروفایل شی را وارد کنید',
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|',
            'description' => 'string',
            'lat' => 'required|numeric',
            'long' => 'required|numeric',
            'period' => 'required|numeric',
            'thing_profile_slug' => 'required',
        ], $messages);

        if ($validator->fails())
            throw new  ThingException($validator->errors()->first(), ThingException::C_GE);
    }


    /**
     * @param Request $request
     * @return void
     * @throws ThingException
     */
    public function validateExcel(Request $request)
    {

        $messages = [
            'things.required' => 'لطفا فایل را انتخاب کنید',
            'things.mimes' => 'نوع فایل را درست وارد کنید',
        ];

        $validator = Validator::make($request->all(), [
            'things' => 'required|file',
        ], $messages);

        if ($validator->fails())
            throw new  ThingException($validator->errors()->first(), ThingException::C_GE);
    }

    /**
     * @param $request
     * @param Project $project
     * @param ThingProfile $thingProfile
     * @return void
     * @throws GeneralException
     * @throws LoraException
     */
    public function insertThing($request, Project $project, ThingProfile $thingProfile = null)
    {
        $request->merge(['devEUI' => $project->generateDevEUI()]);
        if (!$thingProfile)
            throw new GeneralException('Thing Profile Not found', 405);
        $device = $this->loraService->postDevice(collect($request->all()), $project['application_id']);
        $thing = Thing::create([
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'interface' => $device->toArray(),
            'period' => $request->get('period'),
            'type' => $thingProfile['data']['deviceProfile']['supportsJoin'] ? 'OTAA' : 'ABP',
            'loc' => [
                'type' => 'Point',
                'coordinates' => [$request->get('lat'), $request->get('long')]
            ],
        ]);
        $thing->profile()->associate($thingProfile);
        $this->addToProject($project, $thing);
        return $thing;
    }


    public function activateABP($request, Thing $thing)
    {
        $validator = Validator::make($request->all(), [
            'devAddr' => 'required',
            'nwkSKey' => 'required',
            'appSKey' => 'required',
            'fCntUp' => 'required',
            'fCntDown' => 'required',
        ]);
        if ($validator->fails())
            throw new GeneralException('اطلاعات را کامل وارد کنید', 407);
        $data = $request->only([
            'devAddr',
            'nwkSKey',
            'appSKey',
            'fCntUp',
            'fCntDown',
        ]);
        $data['fCntUp'] = intval($data['fCntUp']);
        $data['fCntDown'] = intval($data['fCntDown']);
        $data['skipFCntCheck'] = $request->get('skipFCntCheck') === '1' ? true : false;
        $data['devEUI'] = $thing['interface']['devEUI'];
        $info = $this->loraService->activateDevice($data);
        return $info;
    }

    public function activateOTAA($request, Thing $thing)
    {
        $data = ['deviceKeys' => ['appKey' => $request->get('appKey')]];
        $data['devEUI'] = $thing['interface']['devEUI'];
        $info = $this->loraService->SendKeys($data);
        return $info;
    }

    /**
     * @param Request $request
     * @return void
     * @throws ThingException
     */
    public function validateUpdateThing(Request $request)
    {
        $messages = [
            'name.filled' => 'لطفا نام شی را وارد کنید',
            'description.filled' => 'لطفا توضیحات را درست وارد کنید',
            'period.*' => 'لطفا بازه ارسال را درست وارد کنید',
            'lat.*' => 'لطفا محل سنسور را درست وارد کنید',
            'long.*' => 'لطفا محل سنسور را درست وارد کنید',
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'filled|string|max:255',
            'description' => 'filled|string',
            'period' => 'filled|numeric',
            'lat' => 'filled|numeric',
            'long' => 'filled|numeric',
        ], $messages);

        if ($validator->fails())
            throw new  ThingException($validator->errors()->first(), ThingException::C_GE);
    }

    /**
     * @param Request $request
     * @param Thing $thing
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function updateThing(Request $request, Thing $thing)
    {
        if ($request->get('name'))
            $thing->name = $request->get('name');

        if ($request->get('description'))
            $thing->description = $request->get('description');

        if ($request->get('period'))
            $thing->period = $request->get('period');

        if ($request->get('lat') && $request->get('long'))
            $thing->loc = [
                'lat' => $request->get('lat'),
                'long' => $request->get('long')
            ];

        $thing->save();

        return $thing;
    }


    /**
     * @param Project $project
     * @param Thing $thing
     * @throws GeneralException
     */
    public function addToProject(Project $project, Thing $thing)
    {
        $thing->project()->associate($project);
        $thing->save();
        $this->coreService->postThing($project, $thing);
    }

}