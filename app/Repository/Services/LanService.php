<?php
/**
 * Created by PhpStorm.
 * User: sajjad
 * Date: 01/20/18
 * Time: 6:42 PM
 */

namespace App\Repository\Services;


use App\Exceptions\GeneralException;
use App\Exceptions\LoraException;
use App\Thing;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Ixudra\Curl\CurlService;

class LanService
{
    protected $base_url;
    protected $curlService;

    public function __construct(CurlService $curlService)
    {
        $this->base_url = config('iot.lan.serverBaseUrl');
        $this->curlService = $curlService;
    }

    /**
     * @param Collection $data
     * @return string
     * @throws GeneralException
     */
    public function postDevice(Collection $data)
    {
        Log::debug("LAN Send Device:\t" . $data['devEUI']);
        $url = $this->base_url . '/api/devices';
        $data = $data->only([
            'name',
            'devEUI',
            'ip'
        ]);
        $response = $this->send($url, $data, 'post');
        return collect(json_decode(json_encode($response), true));
    }

    public function updateDevice(Collection $data, $dev_eui)
    {
        Log::debug("LAN Update Device:\t" . $dev_eui);
        $url = $this->base_url . '/api/devices/' . $dev_eui;
        $data = $data->only([
            'name',
            'ip'
        ]);
        $response = $this->send($url, $data, 'put');
        return collect(json_decode(json_encode($response), true));
    }

    public function deleteDevice($dev_eui)
    {
        Log::debug("LAN Delete Device:\t" . $dev_eui);
        $url = $this->base_url . '/api/devices/' . $dev_eui;
        $response = $this->send($url, [], 'delete');
        return collect(json_decode(json_encode($response), true));
    }

    public function getKey(Thing $thing)
    {
        Log::debug("LAN Get Key:\t" . $thing['dev_eui']);
        $url = $this->base_url . '/api/devices/' . $thing['dev_eui'] . '/refresh';

        $response = $this->send($url, [], 'get');
        return collect(json_decode(json_encode($response), true));
    }


    /**
     * @param $url
     * @param $data
     * @param string $method
     * @return array|object
     * @throws GeneralException
     */
    private function send($url, $data, $method)
    {
        if (env('LAN_TEST') == 1) {
            return $this->fake();
        }

        $response = $this->curlService->to($url)
            ->withHeader('Accept: application/json')
            ->withHeader('Authorization: ' . env('CORE_SECRET'))
            ->withData($data)
            ->withOption('SSL_VERIFYHOST', false)
            ->returnResponseObject()
            ->asJsonRequest()
            ->asJsonResponse()
            ->withTimeout('5');
        $new_response = null;
        switch ($method) {
            case 'get':
                $new_response = $response->get();
                break;
            case 'post':
                $new_response = $response->post();
                break;
            case 'delete':
                $new_response = $response->delete();
                break;
            default:
                $new_response = $response->get();
                break;
        }
        /*
        Log::debug('-----------------------------------------------------');
        Log::debug(print_r($data, true));
        Log::debug(print_r($new_response, true));
        Log::debug('-----------------------------------------------------');
        */
        if ($new_response->status == 0) {
            throw new GeneralException($new_response->error, 0);
        }
        if ($new_response->status == 200) {
            return $new_response->content ?: [];
        }
        $error = property_exists($new_response->content, 'error') ? $new_response->content->error :
            (property_exists($new_response->content, 'description') ? $new_response->content->description : 'خطای نامشخص');
        throw new GeneralException($error, $new_response->status);

    }

    public function fake()
    {
        return (object)[
            'status' => 200,
            'content' => [
                'key' => 'value'
            ]
        ];
    }
}
