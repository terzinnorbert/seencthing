<?php

namespace App\Client;

use SyncthingRest\Client;

class Rest extends Client
{
    public function __construct()
    {
        parent::__construct(env('SYNCTHING_HOST'), env('SYNCTHING_API_KEY'));
    }

    /**
     * @param string $folder
     * @return array
     */
    public function getIgnores($folder)
    {
        $response = $this->getDbIgnores($folder);
        if (empty($response['ignore'])) {
            return [];
        }

        return $response['ignore'];
    }

    /**
     * @return array
     */
    public function getConnections()
    {
        $connections = $this->getSystemConnections();

        return $connections['connections'];
    }
}