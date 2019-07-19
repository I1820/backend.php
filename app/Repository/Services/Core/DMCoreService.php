<?php


namespace App\Repository\Services\Core;



use App\Exceptions\CoreException;

class DMCoreService extends AbstractCoreService
{

    /**
     * @return string
     */
    protected function url()
    {
        $url = (string)config('iot.core.dm.url');
        return $url;
    }

    /**
     * @param string $thing_id
     * @param int $since
     * @param int $until
     * @param int $limit
     * @return array
     * @throws CoreException
     */
    public function fetchThing(string $thing_id, int $since, int $until, int $limit = 0)
    {
        $path = '/api/queries/things/' . $thing_id . '/fetch';
        $data = [
            'since' => $since
        ];
        if ($until) {
            $data['until'] = $until;
        } else {
            $data['limit'] = $limit;
        }
        return $this->fetch($path, $data, 'GET');
    }


    /**
     * @param array<string> $ids
     * @param int $since
     * @param int $until
     * @param int $limit
     * @param int $offset
     * @return array
     * @throws CoreException
     */
    public function fetchThings(array $ids, int $since, int $until, int $limit, int $offset)
    {
        $path = '/api/queries/fetch';
        $payload = [
            'since' => $since,
            'thing_ids' => $ids
        ];
        if ($limit) {
            $payload['limit'] = $limit;
            $payload['offset'] = $offset;
        } else {
            $payload['until'] = $until;
        }
        return $this->fetch($path, $payload, 'POST');
    }

    /**
     * @param string $thing_id
     * @return array
     * @throws CoreException
     */
    public function lastParsed(string $thing_id)
    {
        $path = '/api/queries/things/' . $thing_id . '/parsed';
        return $this->fetch($path, [], 'GET');
    }


}