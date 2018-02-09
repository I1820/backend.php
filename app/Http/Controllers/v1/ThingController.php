<?php

namespace App\Http\Controllers\v1;

use App\Permission;
use App\Repository\Helper\Response;
use App\Repository\Services\CoreService;
use App\Repository\Services\PermissionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\ThingException;
use App\Repository\Services\ThingService;
use App\Thing;
use Illuminate\Support\Facades\Auth;

class ThingController extends Controller
{
    protected $thingService;
    protected $permissionService;
    protected $coreService;

    /**
     * ProjectController constructor.
     * @param ThingService $thingService
     * @param PermissionService $permissionService
     * @param CoreService $coreService
     */
    public function __construct(ThingService $thingService,
                                PermissionService $permissionService,
                                CoreService $coreService)
    {
        $this->thingService = $thingService;
        $this->permissionService = $permissionService;
        $this->coreService = $coreService;
    }


    /**
     * @param Request $request
     * @return array
     * @throws ThingException
     * @throws \App\Exceptions\LoraException
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $this->thingService->validateCreateThing($request);
        $thing = $this->thingService->insertThing($request);
        $user->things()->save($thing);
        $owner_permission = $this->permissionService->get('THING-OWNER');
        $permission = Permission::create([
            'name' => $owner_permission['name'],
            'permission_id' => (string)$owner_permission['_id'],
            'item_type' => 'thing'
        ]);
        $thing->permissions()->save($permission);
        $user->permissions()->save($permission);

        return Response::body(compact('thing'));
    }

    /**
     * @return array
     */
    public function all()
    {
        $things = Auth::user()->things()->get();
        $things = $things->map(function ($item) {
            return $item['thing'];
        });
        return Response::body(compact('things'));
    }

    /**
     * @param Thing $thing
     * @return array
     */
    public function get(Thing $thing)
    {
        $user = Auth::user();
        if ($thing['user_id'] != $user->id)
            abort(404);
        $thing->load(['user', 'project','codec']);

        return Response::body(compact('thing'));
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

    /**
     * @param Request $request
     * @param Thing $thing
     * @return array
     * @throws \App\Exceptions\GeneralException
     */
    public function data(Request $request, Thing $thing)
    {
        $user = Auth::user();
        if ($thing['user_id'] != $user->id)
            abort(404);

        $offset = $request->get('offset') ? Carbon::createFromTimestamp($request->get('offset')) : 0;
        $count = $request->get('count') ?: 100;
        $data = $this->coreService->getDeviceData($thing, $offset, $count);
        //$data = $thing->data()->where('timestamp', '>', $offset)->take((int)$count)->get();

        return Response::body(compact('data'));
    }
}
