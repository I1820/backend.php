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
use Maatwebsite\Excel\Excel;

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
            'altitude.required' => 'لطفا ارتفاع را وارد کنید',
            'name.required' => 'لطفا نام درگاه را وارد کنید',
            'mac.required' => 'لطفا آدرس فیزیکی درگاه را وارد کنید',
            'latitude.required' => 'لطفا مختصات جغرافیایی درگاه را وارد کنید',
            'longitude.required' => 'لطفا مختصات جغرافیایی درگاه را وارد کنید',

        ];

        $validator = Validator::make($request->all(), [
            'altitude' => 'required',
            'name' => 'required',
            'mac' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ], $messages);

        if ($validator->fails())
            throw new  GeneralException($validator->errors()->first(), 700);
    }

    /**
     * @param Request $request
     * @return void
     * @throws GeneralException
     */
    public function insertGateway(Request $request, $id)
    {
        $user = Auth::user();
        $old = $user->gateways()->where('mac', $request->get('mac'))->get();
        if (count($old))
            throw new GeneralException('این Gateway قبلا وجود دارد', 706);
        $gateway = Gateway::create([
            '_id' => $id,
            'user_id' => $user['_id'],
            'name' => $request->get('name'),
            'altitude' => $request->get('altitude'),
            'description' => $request->get('description'),
            'mac' => $request->get('mac'),
            'loc' => [
                'type' => 'Point',
                'coordinates' => [$request->get('longitude'), $request->get('latitude')]
            ],
        ]);

        return $gateway;
    }

    /**
     * @param $gateways
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function toExcel($gateways)
    {
        $excel = resolve(Excel::class);
        $res = [[
            '#',
            'name',
            'mac',
            'altitude',
            'description',
            'lat',
            'long',
        ]];
        $res = array_merge($res, $gateways->map(function ($item, $key) {
            return [
                $key + 1,
                $item['name'],
                $item['mac'],
                $item['altitude'],
                $item['description'],
                $item['loc']['coordinates'][0],
                $item['loc']['coordinates'][1],
            ];
        })->toArray());

        return response(
            $excel->create(
                'gateways.xls',
                function ($excel) use ($res) {
                    $excel->sheet(
                        'Gateways',
                        function ($sheet) use ($res) {
                            $sheet->fromArray($res, null, 'A1', false, false);
                        }
                    );
                }
            )->string('xls')
        )
            ->header('Content-Disposition', 'attachment; filename="gateways.xls"')
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8');
    }


}
