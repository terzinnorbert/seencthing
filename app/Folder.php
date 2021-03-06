<?php

namespace App;

use Carbon\Carbon;
use App\Client\Rest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Folder extends Model
{
    const DIRECTORY_SYNC_LIMIT = 100;
    protected $fillable = ['name', 'label', 'scan_time', 'sync_time'];

    public static function syncFromSyncthing()
    {
        $syncStartDate = Carbon::now();
        $client = app(Rest::class);
        $names = $client->getFoldersNames();
        $folders = $client->getStatsFolder();

        foreach ($folders as $name => $data) {
            self::updateOrCreate(
                ['name' => $name, 'label' => $names[$name]],
                [
                    'scan_time' => Rest::convertTime($data['lastScan']),
                    'sync_time' => Carbon::now(),
                ]
            );
        }
        Folder::where('sync_time', '<', $syncStartDate)->delete();
    }

    /**
     * @param $size
     * @param int $delimiter
     * @return string
     */
    public static function fileSize($size, $delimiter = 2)
    {
        $labels = 'kMGTPEZY';
        $separator = (int)floor((strlen($size) - 1) / 3);

        return sprintf(
                "%.{$delimiter}f",
                $size / pow(1024, $separator)
            ).' '.($separator ? $labels[$separator - 1] : '').'B';
    }

    /**
     * @return Folder[]
     */
    public static function getFolders()
    {
        $client = app(Rest::class);
        $names = $client->getFoldersNames();
        $folders = [];
        foreach (Folder::orderBy('name')->get() as $folder) {
            $folder->label = $names[$folder->name];
            $folders[$folder->name] = $folder;
        }

        return $folders;
    }

    /**
     * @return Directory[]|\Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function directory()
    {
        return $this->hasMany(Directory::class);
    }

    /**
     * @return array
     */
    public function getStatus()
    {
        return app(Rest::class)->getDbStatus($this->name);
    }

    /**
     * @param Directory $file
     * @return bool
     */
    public function includeFile(Directory $file)
    {
        $client = app(Rest::class);
        $ignores = $client->getIgnores($this->name);

        if (false !== array_search('!'.$file->getPath(), $ignores)) {
            return false;
        }

        unset($ignores[array_search('**', $ignores)]);
        $ignores[] = '!'.$file->getPath();
        $ignores[] = '**';

        $client->postDbIgnores($this->name, $ignores);

        $file->state = Directory::STATE_DOWNLOAD_IN_PROGRESS;
        $file->save();

        return true;
    }

    /**
     * @param Collection $files
     * @return bool
     */
    public function excludeFiles(Collection $files)
    {
        $client = app(Rest::class);
        $ignores = $client->getIgnores($this->name);

        foreach ($files as $fileKey => $file) {
            if (false !== ($index = array_search('!'.$file->getPath(), $ignores))) {
                unset($ignores[$index]);
            }
        }

        $client->postDbIgnores($this->name, $ignores);

        foreach ($files as $file) {
            $file->state = Directory::STATE_AVAILABLE;
            $file->expiration_time = null;
            $file->save();

            if (file_exists($path = $file->getStoragePath())) {
                unlink($path);
            }
            $file->deleteEmptyParentFolders();
        }

        return true;
    }

    /**
     * @return bool
     */
    public function deleteExpiredFiles()
    {
        $files = $this->directory()->where('state', Directory::STATE_DOWNLOADED)->where(
            'expiration_time',
            '<',
            Carbon::now()
        )->get();

        return $this->excludeFiles($files);
    }

    public function syncDirectoryFromSyncthing()
    {
        $syncStartDate = Carbon::now();
        $structure = app(Rest::class)->getDbBrowse($this->name, Directory::FOLDER_DEPTH);
        Directory::syncFilesAndFoldersFromResponse($this->id, $structure);
        Directory::where('sync_time', '<', $syncStartDate)
            ->where('folder_id', $this->id)
            ->whereNull('expiration_time')
            ->delete();
    }

    public function syncDirectoryPreview()
    {
        if ($this->hasOnlineDevice()) {
            foreach ($this->directory()->hasNoPreview()->files()->limit(self::DIRECTORY_SYNC_LIMIT)->get(
            ) as $directory) {
                $directory->createPreview();
            }
        }
    }

    /**
     * @return array
     */
    public function getDevices()
    {
        /**
         * @var Rest $client
         */
        $client = app(Rest::class);
        $devices = [];
        $connections = $client->getConnections();
        foreach ($client->getFolderDevices($this->name) as $device) {
            if (array_key_exists($device['deviceID'], $connections)) {
                $devices[$device['deviceID']] = [
                    'name'  => $device['name'],
                    'id'    => $device['deviceID'],
                    'state' => $connections[$device['deviceID']]['connected'] ? 'online' : 'offline',
                ];
            }
        }

        return $devices;
    }

    /**
     * @return array
     */
    public function getOnlineDevices()
    {
        return array_filter(
            $this->getDevices(),
            function ($device) {
                return 'online' == $device['state'];
            }
        );
    }

    /**
     * @return bool
     */
    public function hasOnlineDevice()
    {
        return !!count($this->getOnlineDevices());
    }
}
