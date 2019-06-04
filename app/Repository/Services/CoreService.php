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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Ixudra\Curl\CurlService;

class CoreService
{
    protected $base_url;
    protected $port;
    protected $dmPort;
    protected $pmPort;
    protected $gmPort;
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
    public function createProject($id)
    {
        Log::debug("Core Send Project\t" . $id);
        $url = '/api/projects';
        $data = [
            'name' => (string)$id,
            'envs' => Auth::user()->only(['email', 'name', '_id'])
        ];
        $response = $this->_send($url, $data, 'post', $this->pmPort);
        return $response;
    }

    /**
     * @param Project $project
     * @param bool $active
     * @return array
     * @throws GeneralException
     */
    public function activateProject(Project $project, $active = true)
    {
        if ($active) {
            Log::debug("Core Activate Thing\t" . $project['container']['name']);
            $url = '/api/projects/' . $project['container']['name'] . '/activate';
        } else {
            Log::debug("Core Deactivate Thing\t" . $project['container']['name']);
            $url = '/api/projects/' . $project['container']['name'] . '/deactivate';
        }
        $response = $this->_send($url, [], 'get', $this->pmPort);
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
        $url = '/api/projects/' . $project_id;
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
        $url = '/api/things';
        $data = [
            'name' => (string)$thing['interface']['devEUI'],
            'project' => (string)$project['container']['name'],
        ];
        $response = $this->_send($url, $data, 'post', $this->pmPort);
        return $response;
    }

    /**
     * @param Thing $thing
     * @return array
     * @throws GeneralException
     */
    public function getThing(Thing $thing)
    {
        Log::debug("Core Get Thing\t" . $thing['dev_eui']);
        $url = '/api/things/' . $thing['dev_eui'];
        $response = $this->_send($url, [], 'get', $this->pmPort);
        return $response;
    }

    /**
     * @param Thing $thing
     * @return array
     * @throws GeneralException
     */
    public function getThingLastParsed(Thing $thing)
    {
        Log::debug("Core Get Thing\t" . $thing['dev_eui']);
        $url = '/api/things/' . $thing['dev_eui'] . '/p';
        $response = $this->_send($url, [], 'get', $this->dmPort);
        return $response;
    }


    /**
     * @param Thing $thing
     * @param bool $active
     * @return array
     * @throws GeneralException
     */
    public function activateThing(Thing $thing, $active = true)
    {
        if ($active) {
            Log::debug("Core Activate Thing\t" . $thing['dev_eui']);
            $url = '/api/things/' . $thing['dev_eui'] . '/activate';
        } else {
            Log::debug("Core Deactivate Thing\t" . $thing['dev_eui']);
            $url = '/api/things/' . $thing['dev_eui'] . '/deactivate';
        }
        $response = $this->_send($url, [], 'get', $this->pmPort);
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
        $url = '/api/runners/' . $project['container']['name'] . '/codec';
        $response = $this->_send($url, ['code' => $codec, 'id' => $thing['interface']['devEUI']], 'post', $this->pmPort);
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
        $url = '/api/runners/' . $project['container']['name'] . '/encode/' . $thing['interface']['devEUI'];
        $response = $this->_send($url, $data, 'post', $this->pmPort);
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
        $url = '/api/runners/' . $project['container']['name'] . '/decode/' . $thing['interface']['devEUI'];
        $response = $this->_send($url, $data, 'post', $this->pmPort);
        return $response;
    }

    public function gatewayEvent($gateway, $timestamp, $limit)
    {
        $url = $url = $this->dm_url . '/api/gateway/' . $gateway . '?since=' . $timestamp . '&limit=' . $limit;
        return $this->send($url, [], 'get');
    }


    /**
     * @param Thing $thing
     * @param $since
     * @param $until
     * @param $limit
     * @return array
     * @throws GeneralException
     */
    public function thingData(Thing $thing, $since, $until, $limit = 0)
    {
        Log::debug("Core Thing Data");
        $url = '/api/things/' . $thing['interface']['devEUI'];
        $data = ['since' => (int)$since];
        if ($until)
            $data['until'] = (int)$until;
        else
            $data['limit'] = (int)$limit;
        $response = $this->_send($url, $data, 'get', $this->dmPort);
        return $response;
    }

    /**
     * @param array $ids
     * @param $since
     * @param $until
     * @param int $cluster_number
     * @return array
     * @throws GeneralException
     */
    public function thingsSampleData($ids, $since, $until, $cluster_number = 200)
    {
        Log::debug("Core Things Sample Data");
        $url = '/api/things/w';
        $response = $this->_send($url, [
            'since' => (int)$since,
            'until' => (int)$until,
            'thing_ids' => $ids,
            'cn' => $cluster_number
        ], 'post', $this->dmPort);
        return $response;
    }


    /**
     * @param array $ids
     * @param $since
     * @param $until
     * @param $limit
     * @param $offset
     * @return array
     * @throws GeneralException
     */
    public function thingsMainData($ids, $since, $until, $limit, $offset)
    {
        Log::debug("Core Things Data");
        $url = '/api/queries/fetch';
        $data = ['since' => (int)$since, 'thing_ids' => $ids];
        if ($limit) {
            $data['limit'] = (int)$limit;
            $data['offset'] = (int)$offset;
        } else
            $data['until'] = (int)$until;
        $response = $this->_send($url, $data, 'post', $this->dmPort);
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
        $url = '/api/runners/' . $project['container']['name'] . '/scenario';
        $response = $this->_send($url, ['code' => $scenario->code, 'id' => $project['container']['name']], 'post', $this->pmPort);
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
        $url = '/api/runners/' . $project['container']['name'] . '/lint';
        $response = $this->_send($url, $code, 'post', $this->pmPort);
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
        $url = '/api/projects';
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
        $url = '/api/projects/' . $project_id . '/errors/project?limit=' . $limit;
        $response = $this->_send($url, [], 'get', $this->pmPort);
        return $response;
    }

    /**
     * @param $project_id
     * @param $limit
     * @return array
     * @throws GeneralException
     */
    public function loraLogs($project_id, $limit)
    {
        //Log::debug("Core Project Log");
        $url = '/api/projects/' . $project_id . '/errors/lora?limit=' . $limit;
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
        Log::debug("Core Decrypt PhyPayload");
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
            'data' => $data, 'confirmed' => $confirmed, 'fport' => (int)$fport
        ];
        $response = $this->_send($url, $data, 'post', $this->downLinkPort);
        return $response;
    }


    /**
     * General send function for core services that handles errors
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
            ->withTimeout(100)
            ->withHeader('Authorization: ' . env('CORE_SECRET'))
            ->withData($data)
            ->withOption('SSL_VERIFYHOST', false)
            ->returnResponseObject()
            ->asJsonRequest()
            ->asJsonResponse()
            ->withTimeout('60');
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
            throw new GeneralException('Core service connection error', 500);
        }
        if ($new_response->status == 200) {
            return $new_response->content ?: [];
        }
        if ($new_response->status != 200) {
            if ($new_response->content) {
                throw new GeneralException(
                    $new_response->content->error ?: 'Unknown Core service error',
                    $new_response->status
                );
            } else {
                throw new GeneralException('Core service connection error', $new_response->status);
            }
        }
    }

    public function fake()
    {
        return (object)[
            'status' => 200,
            'content' => [
                'key' => 'value'
            ],
            'name' => 'project',
            'port' => 1212,
            'lastSeenAt' => ''
        ];
    }
}
