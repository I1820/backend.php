<?php
/**
 * Created by PhpStorm.
 * User: sajjad
 * Date: 12/20/17
 * Time: 4:50 PM
 */

namespace App\Repository\Services;


use App\Exceptions\ThingException;
use App\Thing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ThingService
{
    protected $loraService;
    protected $coreService;

    public function __construct(LoraService $loraService, CoreService $coreService)
    {
        $this->loraService = $loraService;
        $this->coreService = $coreService;
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
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|',
            'description' => 'string',
            'lat' => 'required|numeric',
            'long' => 'required|numeric',
            'period' => 'required|numeric',
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
     * @return $this|\Illuminate\Database\Eloquent\Model
     * @throws \App\Exceptions\LoraException
     */
    public function insertThing($request)
    {
        $device_profile_id = $this->loraService->postDeviceProfile(collect($request->all()))->deviceProfileID;

        $device = $this->loraService->postDevice(collect($request->all())->merge(['deviceProfileID' => $device_profile_id]));
        // TODO
        $fakeData = [
            "appSKey" => "2b7e151628aed2a6abf7158809cf4f3c",
            "devAddr" => "00000035",
            "devEUI" => $request->get('devEUI'),
            "fCntDown" => 0,
            "fCntUp" => 0,
            "nwkSKey" => "2b7e151628aed2a6abf7158809cf4f3c",
            "skipFCntCheck" => true
        ];
        $info = $this->loraService->activateDevice($fakeData);
        return Thing::create([
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'interface' => $device->toArray(),
            'period' => $request->get('period'),
            'loc' => [
                'type' => 'Point',
                'coordinates' => [$request->get('lat'), $request->get('long')]
            ],
        ]);
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

}