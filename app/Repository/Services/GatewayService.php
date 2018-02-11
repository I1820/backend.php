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
            'name.required' => 'لطفا نام سناریو را وارد کنید',
            'address.required' => 'لطفا آدرس گیت وی را وارد کنید',

        ];

        $validator = Validator::make($request->all(), [
            'address' => 'required',
            'name' => 'required'
        ], $messages);

        if ($validator->fails())
            throw new  GeneralException($validator->errors()->first(), 700);
    }

    /**
     * @param Request $request
     * @param $lora_info
     * @param $core_info
     * @return void
     */
    public function insertGateway(Request $request, $lora_info, $core_info)
    {
        $user = Auth::user();
        $gateway = Gateway::create([
            'name' => $request->get('name'),
            'address' => $request->get('address'),
            'lora_info' => $lora_info,
            'core_info' => $core_info,
        ]);

        $user->gateways()->save($gateway);
        return $gateway;
    }


}