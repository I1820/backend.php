<?php
/**
 * Created by PhpStorm.
 * User: skings
 * Date: 1/16/18
 * Time: 10:27 AM
 */

namespace App\Http\Controllers\v1;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Mockery\Exception;
use Validator;

class PaymentController extends Controller
{
    private $base_url = '172.25.224.90:8090/PaymentServices.svc/rest';

    function setNewUser()
    {
        $user = Auth::user();
        if ($user != null) {
            $url = $this->base_url . "/CreateUser"
                . "/" . $user->id;
            return Curl::to($url)->post();
        }
        return response()->json(["result" => "unauthorized"], 401);
    }

    function getPackages()
    {
        $user = Auth::user();
        if ($user != null) {
            $url = $this->base_url . "/GetPackages/";
            return Curl::to($url)->get();
        }
        return response()->json(["result" => "forbidden"], 403);
    }

    function getUserPackages(Request $request)
    {
        $user = Auth::user();
        if ($user != null) {
            $validator = Validator::make($request->all(),
                [
                    'range' => 'required|integer',
                    'page' => 'required|integer'
                ]);
            if ($validator->fails()) {
                return response()->json(['result' => 'bad request'], 400);
            }

            $validatedData = $validator->valid();
            $url = $this->base_url . "/GetUserPackages"
                . "/" . $user->id
                . "/" . $validatedData['page']
                . "/" . $validatedData['range'];
            return Curl::to($url)->get();
        }
        return response()->json(["result" => "forbidden"], 403);
    }

    function updatePackage(Request $request)
    {
        $user = Auth::user();
        if ($user != null) {
            $validator = Validator::make($request->all(),
                [
                    'package_type' => 'required|string',
                    'cost' => 'required|integer',
                    'time' => 'required|integer',
                    'sensor' => 'required|integer',

                ]);
            if ($validator->fails()) {
                return response()->json(['result' => 'bad request'], 400);
            }

            $validatedData = $validator->valid();
            $url = $this->base_url . "/UpdatePackage"
                . "/" . $validatedData['package_type']
                . "/" . $validatedData['cost']
                . "/" . $validatedData['time']
                . "/" . $validatedData['sensor'];

            return Curl::to($url)->post();
        }
        return response()->json(["result" => "forbidden"], 403);
    }

    function createPackage(Request $request)
    {
        $user = Auth::user();
        if ($user != null) {
            $validator = Validator::make($request->all(),
                [
                    'package_type' => 'required|string',
                    'cost' => 'required|integer',
                    'time' => 'required|integer',
                    'sensor' => 'required|integer',

                ]);
            if ($validator->fails()) {
                return response()->json(['result' => 'bad request'], 400);
            }

            $validatedData = $validator->valid();
            $url = $this->base_url . "/CreatePackage"
                . "/" . $validatedData['package_type']
                . "/" . $validatedData['cost']
                . "/" . $validatedData['time']
                . "/" . $validatedData['sensor'];

            return Curl::to($url)->post();
        }
        return response()->json(["result" => "forbidden"], 403);
    }

    function updatePackageStatus(Request $request)
    {
        $user = Auth::user();
        if ($user != null) {
            $validator = Validator::make($request->all(),
                [
                    'status' => 'required|string',
                ]);
            if ($validator->fails()) {
                return response()->json(['result' => 'bad request'], 400);
            }

            $validatedData = $validator->valid();
            $url = $this->base_url . "/UpdatePackageStatus"
                . "/" . $user->id
                . "/" . $validatedData['status'];
            return Curl::to($url)->post();
        }
        return response()->json(["result" => "forbidden"], 403);
    }

    function getLastPackageStatus()
    {
        $user = Auth::user();
        if ($user != null) {
            $url = $this->base_url . "/GetLastPackageStatus"
                . "/" . $user->id;
            return Curl::to($url)->get();
        }
        return response()->json(["result" => "forbidden"], 403);
    }

    function getUserPackagesByStatus(Request $request)
    {
        $user = Auth::user();
        if ($user != null) {
            $validator = Validator::make($request->all(),
                [
                    'status' => 'required|string',
                ]);
            if ($validator->fails()) {
                return response()->json(['result' => 'bad request'], 400);
            }

            $validatedData = $validator->valid();
            $url = $this->base_url . "/GetUserDataByStatus"
                . "/" . $user->id
                . "/" . $validatedData['status'];
            return Curl::to($url)->get();
        }
        return response()->json(["result" => "forbidden"], 403);
    }


    function getUserTransactions(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(),
            [
                'status' => 'required|string',
            ]);
        if ($validator->fails()) {
            return response()->json(['result' => 'bad request'], 400);
        }
        $validatedData = $validator->valid();

        if ($user != null) {
            $url = $this->base_url . "/GetUserTransaction"
                . "/" . $user->id
                . "/" . $validatedData['status'];
            return Curl::to($url)->get();
        }
        return response()->json(["result" => "forbidden"], 403);
    }

    function getTransactions(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(),
            [
                'status' => 'required|string',
            ]);
        if ($validator->fails()) {
            return response()->json(['result' => 'bad request'], 400);
        }
        $validatedData = $validator->valid();

        if ($user != null) {
            $url = $this->base_url . "/GetTransactionByStatus"
                . "/" . $validatedData['status'];
            return Curl::to($url)->get();
        }
        return response()->json(["result" => "forbidden"], 403);
    }

    function deletePackage(Request $request)
    {
        $user = Auth::user();
        if ($user != null) {
            $validator = Validator::make($request->all(),
                [
                    'package_type' => 'required|string',
                ]);
            if ($validator->fails()) {
                return response()->json(['result' => 'bad request'], 400);
            }

            $validatedData = $validator->valid();
            $url = $this->base_url . "/DeletePackage"
                . "/" . $validatedData['package_type'];
            return Curl::to($url)->post();
        }
        return response()->json(["result" => "forbidden"], 403);
    }

    // must change
    function paymentRequest(Request $request)
    {
        $cb_url_base = 'http://82.102.11.166:5555/api/payment/user/package/verification';
        $auth_url = 'https://sandbox.zarinpal.com/pg/StartPay/';
        $user = Auth::user();
        if ($user != null) {
            $validator = Validator::make($request->all(),
                [
                    'payment_gate' => 'required|string',
                    'merchant_id' => 'required|string',
                    'amount' => 'required|string',
                    'package_type' => 'required|string',
                    'description' => 'string',
                    'email' => 'string|email',
                    'mobile' => 'string',
//                    'callback_url' => 'required|string',
                ]);
            if ($validator->fails()) {
                return response()->json(['result' => 'bad request'], 400);
            }
            $validatedData = $validator->valid();

            if (!array_key_exists('email', $validatedData)) {
                $validatedData['email'] = 'n';
            }
            $validatedData['email'] = urlencode($validatedData['email']);

            if (!array_key_exists('mobile', $validatedData)) {
                $validatedData['mobile'] = 'n';
            }
            $validatedData['mobile'] = urlencode($validatedData['mobile']);

            if (!array_key_exists('description', $validatedData)) {
                $validatedData['description'] = 'n';
            }
            $validatedData['description'] = urlencode($validatedData['description']);

            $cb_url = $cb_url_base
                .'/'.$user->id
                .'/'.$validatedData['merchant_id']
                .'/'.$user->id;
            $cb_url = urlencode($cb_url_base);

            $url = $this->base_url . "/PaymentRequest"
                . "/" . $user->id
                . "/" . $validatedData['payment_gate']
                . "/" . $validatedData['merchant_id']
                . "/" . $validatedData['amount']
                . "/" . $validatedData['package_type']
                . "/" . $validatedData['description']
                . "/" . $validatedData['email']
                . "/" . $validatedData['mobile']
                . "/" . $cb_url;

            try {
                return $url;
                $resp = Curl::to($url)->post();
                return $resp;
                $xmlResponse = simplexml_load_string($resp);
                $json_resp = json_decode(json_encode($xmlResponse), true);;

                if ($json_resp['PaymentRequestResult'] == 100) {
                    $redirect_url = $auth_url . $json_resp['Authority'];
                    return $redirect_url;
                    return redirect($redirect_url);
                } else {
                    return response()->json(["result" => "payment failed"], 417);
                }

            } catch (Exception $e) {
                return response()->json(["result" => "curl exception failed"], 417);
            }
        }
        return response()->json(["result" => "forbidden"], 403);
    }

    function paymentVerification(Request $request, $userId, $merchantId, $authority, $amount,$packageType){
        return $request->input('Status');
        return 'hi';
    }
}