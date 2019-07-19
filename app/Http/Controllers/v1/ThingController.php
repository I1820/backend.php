<?php

namespace App\Http\Controllers\v1;

use App\Exceptions\CoreException;
use App\Exceptions\GeneralException;
use App\Exceptions\LoraException;
use App\Http\Controllers\Controller;
use App\Project;
use App\Repository\Helper\Response;
use App\Repository\Services\Core\DMCoreService;
use App\Repository\Services\CoreService;
use App\Repository\Services\LanService;
use App\Repository\Services\LoraService;
use App\Repository\Services\ThingService;
use App\Thing;
use App\ThingProfile;
use Carbon\Carbon;
use Error;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ThingController extends Controller
{
    protected $thingService;
    protected $dmService;
    protected $coreService;
    protected $lanService;
    protected $loraService;

    /**
     * ProjectController constructor.
     * @param ThingService $thingService
     * @param DMCoreService $dmService
     * @param CoreService $coreService
     * @param LanService $lanService
     * @param LoraService $loraService
     */
    public function __construct(ThingService $thingService,
                                DMCoreService $dmService,
                                CoreService $coreService,
                                LanService $lanService,
                                LoraService $loraService)
    {
        $this->thingService = $thingService;
        $this->coreService = $coreService;
        $this->dmService = $dmService;
        $this->lanService = $lanService;
        $this->loraService = $loraService;

        $this->middleware('can:create,App\Thing')->only(['multiThingData']);
        $this->middleware('can:create,App\Thing')->only(['create']);
        $this->middleware('can:delete,thing')->only(['delete']);
        $this->middleware('can:update,thing')->only(['activate', 'update']);
        $this->middleware('can:view,thing')->only(['get', 'data']);
    }


    /**
     * @param Request $request
     * @return array
     */
    public function create(Request $request)
    {
        $project = Project::where('_id', $request->get('project_id'))->first();
        $thing = $this->createThing(collect($request->all()), $project);
        return Response::body(compact('thing'));
    }

    private function createThing(Collection $data, Project $project)
    {
        $user = Auth::user();
        $this->thingService->validateCreateThing($data);
        if ($data->get('type') == 'lora')
            $thing_profile = ThingProfile::where('thing_profile_slug', (int)$data->get('thing_profile_slug'))->first();
        else
            $thing_profile = null;
        $thing = $this->thingService->insertThing($data, $project, $thing_profile);

        $user->things()->save($thing);
        $this->thingService->addToProject($project, $thing);
        return $thing;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function all(Request $request)
    {
        $user = Auth::user();
        $things = $user->things()->with('project')->get();
        if ($request->get('compress'))
            $things->each->setAppends([]);
        $aliases = [];
        foreach ($user->projects()->get()->pluck('aliases') as $item)
            if ($item)
                foreach ($item as $key => $alias)
                    $aliases[] = [$key => $alias];
        return Response::body(compact('things', 'aliases'));
    }

    public function list(Request $request)
    {
        $user = Auth::user();
        $things = $user->things()->with('project');
        try {
            $data = ['sorted' => json_decode($request->get('sorted'), true) ?: [], 'filtered' => json_decode($request->get('filtered'), true) ?: []];
        } catch (Error $e) {
            $data = ['sorted' => [], 'filtered' => []];
        }
        foreach ($data['filtered'] as $item)
            $things->where($item['id'], 'like', '%' . $item['value'] . '%');
        if (count($data['sorted']))
            $things->orderBy($data['sorted'][0]['id'], $data['sorted'][0]['desc'] ? 'DESC' : 'ASC');

        $pages = ceil($things->count() / (intval($request->get('limit')) ?: 10));
        $things = $things->skip(intval($request->get('offset')))->take(intval($request->get('limit')) ?: 10)->get();
        foreach ($things as $thing)
            $thing->project->setAppends([]);

        return Response::body(['things' => $things, 'pages' => $pages]);
    }

    /**
     * @param Thing $thing
     * @return array
     */
    public function get(Thing $thing)
    {
        $thing->load(['user', 'project', 'profile']);
        $codec = $thing['codec'];
        $thing = $thing->toArray();
        $thing['codec'] = $codec;

        return Response::body(compact('thing'));
    }

    /**
     * @param Thing $thing
     * @param Request $request
     * @return array
     * @throws LoraException
     */
    public function update(Thing $thing, Request $request)
    {
        $thing = $this->thingService->updateThing(collect($request->all()), $thing);
        $thing->load(['profile', 'project', 'user']);
        return Response::body(compact('thing'));
    }

    /**
     * @param Request $request
     * @param bool $sample
     * @return array
     * @throws GeneralException
     * @throws CoreException
     */
    private function data(Request $request)
    {
        $project = Project::where('_id', $request->get('project_id'))->first();

        $aliases = isset($project['aliases']) ? $project['aliases'] : null;

        if ($request->get('window')) {
            $since = Carbon::now()->subMinute((int)$request->get('window'))->getTimestamp();
            $until = Carbon::now()->getTimestamp();
        } else {
            $since = $request->get('since') ?: 0;
            $until = $request->get('until') ?: Carbon::now()->getTimestamp();
        }

        $thing_ids = $request->get('thing_ids')['ids'] ?: [];
        $thing_ids = $project->things()->whereIn('_id', $thing_ids)->get()->pluck('dev_eui');

        $limit = (int)($request->get('limit')) ?: 0;
        $offset = (int)($request->get('offset')) ?: 0;

        $data = $this->dmService->fetchThings($thing_ids, $since, $until, $limit, $offset);
        $data = $this->alias($data, $aliases);

        return $data;
    }

    private function alias($data, $aliases)
    {
        if ($aliases)
            foreach ($data as $d) {
                $res = [];
                foreach ($d->data as $key => $item) {
                    if (isset($aliases[$key]))
                        $res[$aliases[$key]] = $item;
                    else
                        $res[$key] = $item;
                }
                $d->data = $res;
            }
        return $data;
    }

    /**
     * @param Request $request
     * @return array
     * @throws GeneralException
     */
    public function mainData(Request $request)
    {
        $data = $this->data($request);
        return Response::body(compact('data'));

    }

    /**
     * @param Request $request
     * @return ThingService|Model
     * @throws GeneralException
     */
    public function dataToExcel(Request $request)
    {
        $data = $this->data($request);
        return $this->thingService->dataToExcel(collect($data));

    }

    /**
     * @param Request $request
     * @return ThingService|Model
     */
    public function exportExcel(Request $request)
    {
        $things = Auth::user()->things()->with(['profile', 'project'])->get();
        return $this->thingService->toExcel($things);

    }

    /**
     * @param Request $request
     * @return array
     * @throws GeneralException
     */
    public function fromExcel(Request $request)
    {
        $this->thingService->validateExcel($request);
        $file = $request->file('things');
        $res = [];
        Excel::load($file, function ($reader) use (&$res, $request) {
            $project = Project::where('_id', $request->get('project_id'))->first();
            $results = $reader->all();
            $user = Auth::user();
            foreach ($results as $row) {
                $data = $this->prepareRow($row);
                try {
                    $data['devEUI'] = str_repeat("0", 16 - strlen($data['devEUI'])) . $data['devEUI'];
                    $thing = Thing::where('dev_eui', $data['devEUI'])->with('profile')->first();
                    if ($data['devEUI'] == '0000000000000000')
                        continue;
                    Log::debug($data['devEUI']);
                    if (!$user->can('update', $project)) {
                        $res[$data['devEUI']] = 'شما دسترسی این کار را ندارید';
                    } elseif ($row['operation'] == 'add') {
                        if (!$thing) {
                            Auth::user()->can('create', Thing::class);
                            $thing = $this->createThing(collect($row), $project);
                        } else
                            $thing = $this->thingService->updateThing(collect($row), $thing);
                        $res[$data['devEUI']] = $thing;
                        $this->sendKeys($thing, collect($row));
                    } elseif ($row['operation'] == 'delete') {
                        $deleted = $thing && $user->can('delete', $thing) ? $this->delete($thing) : 0;
                        if ($deleted)
                            $res[$data['devEUI']] = 'حذف شد';
                        else
                            $res[$data['devEUI']] = 'پیدا نشد';
                    } else
                        $res[$data['devEUI']] = 'خطای عملیات';
                } catch (Exception $e) {
                    $res[$data['devEUI']] = $e->getMessage();
                }
            }

        });

        return Response::body(compact('res'));
    }

    private function prepareRow($row)
    {
        $row = $row->toArray();
        $row['type'] = 'lora';
        $row['factoryPresetFreqs'] = isset($row['factoryPresetFreqs']) ? [$row['factoryPresetFreqs']] : [];
        return collect($row);

    }

    private function sendKeys(Thing $thing, Collection $data)
    {
        if ($thing['activation'] == 'OTAA')
            $keys = $this->thingService->OTAAKeys($data, $thing);
        elseif ($thing['activation'] == 'ABP')
            $keys = $this->thingService->ABPKeys($data, $thing);
        else
            $keys = $this->thingService->JWTKey($thing);
        $thing['keys'] = $keys;
        $thing->save();
        return $keys;
    }

    /**
     * @param Thing $thing
     * @return array
     * @throws LoraException
     * @throws Exception
     */
    public function delete(Thing $thing)
    {
        $this->thingService->delete($thing);
        return Response::body(['success' => 'true']);
    }

    /**
     * @param Request $request
     * @return array
     * @throws LoraException
     */
    public function deleteMultiple(Request $request)
    {
        $thing_ids = json_decode($request->get('thing_ids'), true) ?: [];
        $things = Auth::user()->things()->whereIn('_id', $thing_ids)->get();
        foreach ($things as $thing) {
            $this->delete($thing);
        }
        return Response::body(['success' => 'true']);
    }

    /**
     * @param Thing $thing
     * @param Request $request
     * @return array
     */
    public function activate(Thing $thing, Request $request)
    {
        $active = $request->get('active') ? true : false;
        $this->thingService->activate($thing, $active);
        return Response::body(['success' => 'true']);
    }

    /**
     * @param Thing $thing
     * @param Request $request
     * @return array
     */
    public function keys(Thing $thing, Request $request)
    {
        $keys = $this->sendKeys($thing, collect($request->all()));
        return Response::body(['keys' => $keys]);

    }

}
