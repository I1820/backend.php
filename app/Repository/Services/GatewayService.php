<?php
/**
 * Created by PhpStorm.
 * User: Sajjad
 * Date: 02/7/18
 * Time: 11:42 AM
 */

namespace App\Repository\Services;

use App\Exceptions\GeneralException;
use App\Gateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class GatewayService
{

    /**
     * @param Request $request
     * @return void
     * @throws GeneralException
     */
    public function validateCreateGateway(Request $request)
    {
        $messages = [
            'altitude.required' => 'لطفا altitude را وارد کنید',
            'name.required' => 'لطفا نام درگاه را وارد کنید',
            'mac.required' => 'لطفا آدرس فیزیکی درگاه را وارد کنید',
            'latitude.required' => 'لطفا مختصات جغرافیایی درگاه را وارد کنید',
            'longitude.required' => 'لطفا مختصات جغرافیایی درگاه را وارد کنید',
            'description.required' => 'لطفا توضیحات درگاه را وارد کنید',

        ];

        $validator = Validator::make($request->all(), [
            'altitude' => 'required',
            'name' => 'required',
            'mac' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'description' => 'required',
        ], $messages);

        if ($validator->fails())
            throw new  GeneralException($validator->errors()->first(), 700);
    }

    /**
     * @param Request $request
     * @param $id
     * @return void
     */
    public function insertGateway(Request $request, $id)
    {
        $gateway = Gateway::create([
            '_id' => $id,
            'name' => $request->get('name'),
            'altitude' => $request->get('altitude'),
            'description' => $request->get('description'),
            'mac' => $request->get('mac'),
            'loc' => [
                'type' => 'Point',
                'coordinates' => [$request->get('latitude'), $request->get('longitude')]
            ],
        ]);

        return $gateway;
    }


}