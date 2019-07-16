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
     * @throws GeneralException
     */
    public function delete($project_id)
    {
        $path = '/api/projects/' . $project_id;
        return $this->fetch($path, [], 'DELETE');
    }

}
