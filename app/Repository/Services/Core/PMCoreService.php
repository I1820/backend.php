<?php


namespace App\Repository\Services\Core;



use App\Exceptions\CoreException;
use App\Thing;

class PMCoreService extends AbstractCoreService
{

    protected function url()
    {
        $url = (string)config('iot.core.pm.url');
        return $url;
    }

    /**
     * @param string $id
     * @param string $email project owner email address
     * @return array
     * @throws CoreException
     */
    public function create(string $id, string $email)
    {
        $path = '/api/projects';
        $data = [
            'name' => $id,
            'owner' => $email,
        ];
        return $this->fetch($path, $data, 'POST');
    }

    /**
     * @param string $project_id
     * @return array
     * @throws CoreException
     */
    public function delete(string $project_id)
    {
        $path = '/api/projects/' . $project_id;
        return $this->fetch($path, [], 'DELETE');
    }

    /**
     * @return array
     * @throws CoreException
     */
    public function list()
    {
        $path = '/api/projects';
        return $this->fetch($path, [], 'GET');
    }

    /**
     * @param string $project_id
     * @param int $limit
     * @return array
     * @throws CoreException
     */
    public function logs(string $project_id, int $limit)
    {
        $path = '/api/projects/' . $project_id . '/logs?limit=' . $limit;
        return $this->fetch($path, [], 'GET');
    }


}
