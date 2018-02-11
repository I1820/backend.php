<?php

namespace App\Http\Controllers\v1;

use App\Exceptions\GeneralException;
use App\Exceptions\LoraException;
use App\Repository\Helper\Response;
use App\Repository\Services\CoreService;
use App\Repository\Services\GatewayService;
use App\Repository\Services\LoraService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use MongoDB\BSON\ObjectId;

class GatewayController extends Controller
{
    protected $coreService;
    protected $loraService;
    protected $gatewayService;

    /**
     * ProjectController constructor.
     * @param GatewayService $gatewayService
     * @param CoreService $coreService
     * @param LoraService $loraService
     */
    public function __construct(GatewayService $gatewayService,
                                CoreService $coreService,
                                LoraService $loraService)
    {
        $this->coreService = $coreService;
        $this->loraService = $loraService;
        $this->gatewayService = $gatewayService;
    }


    /**
     * @param Request $request
     * @return array
     * @throws GeneralException
     */
    public function create(Request $request)
    {
        $this->gatewayService->validateCreateGateway($request);
        $name = (string)(new ObjectId());
        $core_info = $this->coreService->sendGateway(['address' => $request->get('address'), 'name' => $name]);
        $core_info = (array)$core_info;
        $core_info['name'] = $name;
        $lora_address = 'tcp://' . config('iot.core.serverBaseUrl') . ':' . $core_info['port'];
        try {
            $lora_info = $this->loraService->sendNetworkServer(['address' => $lora_address, 'name' => $name]);
        } catch (LoraException $e) {
            $lora_info = [];
        }
        $gateway = $this->gatewayService->insertGateway($request, $lora_info, $core_info);
        return Response::body(compact('gateway'));
    }


    /**
     * @return array
     */
    public function list()
    {
        $user = Auth::user();

        $gateway = $user->gateways()->get();

        return Response::body(compact('gateway'));
    }


}
