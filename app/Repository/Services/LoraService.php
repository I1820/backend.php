<?php
/**
 * Created by PhpStorm.
 * User: sajjad
 * Date: 01/20/18
 * Time: 6:42 PM
 */

namespace App\Repository\Services;


use App\Exceptions\LoraException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Ixudra\Curl\CurlService;

class LoraService
{
    protected $token;
    protected $base_url;
    protected $application_id;
    protected $organization_id;
    protected $curlService;
    protected $networkServerID;

    public function __construct(CurlService $curlService)
    {
        $this->token = Storage::get('jwt.token');
        $this->base_url = config('iot.lora.serverBaseUrl');
        $this->application_id = config('iot.lora.applicationID');
        $this->organization_id = config('iot.lora.organizationID');
        $this->networkServerID = config('iot.lora.networkServerID');
        $this->curlService = $curlService;
    }

    /**
     * @param Collection $data
     * @throws LoraException
     * @return string
     */
    public function postDevice(Collection $data)
    {
        if (env('TEST_MODE'))
            return collect(['deviceProfileID' => 'test']);
        $this->authenticate();
        $url = $this->base_url . '/api/devices';
        $data = $data->only([
            'description',
            'devEUI',
            'deviceProfileID',
            'name',
        ])->merge([
            'applicationID' => $this->application_id
        ]);
        $response = $this->send($url, $data, 'post');

        if ($response->status == 200)
            return $data;
        throw new LoraException($response->content->error, $response->content->code);
    }

    /**
     * @param Collection $data
     * @return void
     * @throws LoraException
     */
    public function postDeviceProfile(Collection $data)
    {
        if (env('TEST_MODE'))
            return (object)['deviceProfileID' => 'test'];
        $url = $this->base_url . '/api/device-profiles';
        $response = $this->send($url, $this->prepareDeviceProfileData($data), 'post');
        if ($response->status == 200)
            return $response->content;
        throw new LoraException($response->content->error, $response->content->code);
    }

    /**
     * @param string $devEUI
     * @return string
     * @throws LoraException
     */
    public function deleteDevice(string $devEUI)
    {
        if (env('TEST_MODE'))
            return;
        $url = $this->base_url . '/api/devices/' . $devEUI;
        $this->send($url, [], 'delete');
        return;
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
                    'supportsJoin' => $data->get('supports32bitFCnt') ? true : false
                ],
                'name' => $data->get('name'),
                'networkServerID' => $this->networkServerID,
                'organizationID' => $this->organization_id
            ];
        } catch (\Exception $e) {
            throw new LoraException('Device Profile Data Invalid', 500);
        }

        return $res;
    }

    private function send($url, $data, $method = 'get')
    {
        $response = $this->curlService->to($url)
            ->withData($data)
            ->withOption('SSL_VERIFYHOST', false)
            ->withHeader('Authorization: ' . $this->token)
            ->returnResponseObject();
        $new_response = null;
        switch ($method) {
            case 'get':
                $new_response = $response->get();
                break;
            case 'post':
                $new_response = $response->asJson()->post();
                break;
            case 'delete':
                $new_response = $response->delete();
                break;
            default:
                $new_response = $response->get();
                break;
        }
        if ($new_response->status == 401) {
            $this->authenticate();
            $response = $response->withHeader('Authorization: ' . $this->token);
            switch ($method) {
                case 'get':
                    $new_response = $response->get();
                    break;
                case 'post':
                    $new_response = $response->asJson()->post();
                    break;
                case 'delete':
                    $new_response = $response->asJson()->delete();
                    break;
                default:
                    $new_response = $response->get();
                    break;
            }
        }
        if ($new_response->status == 0) {
            throw new LoraException($new_response->error, 0);
        }
        return $new_response;
    }

    private function authenticate()
    {
        $response = $this->curlService->to($this->base_url . '/api/internal/login')
            ->withData(['username' => 'admin', 'password' => 'admin'])
            ->asJson()
            ->withOption('SSL_VERIFYHOST', false)
            ->post();;
        $this->token = $response->jwt;
        Storage::put('jwt.token', $this->token);
    }

}