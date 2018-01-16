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
    private $base_url = "https://www.google.com";

    function setNewUser()
    {
        $user = Auth::user();
        if ($user != null) {
            $url = $this->base_url."/".$user->id;
            return Curl::to($url)->get();
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
            $url = $this->base_url."/GetUserPackages/" .$user->id
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
            $url = $this->base_url."/UpdatePackage/" .$user->id
                ."/".$validatedData['package_type']
                ."/".$validatedData['cost']
                ."/".$validatedData['time']
                ."/".$validatedData['sensor'];
            return Curl::to($url)->get();
        }
        return response()->json(["result" => "forbidden"],403);
    }
}