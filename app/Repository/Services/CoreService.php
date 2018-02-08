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
use App\Project;
use App\Thing;
use Illuminate\Support\Collection;
use Ixudra\Curl\CurlService;

class CoreService
{
    protected $base_url;
    protected $curlService;

    public function __construct(CurlService $curlService)
    {
        $this->base_url = config('iot.core.serverBaseUrl');
        $this->curlService = $curlService;
    }

    /**
     * @param $id
     * @return string
     * @throws GeneralException
     */
    public function postProject($id)
    {
        if (env('TEST_MODE'))
            return (object)['test' => 'testValue'];
        $url = $this->base_url . '/api/project';
        $data = [
            'name' => (string)$id,
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
        $url = $this->base_url . '/api/project/' . $project_id;
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
        $url = $this->base_url . '/api/project/' . $project['container']['name'] . '/things/';
        $data = [
            'name' => $thing['interface']['devEUI'],
        ];
        $response = $this->send($url, $data, 'post');
        if ($response->status == 200)
            return $response->content;
        throw new GeneralException($response->content->error ?: '', $response->status);
        return $response;
    }

    private function send($url, $data, $method = 'get')
    {
        $response = $this->curlService->to($url)
            ->withData($data)
            ->withOption('SSL_VERIFYHOST', false)
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
        return $new_response;
    }


}