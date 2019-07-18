<?php

namespace App\Http\Controllers;

use App\Repository\Services\LanService;
use Illuminate\Http\Request;
use Ixudra\Curl\CurlService;

class AboutController extends Controller
{
    public function index(Request $request)
    {
        return response("18.20 is leaving us", 200);
    }

}
