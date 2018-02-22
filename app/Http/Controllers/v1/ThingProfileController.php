<?php

namespace App\Http\Controllers\v1;

use App\Repository\Helper\Response;
use App\Repository\Services\LoraService;
use App\ThingProfile;
use App\Exceptions\LoraException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;

class ThingProfileController extends Controller
{
    protected $loraService;

    /**
     * ProjectController constructor.
     * @param LoraService $loraService
     */
    public function __construct(LoraService $loraService)
    {
        $this->loraService = $loraService;
    }


    /**
     * @param Request $request
     * @return array
     * @throws LoraException
     */
    public function create(Request $request)
    {
        $data = $this->prepareDeviceProfileData(collect($request->all()));
        $device_profile_id = $this->loraService->postDeviceProfile(collect($data))->deviceProfileID;
        $thing_profile = ThingProfile::create([
            'thing_profile_slug' => $device_profile_id,
            'data' => $data,
            'type' => $request->get('supportsJoin') === '1' ? 'OTAA' : 'ABP'
        ]);
        return Response::body(compact('thing_profile'));
    }

    /**
     * @return array
     */
    public function all()
    {
        $thing_profiles = ThingProfile::all();
        return Response::body(compact('thing_profiles'));
    }

    /**
     * @param ThingProfile $thing_profile
     * @return array
     * @throws LoraException
     * @throws \Exception
     */
    public function delete(ThingProfile $thing_profile)
    {
        $this->loraService->deleteDeviceProfile($thing_profile['thing_profile_slug']);
        $thing_profile->delete();
        return Response::body(['success' => 'true']);
    }


    private function prepareDeviceProfileData(Collection $data)
    {
        try {
            $factoryPresetFreqs = collect($data->get('factoryPresetFreqs'))->map(function ($item, $key) {
                return (int)$item;
            });
            $res = [
                'deviceProfile' => [
                    'classBTimeout' => (int)$data->get('classBTimeout', 0),
                    'classCTimeout' => (int)$data->get('classCTimeout', 0),
                    'factoryPresetFreqs' => $factoryPresetFreqs,
                    'macVersion' => $data->get('macVersion', ''),
                    'maxDutyCycle' => (int)$data->get('maxDutyCycle', 0),
                    'maxEIRP' => (int)$data->get('maxEIRP', 0),
                    'pingSlotDR' => (int)$data->get('pingSlotDR', 0),
                    'pingSlotFreq' => (int)$data->get('pingSlotFreq', 0),
                    'pingSlotPeriod' => (int)$data->get('pingSlotPeriod', 0),
                    'regParamsRevision' => $data->get('regParamsRevision', ''),
                    'rfRegion' => $data->get('rfRegion', ''),
                    'rxDROffset1' => (int)$data->get('rxDROffset1', 0),
                    'rxDataRate2' => (int)$data->get('rxDataRate2', 0),
                    'rxDelay1' => (int)$data->get('rxDelay1', 0),
                    'rxFreq2' => (int)$data->get('rxFreq2', 0),
                    'supports32bitFCnt' => $data->get('supports32bitFCnt') ? true : false,
                    'supportsClassB' => $data->get('supportsClassB') ? true : false,
                    'supportsClassC' => $data->get('supportsClassC') ? true : false,
                    'supportsJoin' => $data->get('supportsJoin') === '1' ? true : false
                ],
                'name' => $data->get('name'),
                'networkServerID' => $this->loraService->getNetworkServerID(),
                'organizationID' => $this->loraService->getOrganizationId()
            ];
        } catch (\Exception $e) {
            throw new LoraException('Device Profile Data Invalid', 500);
        }

        return $res;
    }
}
