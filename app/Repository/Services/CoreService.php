<?php
/**
 * Created by PhpStorm.
 * User: Sajjad
 * Date: 02/7/18
 * Time: 11:42 AM
 */

namespace App\Repository\Services;


use App\Exceptions\GeneralException;
use App\Exceptions\LoraException;
use App\Gateway;
use App\Project;
use App\Scenario;
use App\Thing;
use Illuminate\Support\Collection;
use Ixudra\Curl\CurlService;

class CoreService
{
    protected $base_url;
    protected $port;
    protected $dmPort;
    protected $gmPort;
    protected $curlService;

    public function __construct(CurlService $curlService)
    {
        $this->base_url = config('iot.core.serverBaseUrl');
        $this->port = config('iot.core.port');
        $this->dmPort = config('iot.core.dmPort');
        $this->gmPort = config('iot.core.gmPort');
        $this->curlService = $curlService;
    }

    /**
     * @param $id
     * @return string
     * @throws GeneralException
     */
    public function postProject($id)
    {
        $id = (string)$id;
        if (env('TEST_MODE'))
            return ['container' => ['port' => 22]];
        $url = '/api/project';
        $data = [
            'name' => $id,
        ];
        $response = $this->send($url, $data, 'post');

        if ($response->status == 200)
            return $response->content;
        throw new GeneralException($response->content->error ?: '', $response->status);
    }

    /**
     * @param $project_id
     * @return string
     * @throws GeneralException
     */
    public function deleteProject($project_id)
    {
        if (env('TEST_MODE'))
            return (object)['test' => 'testValue'];
        $url = '/api/project/' . $project_id;
        $response = $this->send($url, [], 'delete');
        if ($response->status == 200)
            return $response->content;
        throw new GeneralException($response->content->error ?: '', $response->status);
    }


    /**
     * @param Thing $thing
     * @param Project $project
     * @return string
     * @throws GeneralException
     */
    public function postThing(Project $project, Thing $thing)
    {
        if (env('TEST_MODE'))
            return (object)['test' => 'testValue'];
        $url = '/api/project/' . $project['container']['name'] . '/things/';
        $data = [
            'name' => $thing['interface']['devEUI'],
        ];
        $response = $this->send($url, $data, 'post');
        if ($response->status == 200)
            return $response->content;
        throw new GeneralException($response->content->error ?: '', $response->status);
        return $response;
    }

    /**
     * @param Project $project
     * @param Thing $thing
     * @param $codec
     * @return string
     * @throws GeneralException
     */
    public function sendCodec(Project $project, Thing $thing, $codec)
    {
        if (env('TEST_MODE'))
            return (object)['test' => 'testValue'];
        $url = '/api/codec/' . $thing['interface']['devEUI'];
        $response = $this->send($url, $codec, 'post', $project['container']['runner']['port'], 0);
        if ($response->status == 200)
            return $response->content;
        throw new GeneralException($response->content->error ?: '', $response->status);
        return $response;
    }

    /**
     * @param Thing $thing
     * @param $offset
     * @param $limit
     * @return string
     * @throws GeneralException
     */
    public function getDeviceData(Thing $thing, $offset, $limit)
    {
        if (env('TEST_MODE'))
            return ['test' => 'testValue'];
        $url = '/api/things/' . $thing['interface']['devEUI'];
        $response = $this->send($url, ['offset' => $offset->timestamp, 'limit' => $limit], 'get', $this->dmPort);
        if ($response->status == 200)
            return $response->content;

        throw new GeneralException($response->content->error ?: '', $response->status);
        return $response;
    }


    /**
     * @param Project $project
     * @param Scenario $scenario
     * @return string
     * @throws GeneralException
     */
    public function sendScenario(Project $project, Scenario $scenario)
    {
        if (env('TEST_MODE'))
            return (object)['test' => 'testValue'];
        $url = '/api/scenario/' . $project['container']['name'];
        $response = $this->send($url, $scenario->code, 'post', $project['container']['runner']['port'], 0);
        if ($response->status == 200)
            return $response->content;
        throw new GeneralException($response->content->error ?: '', $response->status);
        return $response;
    }

    /**
     * @param $data
     * @return string
     * @throws GeneralException
     */
    public function sendGateway($data)
    {
        if (env('TEST_MODE'))
            return (object)['test' => 'testValue'];
        $url = '/api/gateway';
        $response = $this->send($url, $data, 'post', $this->gmPort);
        if ($response->status == 200)
            return $response->content;
        throw new GeneralException($response->content->error ?: '', $response->status);
        return $response;
    }

    private function send($url, $data, $method = 'get', $port = '', $json_request = 1)
    {
        $port = $port == '' ? $this->port : $port;
        $url = $this->base_url . ':' . $port . $url;

        $response = $this->curlService->to($url)
            ->withData($data)
            ->withOption('SSL_VERIFYHOST', false)
            ->returnResponseObject()->withTimeout('5');
        if ($method == 'post' && $json_request)
            $response = $response->asJson();
        $new_response = null;
        switch ($method) {
            case 'get':
                $new_response = $response->asJsonResponse()->get();
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
        if ($new_response->status == 0) {
            throw new GeneralException($new_response->error, 0);
        }
        return $new_response;
    }


}