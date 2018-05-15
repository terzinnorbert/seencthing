<?php

namespace App\Client;

use SyncthingRest\Client;

class Rest extends Client
{
    public function __construct()
    {
        parent::__construct(env('SYNCTHING_HOST'), env('SYNCTHING_API_KEY'));
    }

    public function getIgnores($folder)
    {
        $response = $this->getDbIgnores($folder);
        if (empty($response['ignore'])) {
            return [];
        }

        return $response['ignore'];
    }
}