<?php

namespace App\Http\Controllers;

use App\Repository\Services\LanService;
use Illuminate\Http\Request;
use Ixudra\Curl\CurlService;

class TestController extends Controller
{
    public function index(Request $request)
    {
        $curl = new CurlService();
        $lan = new LanService($curl);

//        return $lan->postDeviceProfile($request->get('name')); // postprofiledevice
        return $lan->postDevice(collect($request->all()), "5b13c9026abc2ad22c052e8c", "5b13d90570d071dcb1aa8a22"); //post device
//        return $lan->getDevices("5b13c9026abc2ad22c052e8c"); // list devices
//        return $lan->deleteDevice("5b13c9026abc2ad22c052e8c",12); // delete

    }

}
