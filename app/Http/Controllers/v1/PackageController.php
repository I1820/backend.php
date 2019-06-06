<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Package;
use App\Repository\Helper\Response;
use Illuminate\Http\Request;

class PackageController extends Controller
{


    /**
     * PackageController constructor.
     */
    public function __construct()
    {

        $this->middleware('can:view,package')->only(['get']);
        $this->middleware('can:update,package')->only(['activate', 'update']);
        $this->middleware('can:delete,package')->only(['delete']);
        $this->middleware('can:create,App\Package')->only(['create', 'all']);
    }


    /**
     * @param Request $request
     * @return array
     */
    public function list(Request $request)
    {
        $packages = Package::where('is_active', true)->get();
        return Response::body(['packages' => $packages]);
    }

    /**
     * @param Package $package
     * @return array
     */
    public function get(Package $package)
    {
        return Response::body(compact('package'));
    }


}
