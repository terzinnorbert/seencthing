<?php

namespace App\Http\Controllers;

use App\Client\Rest;
use App\Directory;
use App\Folder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DirectoryController extends Controller
{
    /**
     * @var Rest
     */
    private $client;

    public function __construct()
    {
        $this->client = app(Rest::class);
    }

    /**
     * @param Request $request
     * @param Folder $folder
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function listing(Request $request, Folder $folder)
    {
        $path = $request->input('path', '/');
        if (empty($path)) {
            $request->merge(['path' => $path = '/']);
        }

        return view(
            'directory.listing',
            [
                'devices'         => $this->getFolderDevices($folder),
                'connections'     => $this->client->getConnections(),
                'folder'          => $folder,
                'foldersAndFiles' => $folder->directory()->path($path)->orderBy('type')->get(),
            ]
        );
    }

    /**
     * @param Folder $folder
     * @param Directory $directory
     * @return \Illuminate\Http\JsonResponse
     */
    public function markToDownload(Folder $folder, Directory $directory)
    {
        $folder->includeFile($directory);

        return $this->success();
    }

    /**
     * @param Folder $folder
     * @param Directory $directory
     * @return \Illuminate\Http\JsonResponse
     */
    public function isDownloadable(Folder $folder, Directory $directory)
    {
        $localSize = 0;
        if (file_exists($directory->getStoragePath())) {
            $localSize = filesize($directory->getStoragePath());
        }

        return $this->success(
            [
                'downloadable' => $directory->isDownloadable(),
                'progress'     => number_format($localSize / (int)$directory->size, 0) * 100,
            ]
        );
    }

    /**
     * @param Folder $folder
     * @param Directory $directory
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(Folder $folder, Directory $directory)
    {
        while (!$directory->isDownloadable()) {
            sleep(2);
        }

        $directory->state = Directory::STATE_DOWNLOADED;
        $directory->expiration_time = Carbon::now()->addMinutes(15);
        $directory->save();

        return $directory->getFile();
    }

    /**
     * @param Folder $folder
     * @return array
     */
    protected function getFolderDevices(Folder $folder)
    {
        $folderName = $folder->name;
        $devices = $this->client->getDevices();

        foreach ($this->client->getFolders() as $folder) {
            if ($folderName == $folder['id']) {
                return array_map(
                    function ($device) use ($devices) {
                        return $devices[$device['deviceID']];
                    },
                    $folder['devices']
                );
            }
        }

        return [];
    }
}