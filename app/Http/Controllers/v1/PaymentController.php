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
use Validator;

class PaymentController extends Controller
{
    private $base_url = "172.25.224.78:8090/PaymentServices.svc/rest";

    function setNewUser()
    {
        $user = Auth::user();
        if ($user != null) {
            $url = $this->base_url."/SetNewUser"
                ."/".$user->id;
            return Curl::to($url)->post();
        }
        return response()->json(["result" => "unauthorized"],401);
    }

    function getPackages()
    {
        $user = Auth::user();
        if ($user != null) {
            $url = $this->base_url."/GetPackage/";
            return Curl::to($url)->get();
        }
        return response()->json(["result" => "forbidden"],403);
    }

    function getUserPackages(Request $request)
    {
        $user = Auth::user();
        if ($user != null) {
            $validator = Validator::make($request->all(),
                [
                'range' => 'required|integer',
                'page' =>  'required|integer'
                ]);
            if($validator->fails()) {
                return response()->json(['result' => 'bad request'], 400);
            }

            $validatedData = $validator->valid();
            $url = $this->base_url."/GetUserPackages"
                                    ."/".$user->id
                                    ."/".$validatedData['page']
                                    ."/".$validatedData['range'];
            return Curl::to($url)->get();
        }
        return response()->json(["result" => "forbidden"],403);
    }

    function updatePackage(Request $request){
        $user = Auth::user();
        if ($user != null) {
            $validator = Validator::make($request->all(),
                [
                    'package_type' => 'required|string',
                    'cost' =>  'required|integer',
                    'time' =>  'required|integer',
                    'sensor' =>  'required|integer',

                ]);
            if($validator->fails()) {
                return response()->json(['result' => 'bad request'], 400);
            }

            $validatedData = $validator->valid();
            $url = $this->base_url."/UpdatePackage"
                ."/".$validatedData['package_type']
                ."/".$validatedData['cost']
                ."/".$validatedData['time']
                ."/".$validatedData['sensor'];
            return Curl::to($url)->post();
        }
        return response()->json(["result" => "forbidden"],403);
    }

    function updatePackageStatus(Request $request){
        $user = Auth::user();
        if ($user != null) {
            $validator = Validator::make($request->all(),
                [
                    'status' => 'required|string',
                ]);
            if($validator->fails()) {
                return response()->json(['result' => 'bad request'], 400);
            }

            $validatedData = $validator->valid();
            $url = $this->base_url."/UpdatePackageStatus"
                ."/".$user->id
                ."/".$validatedData['status'];
            return Curl::to($url)->post();
        }
        return response()->json(["result" => "forbidden"],403);
    }

    function getLastPackageStatus(){
        $user = Auth::user();
        if ($user != null) {
            $url = $this->base_url."/GetLastPackageStatus"
                ."/".$user->id;
            return Curl::to($url)->get();
        }
        return response()->json(["result" => "forbidden"],403);
    }

    function getUserPackagesByStatus(Request $request){
        $user = Auth::user();
        if ($user != null) {
            $validator = Validator::make($request->all(),
                [
                    'status' => 'required|string',
                ]);
            if($validator->fails()) {
                return response()->json(['result' => 'bad request'], 400);
            }

            $validatedData = $validator->valid();
            $url = $this->base_url."/GetUserDataByStatus"
                ."/".$user->id
                ."/".$validatedData['status'];
            return Curl::to($url)->get();
        }
        return response()->json(["result" => "forbidden"],403);
    }

    function getUserTransactions(){
        $user = Auth::user();
        if ($user != null) {
            $url = $this->base_url."/GetUserTransaction"
                ."/".$user->id;
            return Curl::to($url)->get();
        }
        return response()->json(["result" => "forbidden"],403);
    }

    function deletePackage(Request $request){
        $user = Auth::user();
        if ($user != null) {
            $validator = Validator::make($request->all(),
                [
                    'package_type' => 'required|string',
                ]);
            if($validator->fails()) {
                return response()->json(['result' => 'bad request'], 400);
            }

            $validatedData = $validator->valid();
            $url = $this->base_url."/DeletePackage"
                ."/".$validatedData['package_type'];
            return Curl::to($url)->post();
        }
        return response()->json(["result" => "forbidden"],403);
    }
}