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
    protected $downLinkPort;
    protected $curlService;

    public function __construct(CurlService $curlService)
    {
        $this->base_url = config('iot.core.serverBaseUrl');
        $this->port = config('iot.core.port');
        $this->dmPort = config('iot.core.dmPort');
        $this->downLinkPort = config('iot.core.downLinkPort');
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
            return ["container" => [
                "name" => "5a9958eca8f082000a24bf84",
                "runner" => [
                    "id" => "37cb1c466fb56b40c94813e2cc3a5dcb21b3d578daa82682f7c26f33d809d23d",
                    "port" => "8081"
                ]
            ]];
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
        $url = '/api/project/' . $project['container']['name'] . '/things';
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
            return ['test' => 'testValue'];
        $url = '/api/codec/' . $thing['interface']['devEUI'];
        $response = $this->send($url, $codec, 'post', $project['container']['runner']['port'], 0);
        if ($response->status == 200)
            return $response->content;
        throw new GeneralException($response->content->error ?: '', $response->status);
        return $response;
    }

    /**
     * @param Thing $thing
     * @param $since
     * @param $until
     * @return string
     * @throws GeneralException
     */
    public function thingData(Thing $thing, $since, $until)
    {
        if (env('TEST_MODE'))
            return ['test' => 'testValue'];
        $url = '/api/things/' . $thing['interface']['devEUI'];
        $response = $this->send($url, ['since' => (int)$since, 'until' => (int)$until], 'get', $this->dmPort);
        if ($response->status == 200)
            return $response->content ?: [];

        throw new GeneralException($response->content->error ?: '', $response->status);
        return $response;
    }

    /**
     * @param array $ids
     * @param $since
     * @param $until
     * @return string
     * @throws GeneralException
     */
    public function thingsData($ids, $since, $until)
    {
        if (env('TEST_MODE'))
            return array (
                        0 =>
                            (object)array (
                                '_id' => '5ab0bb08845ac1936f1f3c15',
                                'data' =>
                                    array (
                                        1 => 23.625101089477539,
                                        4 => 23.497787475585938,
                                    ),
                                'rxinfo' =>
                                    array (
                                        0 =>
                                            array (
                                                'lorasnr' => 3,
                                                'mac' => 'b827ebffff47d1a5',
                                                'name' => '5aaf30e5429987001965dfa5',
                                                'rssi' => -114,
                                                'time' => '0001-01-01T00:00:00Z',
                                            ),
                                    ),
                                'thingid' => '0000000000000001',
                                'timestamp' => '2018-03-20T11:10:56.908+03:30',
                            ),
                        1 =>
                            (object)array (
                                '_id' => '5ab0bb0b845ac1936f1f3c1a',
                                'data' =>
                                    array (
                                        2 => 23.497787475585938,
                                        4 => 23.625101089477539,
                                    ),
                                'rxinfo' =>
                                    array (
                                        0 =>
                                            array (
                                                'lorasnr' => 2.5,
                                                'mac' => 'b827ebffff47d1a5',
                                                'name' => '5aaf30e5429987001965dfa5',
                                                'rssi' => -114,
                                                'time' => '0001-01-01T00:00:00Z',
                                            ),
                                    ),
                                'thingid' => '0000000000000001',
                                'timestamp' => '2018-03-20T11:10:59.015+03:30',
                            ),
                        2 =>
                            (object)array (
                                '_id' => '5ab0bd61845ac1936f1f3d63',
                                'data' =>
                                    array (
                                        1 => 23.435916900634766,
                                        3 => 23.829360961914062,
                                    ),
                                'rxinfo' =>
                                    array (
                                        0 =>
                                            array (
                                                'lorasnr' => 3.5,
                                                'mac' => 'b827ebffff47d1a5',
                                                'name' => '5aaf30e5429987001965dfa5',
                                                'rssi' => -112,
                                                'time' => '0001-01-01T00:00:00Z',
                                            ),
                                    ),
                                'thingid' => '0000000000000001',
                                'timestamp' => '2018-03-20T11:20:57.053+03:30',
                            ),
                        3 =>
                            (object)array (
                                '_id' => '5ab0bd63845ac1936f1f3d67',
                                'data' =>
                                    array (
                                        1 => 23.435916900634766,
                                        2 => 23.829360961914062,
                                    ),
                                'rxinfo' =>
                                    array (
                                        0 =>
                                            array (
                                                'lorasnr' => 1.2,
                                                'mac' => 'b827ebffff47d1a5',
                                                'name' => '5aaf30e5429987001965dfa5',
                                                'rssi' => -114,
                                                'time' => '0001-01-01T00:00:00Z',
                                            ),
                                    ),
                                'thingid' => '0000000000000001',
                                'timestamp' => '2018-03-20T11:20:59.162+03:30',
                            ),
            );
        $url = '/api/things';
        $response = $this->send($url, ['since' => (int)$since, 'until' => (int)$until, 'thing_ids' => $ids], 'post', $this->dmPort);
        if ($response->status == 200)
            return $response->content ?: [];

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


    /**
     * @return string
     * @throws GeneralException
     */
    public function projectList()
    {
        $url = '/api/project';
        $response = $this->send($url, [], 'get');
        if ($response->status == 200)
            return $response->content;
        throw new GeneralException($response->content->error ?: '', $response->status);
        return $response;
    }

    /**
     * @param $project_id
     * @return string
     * @throws GeneralException
     */
    public function projectLogs($project_id)
    {
        $url = '/api/project/' . $project_id . '/logs/?limit=10';
        $response = $this->send($url, [], 'get');
        if ($response->status == 200)
            return $response->content;
        throw new GeneralException($response->content->error ?: '', $response->status);
    }

    public function downLinkThing(Project $project, Thing $thing, $data)
    {
        $url = '/api/send';
        $data = ['thing' => $thing->toArray(), 'data' => $data, 'project_id' => $project->application_id];
        $response = $this->send($url, $data, 'post', $this->downLinkPort);
        if ($response->status == 200)
            return $response->content;
        throw new GeneralException($response->content->error ?: '', $response->status);
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