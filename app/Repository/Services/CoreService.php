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
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
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
        $this->pmPort = config('iot.core.pmPort');
        $this->dmPort = config('iot.core.dmPort');
        $this->downLinkPort = config('iot.core.downLinkPort');
        $this->gmPort = config('iot.core.gmPort');
        $this->curlService = $curlService;
    }

    /**
     * @param $id
     * @return array
     * @throws GeneralException
     */
    public function postProject($id)
    {
        Log::debug("Core Send Project\t" . $id);
        $url = '/api/project';
        $data = ['name' => (string)$id];
        $response = $this->_send($url, $data, 'post', $this->pmPort);
        return $response;
    }

    /**
     * @param $project_id
     * @return array
     * @throws GeneralException
     */
    public function deleteProject($project_id)
    {
        Log::debug("Core Delete Project\t" . $project_id);
        $url = '/api/project/' . $project_id;
        $response = $this->_send($url, [], 'delete', $this->pmPort);
        return $response;
    }


    /**
     * @param $devEUI
     * @return array
     * @throws GeneralException
     */
    public function deleteThing($devEUI)
    {
        Log::debug("Core Delete Thing\t" . $devEUI);
        $url = '/api/things/' . $devEUI;
        $response = $this->_send($url, [], 'delete', $this->pmPort);
        return $response;
    }

    /**
     * @param Project $project
     * @param Thing $thing
     * @return array
     * @throws GeneralException
     */
    public function postThing(Project $project, Thing $thing)
    {
        Log::debug("Core Send Thing\t" . $project['_id']);
        $url = '/api/project/' . $project['container']['name'] . '/things';
        $data = [
            'name' => $thing['interface']['devEUI'],
        ];
        $response = $this->_send($url, $data, 'post', $this->pmPort);
        return $response;
    }

    /**
     * @param Project $project
     * @param Thing $thing
     * @param $codec
     * @return array
     * @throws GeneralException
     */
    public function sendCodec(Project $project, Thing $thing, $codec)
    {
        Log::debug("Core Send Codec\t" . $project['_id']);
        $url = '/api/codec';
        $response = $this->_send($url, ['code' => $codec, 'id' => $thing['interface']['devEUI']], 'post', $project['container']['runner']['port']);
        return $response;
    }

    /**
     * @param Project $project
     * @param Thing $thing
     * @param $data
     * @return array
     * @throws GeneralException
     */
    public function encode(Project $project, Thing $thing, $data)
    {
        $url = '/api/encode/' . $thing['interface']['devEUI'];
        $response = $this->_send($url, $data, 'post', $project['container']['runner']['port']);
        return $response;
    }

    /**
     * @param Project $project
     * @param Thing $thing
     * @param $data
     * @return array
     * @throws GeneralException
     */
    public function decode(Project $project, Thing $thing, $data)
    {
        $url = '/api/decode/' . $thing['interface']['devEUI'];
        $response = $this->_send($url, $data, 'post', $project['container']['runner']['port']);
        return $response;
    }


    /**
     * @param Thing $thing
     * @param $since
     * @param $until
     * @return array
     * @throws GeneralException
     */
    public function thingData(Thing $thing, $since, $until)
    {
        Log::debug("Core Thing Data");
        $url = '/api/things/' . $thing['interface']['devEUI'];
        $response = $this->_send($url, ['since' => (int)$since, 'until' => (int)$until], 'get', $this->dmPort);
        return $response;
    }

    /**
     * @param array $ids
     * @param $since
     * @param $until
     * @return array
     * @throws GeneralException
     */
    public function thingsSampleData($ids, $since, $until)
    {
        Log::debug("Core Things Sample Data");
        $url = '/api/things/w';
        $response = $this->_send($url, ['since' => (int)$since, 'until' => (int)$until, 'thing_ids' => $ids], 'post', $this->dmPort);
        return $response;
    }


    /**
     * @param array $ids
     * @param $since
     * @param $until
     * @return array
     * @throws GeneralException
     */
    public function thingsMainData($ids, $since, $until)
    {
        Log::debug("Core Things Data");
        $url = '/api/things';
        $response = $this->_send($url, ['since' => (int)$since, 'until' => (int)$until, 'thing_ids' => $ids], 'post', $this->dmPort);
        return $response;
    }


    /**
     * @param Project $project
     * @param Scenario $scenario
     * @return array
     * @throws GeneralException
     */
    public function sendScenario(Project $project, Scenario $scenario)
    {
        Log::debug("Core Send Scenario\t" . $project['_id']);
        $url = '/api/scenario';
        $response = $this->_send($url, ['code' => $scenario->code, 'id' => $project['container']['name']], 'post', $project['container']['runner']['port']);
        return $response;
    }


    /**
     * @param Project $project
     * @param $code
     * @return array
     * @throws GeneralException
     */
    public function lint(Project $project, $code)
    {
        Log::debug("Core Lint\t" . $project['_id']);
        $url = '/api/lint';
        $response = $this->_send($url, $code, 'post', $project['container']['runner']['port']);
        return $response;
    }

    /**
     * @param $mac
     * @return array
     * @throws GeneralException
     */
    public function enableGateway($mac)
    {
        Log::debug("Core Enable Gateway\t" . $mac);
        $url = '/api/gateway/' . $mac . '/enable';
        $response = $this->_send($url, [], 'get', $this->dmPort);
        return $response;
    }


    /**
     * @return array
     * @throws GeneralException
     */
    public function projectList()
    {
        Log::debug("Core Project List");
        $url = '/api/project';
        $response = $this->_send($url, [], 'get', $this->pmPort);
        return $response;
    }

    /**
     * @param $project_id
     * @param $limit
     * @return array
     * @throws GeneralException
     */
    public function projectLogs($project_id, $limit)
    {
        //Log::debug("Core Project Log");
        $url = '/api/project/' . $project_id . '/logs?limit=' . $limit;
        $response = $this->_send($url, [], 'get', $this->pmPort);
        return $response;
    }

    /**
     * @param $mac
     * @param $since
     * @return array
     * @throws GeneralException
     */
    public function gatewayFrames($mac, $since)
    {
        //Log::debug("Core Project Log");
        $url = '/api/gateway/' . $mac;
        $response = $this->_send($url, ['since' => $since], 'get', $this->dmPort);
        return $response;
    }

    /**
     * @param $appskey
     * @param $netskey
     * @param $phyPayload
     * @return array
     * @throws GeneralException
     */
    public function decryptPhyPayload($appskey, $netskey, $phyPayload)
    {
        Log::debug("Core Decrypt PhyPalayload");
        $url = '/api/decrypt';
        $data = [
            'appskey' => $appskey,
            'netskey' => $netskey,
            'phy_payload' => $phyPayload,
        ];
        $response = $this->_send($url, $data, 'post', $this->gmPort);
        return $response;
    }

    /**
     * @param Project $project
     * @param Thing $thing
     * @param $data
     * @param int $fport
     * @param bool $confirmed
     * @return array
     * @throws GeneralException
     */
    public function downLinkThing(Project $project, Thing $thing, $data, $fport = 2, $confirmed = false)
    {
        Log::debug("DownLink Project List\t" . $thing['dev_eui']);
        $url = '/api/send';
        $data = [
            'application_id' => $project['application_id'],
            'thing_id' => $thing['interface']['devEUI'],
            'data' => $data, 'confirmed' => $confirmed, 'fport' => intval($fport)
        ];
        $response = $this->_send($url, $data, 'post', $this->downLinkPort);
        return $response;
    }


    /**
     * @param $url
     * @param $data
     * @param string $method
     * @param string $port
     * @return array|object
     * @throws GeneralException
     */
    private function _send($url, $data, $method, $port)
    {
        if (env('CORE_TEST') == 1) {
            return $this->fake();
        }

        $url = $this->base_url . ':' . $port . $url;

        $response = $this->curlService->to($url)
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
        throw new GeneralException(
            $new_response->content->error ?: '',
            $new_response->status
        );

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
