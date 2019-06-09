<?php

namespace App\Http\Controllers\v1;

use App\Exceptions\CoreException;
use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Repository\Helper\Response;
use App\Repository\Services\Core\GMCoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OtherController extends Controller
{
    protected $gmService;

    public function __construct(GMCoreService $gmService)
    {
        $this->gmService = $gmService;
    }

    /**
     * @param Request $request
     * @return array
     * @throws GeneralException
     * @throws CoreException
     */
    public function decryptPhyPayload(Request $request)
    {
        $messages = [
            'appskey.required' => 'لطفا کلید اپلیکسشن(appskey) را وارد کنید',
            'netskey.required' => 'لطفا کلید شبکه(netskey) را وارد کنید',
            'phyPayload.required' => 'لطفا داده رمز شده را وارد کنید',
        ];

        $validator = Validator::make($request->all(), [
            'appskey' => 'required',
            'netskey' => 'required',
            'phyPayload' => 'required',
        ], $messages);

        if ($validator->fails())
            throw new  GeneralException($validator->errors()->first(), GeneralException::VALIDATION_ERROR);

        $data = $request->only(['appskey', 'netskey', 'phyPayload']);
        return Response::body($this->gmService->decrypt($data['appskey'], $data['netskey'], $data['phyPayload']));
    }
}
