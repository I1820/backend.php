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

    /**
     * @param Request $request
     * @return void
     * @throws ThingException
     */
    public function validateCreateThing(Request $request)
    {
        $messages = [
            'name.required' => 'لطفا نام شی را وارد کنید',
            'mac_address.unique' => 'این شی قبلا اضافه شده',
            'mac_address.required' => 'ادرس فیزیکی شی را وارد کنید',
            'lat.numeric' => 'لطفا محل سنسور را درست وارد کنید',
            'lat.required' => 'لطفا محل سنسور را درست کنید',
            'long.numeric' => 'لطفا محل سنسور را درست وارد کنید',
            'long.required' => 'لطفا محل سنسور را وارد کنید',
            'period.required' => 'لطفا بازه ارسال داده سنسور را وارد کنید',
            'period.numeric' => 'لطفا بازه ارسال داده سنسور را درست وارد کنید',
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'mac_address' => 'required|unique:things',
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
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function insertThing(Request $request)
    {
        return Thing::create([
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'mac_address' => $request->get('mac_address'),
            'period' => $request->get('period'),
            'loc' => [
                'lat' => $request->get('lat'),
                'long' => $request->get('long'),
            ]
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