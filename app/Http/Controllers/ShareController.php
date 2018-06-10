<?php

namespace App\Http\Controllers;

use App\Directory;

class ShareController extends Controller
{
    public function index($hash)
    {
        /**
         * @var Directory $directory
         */
        $directory = Directory::where('hash', $hash)->firstOrFail();
        $devices = $directory->folder->getDevices();
        $peers = [
            'available' =>
                count(
                    array_filter(
                        $devices,
                        function ($device) {
                            return 'online' == $device['state'];
                        }
                    )
                ),
            'all'       => count($devices),
        ];

        return view('share.index', compact('directory', 'peers'));
    }

    public function download($hash)
    {
        /**
         * @var Directory $directory
         */
        $directory = Directory::where('hash', $hash)->firstOrFail();
        $directory->markToDownload();

        return $directory->download();
    }

    public function markToDownload($hash)
    {
        /**
         * @var Directory $directory
         */
        $directory = Directory::where('hash', $hash)->firstOrFail();
        $directory->markToDownload();
    }

    public function progress($hash)
    {
        /**
         * @var Directory $directory
         */
        $directory = Directory::where('hash', $hash)->firstOrFail();

        return $this->success(
            [
                'downloadable' => $directory->isDownloadable(),
                'progress'     => $directory->progress(),
            ]
        );
    }
}
