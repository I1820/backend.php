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
}