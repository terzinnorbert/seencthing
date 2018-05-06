<?php

namespace App\Client;

use SyncthingRest\Client;

class Rest extends Client
{
    public function __construct()
    {
        parent::__construct(env('SYNCTHING_HOST'), env('SYNCTHING_API_KEY'));
    }
}