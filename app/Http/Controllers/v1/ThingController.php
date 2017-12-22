<?php

namespace App\Http\Controllers\v1;

use App\Repository\Helper\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\ThingException;
use App\Repository\Services\ThingService;
use App\Thing;
use Illuminate\Support\Facades\Auth;

class ThingController extends Controller
{
    protected $thingService;

    /**
     * ProjectController constructor.
     * @param ThingService $thingService
     */
    public function __construct(ThingService $thingService)
    {
        $this->thingService = $thingService;
    }


    /**
     * @param Request $request
     * @return array
     * @throws ThingException
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $this->thingService->validateCreateThing($request);
        $thing = $this->thingService->insertThing($request);
        $user->things()->save($thing);

        return Response::body(compact('thing'));
    }

    /**
     * @return array
     */
    public function all()
    {
        $things = Auth::user()->things()->with('project')->get();
        return Response::body(compact('things'));
    }

    /**
     * @param Thing $thing
     * @param Request $request
     * @return array
     * @throws ThingException
     */
    public function update(Request $request, Thing $thing)
    {
        $user = Auth::user();
        if ($thing['user_id'] != $user->id)
            abort(404);

        $this->thingService->validateUpdateThing($request);

        $thing = $this->thingService->updateThing($request, $thing);


        return Response::body(compact('thing'));
    }
}
