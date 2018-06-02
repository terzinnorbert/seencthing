<?php

return [
    'host' => env('SYNCTHING_HOST'),
    'key'  => env('SYNCTHING_API_KEY'),

    'cron' => [
        'folders'     => env('SYNCTHING_CRON_FOLDERS', 5),
        'directories' => env('SYNCTHING_CRON_DIRECTORIES', 5),
        'expiration'  => env('SYNCTHING_CRON_EXPIRATION', 5),
    ],
];