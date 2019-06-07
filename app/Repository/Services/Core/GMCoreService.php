<?php


namespace App\Repository\Services\Core;


use App\Exceptions\CoreException;

class GMCoreService extends AbstractCoreService
{

    /**
     * @return string
     */
    protected function url()
    {
        $url = (string)config('iot.core.gm.url');
        return $url;
    }

    /**
     * @param string $appSKey
     * @param string $netSKey
     * @param string $phyPayload
     * @return array
     * @throws CoreException
     */
    public function decrypt(string $appSKey, string $netSKey, string $phyPayload) {
        $path = '/api/decrypt';
        $data = [
            'appskey' => $appSKey,
            'netskey' => $netSKey,
            'phy_payload' => $phyPayload,
        ];
        return $this->fetch($path, $data, 'POST');
    }
}