<?php

namespace App\Http\Controllers;

use App\Directory;
use App\Share;

class ShareController extends AbstractFileManagerController
{
    const VIEW_FOLDER = 'share.folder';
    /**
     * @var $sharedDirectory Directory
     */
    protected $sharedDirectory;

    /**
     * @param Share $share
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function index(Share $share)
    {
        $this->sharedDirectory = $directory = $share->directory;
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

        if ($directory->isFolder()) {
            return $this->folderListing($directory->folder, $share);
        }

        return view('share.index', compact('directory', 'peers'));
    }

    /**
     * @param Share $share
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadFile(Share $share)
    {
        /**
         * @var Directory $directory
         */
        $directory = $share->$directory;
        $directory->markToDownload();

        return $directory->download();
    }

    /**
     * @param Share $share
     */
    public function markToDownloadFile(Share $share)
    {
        /**
         * @var Directory $directory
         */
        $directory = $share->$directory;
        $directory->markToDownload();
    }

    /**
     * @param Share $share
     * @return \Illuminate\Http\JsonResponse
     */
    public function progress(Share $share)
    {
        /**
         * @var Directory $directory
         */
        $directory = $share->$directory;

        return $this->success(
            [
                'downloadable' => $directory->isDownloadable(),
                'progress'     => $directory->progress(),
            ]
        );
    }

    /**
     * @param Directory $directory
     * @return string
     */
    public function getDirectoryPath(Directory $directory)
    {
        return str_replace_first($this->sharedDirectory->getPath(), '', $directory->getPath());
    }

    /**
     * @param Directory $directory
     * @param $path
     * @return string
     */
    protected function getNormalizedPath(Directory $directory, $path)
    {
        if ('/' == $path) {
            return $directory->getPath();
        }

        return $directory->getPath().$path;
    }

    /**
     * @return string
     */
    protected function getBreadcrumbHomeTitle()
    {
        return $this->sharedDirectory->name;
    }
}
