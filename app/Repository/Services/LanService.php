<?php
/**
 * Created by PhpStorm.
 * User: sajjad
 * Date: 01/20/18
 * Time: 6:42 PM
 */

namespace App\Repository\Services;


use App\Exceptions\GeneralException;
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
    public function postDevice($data)
    {
        Log::debug("LAN Send Device:\t" . $data['devEUI']);
        $url = $this->base_url . '/api/devices';
        $data = $data->only([
            'name',
            'devEUI'
        ]);
        $response = $this->_send($url, $data, 'post');
        return $response;
    }

    public function updateDevice($data, $dev_eui)
    {
        Log::debug("LAN Update Device:\t" . $dev_eui);
        $url = $this->base_url . '/api/devices/' . $dev_eui;
        $response = $this->_send($url, $data, 'put');
        return $response;
    }

    public function deleteDevice($dev_eui)
    {
        Log::debug("LAN Delete Device:\t" . $dev_eui);
        $url = $this->base_url . '/api/devices/' . $dev_eui;
        $response = $this->_send($url, [], 'delete');
        return $response;
    }

    public function getKey(Thing $thing)
    {
        Log::debug("LAN Get Key:\t" . $thing['dev_eui']);
        $url = $this->base_url . '/api/devices/' . $thing['dev_eui'] . '/refresh';

        $response = $this->_send($url, [], 'get');
        return $response;
    }


    /**
     * @param $url
     * @param $data
     * @param string $method
     * @return array
     * @throws GeneralException
     */
    private function _send($url, $data, $method)
    {
        if (env('LAN_TEST') == 1) {
            return $this->fake();
        }

        $response = $this->curlService->to($url)
            ->withData($data)
            ->returnResponseObject()
            ->asJson(true)
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
            case 'put':
                $new_response = $response->put();
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
            throw new GeneralException('LAN service connection error', 500);
        }
        if ($new_response->status == 200) {
            return collect($new_response->content) ?: [];
        }
        if ($new_response->status != 200) {
            if ($new_response->content) {
                throw new GeneralException(
                    $new_response->content->message ?: 'Unknown LAN service error',
                    $new_response->status
                );
            } else {
                throw new GeneralException('LAN service connection error', $new_response->status);
            }
        }
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
