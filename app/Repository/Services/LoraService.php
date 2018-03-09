<?php
/**
 * Created by PhpStorm.
 * User: sajjad
 * Date: 01/20/18
 * Time: 6:42 PM
 */

namespace App\Repository\Services;


use App\Exceptions\LoraException;
use App\Thing;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Ixudra\Curl\CurlService;

class LoraService
{
    protected $token;
    protected $base_url;
    protected $organization_id;
    protected $curlService;
    protected $networkServerID;
    protected $serviceProfileID;

    public function __construct(CurlService $curlService)
    {
        $this->token = Storage::get('jwt.token');
        $this->base_url = config('iot.lora.serverBaseUrl');
        $this->organization_id = config('iot.lora.organizationID');
        $this->networkServerID = config('iot.lora.networkServerID');
        $this->serviceProfileID = config('iot.lora.serviceProfileID');
        $this->curlService = $curlService;
    }

    /**
     * @param Collection $data
     * @param $application_id
     * @return string
     * @throws LoraException
     */
    public function postDevice(Collection $data, $application_id)
    {
        if (env('TEST_MODE'))
            return collect(['deviceProfileID' => 'test']);
        $url = $this->base_url . '/api/devices';
        $data = $data->only([
            'description',
            'devEUI',
            'deviceProfileID',
            'name',
        ])->merge([
            'applicationID' => $application_id,
            'deviceProfileID' => $data->get('thing_profile_slug')
        ]);
        $response = $this->send($url, $data, 'post');
        if ($response->status == 200)
            return $data;
        throw new LoraException($response->content->error, $response->content->code);
    }

    /**
     * @param $data
     * @return void
     * @throws LoraException
     */
    public function postDeviceProfile($data)
    {
        if (env('TEST_MODE'))
            return (object)['deviceProfileID' => 'test'];

        $url = $this->base_url . '/api/device-profiles';
        $response = $this->send($url, $data, 'post');
        if ($response->status == 200)
            return $response->content;
        throw new LoraException($response->content->error, $response->content->code);
    }


    /**
     * @param $deviceProfileId
     * @return string
     * @throws LoraException
     */
    public function deleteDeviceProfile($deviceProfileId)
    {
        if (env('TEST_MODE'))
            return (object)['test' => 'testValue'];
        $url = $url = $this->base_url . '/api/device-profiles/' . $deviceProfileId;
        $response = $this->send($url, [], 'delete');
        if ($response->status == 200)
            return $response->content;
        throw new LoraException($response->content->error ?: '', $response->status);
    }

    /**
     * @param $deviceId
     * @return string
     * @throws LoraException
     */
    public function deleteDevice($deviceId)
    {
        if (env('TEST_MODE'))
            return (object)['test' => 'testValue'];
        $url = $url = $this->base_url . '/api/devices/' . $deviceId;
        $response = $this->send($url, [], 'delete');
        if ($response->status == 200)
            return $response->content;
        throw new LoraException($response->content->error ?: '', $response->status);
    }

    /**
     * @param $data
     * @return string
     * @throws LoraException
     */
    public function sendGateway($data)
    {
        if (env('TEST_MODE'))
            return ['test' => 'testValue'];
        $url = $url = $this->base_url . '/api/gateways';
        $response = $this->send($url, $data, 'post');
        if ($response->status == 200)
            return $response->content;
        throw new LoraException($response->content->error ?: '', $response->status);
    }

    /**
     * @param $mac
     * @return string
     * @throws LoraException
     */
    public function deleteGateway($mac)
    {
        if (env('TEST_MODE'))
            return [];
        $url = $url = $this->base_url . '/api/gateways/' . $mac;
        $response = $this->send($url, [], 'delete');
        if ($response->status == 200)
            return $response->content;
        throw new LoraException($response->content->error ?: '', $response->status);
    }

    /**
     * @param $data
     * @return string
     * @throws LoraException
     */
    public function activateDevice($data)
    {
        if (env('TEST_MODE'))
            return (object)['test' => 'testValue'];
        $url = $url = $this->base_url . '/api/devices/' . $data['devEUI'] . '/activate';
        $response = $this->send($url, $data, 'post');
        if ($response->status == 200)
            return $response->content;
        throw new LoraException($response->content->error ?: '', $response->status);
    }

    /**
     * @param $data
     * @return string
     * @throws LoraException
     */
    public function sendKeys($data)
    {
        if (env('TEST_MODE'))
            return (object)['test' => 'testValue'];
        $url = $url = $this->base_url . '/api/devices/' . $data['devEUI'] . '/keys';
        $response = $this->send($url, $data, 'post');
        if ($response->status == 200)
            return $response->content;
        throw new LoraException($response->content->error ?: '', $response->status);
    }

    /**
     * @return \Illuminate\Config\Repository|mixed
     */
    public function getOrganizationId()
    {
        return $this->organization_id;
    }

    /**
     * @return \Illuminate\Config\Repository|mixed
     */
    public function getNetworkServerID()
    {
        return $this->networkServerID;
    }

    /**
     * @param $description
     * @param $id
     * @return string
     * @throws LoraException
     */
    public function postApp($description, $id)
    {
        if (env('TEST_MODE'))
            return 1;
        $url = $this->base_url . '/api/applications';
        $data = [
            'organizationID' => $this->organization_id,
            'serviceProfileID' => $this->serviceProfileID,
            'name' => (string)$id,
            'description' => $description
        ];
        $response = $this->send($url, $data, 'post');

        if ($response->status == 200)
            return $response->content->id;
        throw new LoraException($response->content->error, $response->content->code);
    }

    /**
     * @param $applicationId
     * @return string
     * @throws LoraException
     */
    public function deleteApp($applicationId)
    {
        if (env('TEST_MODE'))
            return (object)['test' => 'testValue'];

        $url = $url = $this->base_url . '/api/applications/' . $applicationId;
        $response = $this->send($url, [], 'delete');

        if ($response->status == 200)
            return $response->content;
        throw new LoraException($response->content->error ?: '', $response->status);
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
                $new_response = $response->asJsonResponse()->get();
                break;
            case 'post':
                $new_response = $response->asJson()->post();
                break;
            case 'delete':
                $new_response = $response->asJsonResponse()->delete();
                break;
            default:
                $new_response = $response->asJsonResponse()->get();
                break;
        }
        if ($new_response->status == 401 | $new_response->status == 403) {
            $this->authenticate();
            $response = $response->withHeader('Authorization: ' . $this->token);
            switch ($method) {
                case 'get':
                    $new_response = $response->asJsonResponse()->get();
                    break;
                case 'post':
                    $new_response = $response->asJson()->post();
                    break;
                case 'delete':
                    $new_response = $response->asJsonResponse()->asJson()->delete();
                    break;
                default:
                    $new_response = $response->asJsonResponse()->get();
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