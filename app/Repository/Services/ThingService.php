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
use App\Project;
use App\Thing;
use App\ThingProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ThingService
{
    protected $loraService;
    protected $coreService;
    protected $projectService;
    protected $lanService;

    public function __construct(
        LoraService $loraService,
        CoreService $coreService,
        LanService $lanService,
        ProjectService $projectService
    )
    {
        $this->loraService = $loraService;
        $this->coreService = $coreService;
        $this->lanService = $lanService;
        $this->projectService = $projectService;
    }

    /**
     * @param $request
     * @return void
     * @throws GeneralException
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
            'devEUI.min' => 'لطفا devEUI سنسور را درست وارد کنید',
            'devEUI.max' => 'لطفا devEUI سنسور را درست وارد کنید',
            'devEUI.required' => 'لطفا devEUI سنسور را وارد کنید',
            'thing_profile_slug.required' => 'لطفا شناسه پروفایل شی را وارد کنید',
        ];
        $rules = [
            'name' => 'required|string|max:255',
            'devEUI' => 'required|min:16|max:16',
            'type' => 'required|',
            'lat' => 'required|numeric',
            'long' => 'required|numeric',
            'period' => 'required|numeric',
        ];
        if ($request->get('type') == 'lora') {
            $rules['thing_profile_slug'] = 'required';
            $messages['thing_profile_slug.required'] = 'لطفا شناسه پروفایل شی را وارد کنید';
        } else {
            $rules['ip'] = 'required|ip';
            $messages['ip.required'] = 'لطفا شناسه IP شی را وارد کنید';
            $messages['ip.ip'] = 'لطفا شناسه IP شی را درست وارد کنید';
        }

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails())
            throw new  GeneralException($validator->errors()->first(), GeneralException::VALIDATION_ERROR);
        if (Thing::where('dev_eui', $request->get('devEUI'))->first())
            throw new  GeneralException('این DEV EUI قبلا ثبت شده است.', GeneralException::VALIDATION_ERROR);
    }


    /**
     * @param Request $request
     * @return void
     * @throws GeneralException
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

        $extension = $request->file('things')->clientExtension();
        if ($validator->fails())
            throw new  GeneralException($validator->errors()->first(), GeneralException::VALIDATION_ERROR);
        if ($extension != 'csv' && $extension != 'xls' && $extension != 'xlsx')
            throw new  GeneralException('لطفا فایل با فرمت اکسل انتخاب کنید', GeneralException::VALIDATION_ERROR);
    }

    /**
     * @param $request
     * @param Project $project
     * @param ThingProfile $thingProfile
     * @return void
     * @throws GeneralException
     * @throws LoraException
     */
    public function insertThing($request, Project $project = null, ThingProfile $thingProfile = null)
    {
        $lora = $request->get('type') == 'lora';
        if (!$thingProfile && $lora)
            throw new GeneralException('پروفایل شی یافت نشد', 700);
        if (!$project)
            throw new GeneralException('پروژه یافت نشد', 700);

        if ($lora)
            $device = $this->loraService->postDevice(
                $request,
                $project['application_id'],
                $thingProfile['device_profile_id']
            );
        else
            $device = $this->lanService->postDevice($request);

        $thing = Thing::create([
            'name' => $request->get('name'),
            'description' => $request->get('description') ?: '',
            'period' => $request->get('period'),
            'dev_eui' => $request->get('devEUI'),
            'active' => true,
            'interface' => $device->toArray(),
            'type' => $lora ? 'LoRa' : 'LAN',
            'activation' => $lora ? ($thingProfile['data']['deviceProfile']['supportsJoin'] ? 'OTAA' : 'ABP') : 'JWT',
            'keys' => $lora ? [] : ['JWT' => $device['token']],
            'loc' => [
                'type' => 'Point',
                'coordinates' => [$request->get('lat'), $request->get('long')]
            ],
        ]);
        if ($lora)
            $thing->profile()->associate($thingProfile);
        return $thing;
    }


    public function ABPKeys($request, Thing $thing)
    {
        $validator = Validator::make($request->all(), [
            'devAddr' => 'required',
            'nwkSKey' => 'required',
            'appSKey' => 'required',
        ]);
        if ($validator->fails())
            throw new GeneralException('اطلاعات فعال سازی را کامل وارد کنید', 407);
        $data['devAddr'] = (string)$request->get('devAddr');
        $data['nwkSKey'] = (string)$request->get('nwkSKey');
        $data['appSKey'] = (string)$request->get('appSKey');
        $data['fCntUp'] = intval($request->get('fCntUp', 0));
        $data['fCntDown'] = intval($request->get('fCntDown', 0));
        $data['skipFCntCheck'] = $request->get('skipFCntCheck') === 'true' ? true : false;
        $data['devEUI'] = $thing['interface']['devEUI'];
        $this->loraService->activateDevice($data);
        return $data;
    }

    public function OTAAKeys($request, Thing $thing)
    {
        $key = $request->get('appKey');
        $data = ['deviceKeys' => ['appKey' => $key]];
        $data['devEUI'] = $thing['interface']['devEUI'];
        $this->loraService->SendKeys($data);
        return $data['deviceKeys'];
    }

    public function JWTKey(Thing $thing)
    {
        $result = $this->lanService->getKey($thing);
        return ['JWT' => $result['token']];
    }

    public function activate(Thing $thing, $active)
    {
        $this->coreService->activateThing($thing, $active);
        $thing->active = $active;
        $thing->save();
    }


    /**
     * @param Collection $request
     * @param Thing $thing
     * @return $this|\Illuminate\Database\Eloquent\Model
     * @throws LoraException
     */
    public function updateThing(Collection $request, Thing $thing)
    {
        $lora_data = [];
        if ($request->get('name')) {
            $thing->name = $request->get('name');
            $lora_data['name'] = (string)$request->get('name');
        }

        if ($request->get('description')) {
            $thing->description = $request->get('description');
            $lora_data['description'] = (string)$request->get('description');
        }

        if ($request->get('period'))
            $thing->period = $request->get('period');

        if ($request->get('thing_profile_slug')) {
            $profile = ThingProfile::where('thing_profile_slug', (int)$request->get('thing_profile_slug'))->first();

            if ($profile && Auth::user()->can('view', $profile)) {
                $lora_data['deviceProfileID'] = (string)$profile['device_profile_id'];
                $thing['profile_id'] = $profile['_id'];
                $thing['type'] = $profile['data']['deviceProfile']['supportsJoin'] ? 'OTAA' : 'ABP';
            } else
                $lora_data['deviceProfileID'] = (string)$thing['profile']['device_profile_id'];
        }


        if ($request->get('lat') && $request->get('long'))
            $thing->loc = [
                'type' => 'Point',
                'coordinates' => [$request->get('lat'), $request->get('long')]
            ];
        $this->loraService->updateDevice($lora_data, $thing['dev_eui']);
        $thing->save();

        return $thing;
    }


    /**
     * @param $things
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function toExcel($things)
    {
        $excel = resolve('Maatwebsite\Excel\Excel');
        $res = [[
            'operation',
            'name',
            'type',
            'description',
            'lat',
            'long',
            'period',
            'devEUI',
            'thing_profile_slug',
            'appKey',
            'appSKey',
            'nwkSKey',
            'devAddr',
            'fCntDown',
            'fCntUp',
            'skipFCntCheck',

        ]];
        $res = array_merge($res, $things->map(function ($item) {
            return [
                'add',
                $item['name'],
                'lora',
                $item['description'],
                $item['loc']['coordinates'][0],
                $item['loc']['coordinates'][1],
                $item['period'],
                $item['dev_eui'],
                $item['profile']['thing_profile_slug'],
                isset($item['keys']['appKey']) ? $item['keys']['appKey'] : '',
                isset($item['keys']['appSKey']) ? $item['keys']['appSKey'] : '',
                isset($item['keys']['nwkSKey']) ? $item['keys']['nwkSKey'] : '',
                isset($item['keys']['devAddr']) ? $item['keys']['devAddr'] : '',
                isset($item['keys']['fCntDown']) ? $item['keys']['fCntDown'] : '',
                isset($item['keys']['fCntUp']) ? $item['keys']['fCntUp'] : '',
                isset($item['keys']['skipFCntCheck']) && $item['keys']['skipFCntCheck'] ? 'true' : '',
            ];
        })->toArray());

        return response(
            $excel->create(
                'things.csv',
                function ($excel) use ($res) {
                    $excel->sheet(
                        'Things',
                        function ($sheet) use ($res) {
                            $sheet->fromArray($res, null, 'A1', false, false);
                        }
                    );
                }
            )->string('csv')
        )
            ->header('Content-Disposition', 'attachment; filename="things.csv"')
            ->header('Content-Type', 'application/csv; charset=UTF-8');
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
