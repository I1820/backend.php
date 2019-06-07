<?php


namespace App\Repository\Services\Core;



use App\Exceptions\CoreException;
use App\Thing;

class TMCoreService extends AbstractCoreService
{

    protected function url()
    {
        $url = (string)config('iot.core.tm.url');
        return $url;
    }

    /**
     * @param string $project_id
     * @param Thing $thing
     * @return array
     * @throws CoreException
     */
    public function create(string $project_id, Thing $thing) {
        $data = [
            'name' => (string)$thing['interface']['devEUI'],
            'model' => (string)$thing['model'],
            'location' => [
                'lat' => floatval($thing['loc']['coordinates'][1]),
                'long' => floatval($thing['loc']['coordinates'][0]),
            ]
        ];
        $path = '/api/projects/' . $project_id . '/things';
        return $this->fetch($path, $data, 'POST');
    }

    /**
     * @param string $thing_id
     * @return array
     * @throws CoreException
     */
    public function delete(string $thing_id) {
        $path = '/api/things/' . $thing_id;
        return $this->fetch($path, [], 'DELETE');
    }

    /**
     * @param string $thing_id
     * @return array
     * @throws CoreException
     */
    public function show(string $thing_id) {
        $path = '/api/things/' . $thing_id;
        return $this->fetch($path, [], 'GET');
    }

    /**
     * @param string $thing_id
     * @param bool $activate
     * @return array
     * @throws CoreException
     */
    public function activation(string $thing_id, bool $activate) {
        if ($activate) {
            $path = '/api/things/' . $thing_id . '/activate';
        } else {
            $path = '/api/things/' . $thing_id . '/deactivate';
        }
        return $this->fetch($path, [], 'GET');
    }
}
