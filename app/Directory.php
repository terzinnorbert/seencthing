<?php

namespace App;

use Carbon\Carbon;
use App\Client\Rest;
use Illuminate\Database\Eloquent\Model;

class Directory extends Model
{
    const PATH_SYNCTHING = 'share';

    const FOLDER_DEPTH = 5;
    const TYPE_FOLDER = 0;
    const TYPE_FILE = 1;

    const STATE_AVAILABLE = 0;
    const STATE_DOWNLOAD_IN_PROGRESS = 1;
    const STATE_DOWNLOADED = 2;

    protected $fillable = [
        'folder_id',
        'path',
        'name',
        'type',
        'sync_time',
        'modification_time',
        'size',
        'state',
        'expiration_time',
    ];

    public static function syncFromSyncthing()
    {
        $syncStartDate = Carbon::now();
        foreach (Folder::all() as $folder) {
            $folder->syncDirectoryFromSyncthing();
        }
        Directory::where('sync_time', '<', $syncStartDate)->delete();
    }

    /**
     * @param integer $folderId
     * @param array $structure
     * @param string $path
     */
    public static function syncFilesAndFoldersFromResponse($folderId, array $structure, $path = '/')
    {
        $files = $folders = [];
        foreach ($structure as $name => $data) {
            if (array_key_exists(0, $data)) {
                $files[] = $name;
            } else {
                $folders[] = $name;
            }
        }

        if (!empty($folders)) {
            foreach ($folders as $folder) {
                $directory = self::updateOrCreate(
                    [
                        'name'      => $folder,
                        'path'      => $path,
                        'folder_id' => $folderId,
                        'type'      => self::TYPE_FOLDER,
                    ],
                    [
                        'modification_time' => Carbon::now(),
                        'sync_time'         => Carbon::now(),
                        'size'              => 0,
                    ]
                );
                self::syncFilesAndFoldersFromResponse(
                    $folderId,
                    $structure[$folder],
                    self::generatePath($path, $folder)
                );
            }
        }
        if (!empty($files)) {
            foreach ($files as $file) {
                self::updateOrCreate(
                    [
                        'name'      => $file,
                        'path'      => $path,
                        'folder_id' => $folderId,
                        'type'      => self::TYPE_FILE,
                    ],
                    [
                        'modification_time' => Rest::convertTime($structure[$file][0]),
                        'sync_time'         => Carbon::now(),
                        'size'              => $structure[$file][1],
                    ]
                );
            }
        }
    }

    /**
     * @param string $basePath
     * @param string $folder
     * @return string
     */
    public static function generatePath($basePath, $folder)
    {
        return rtrim($basePath, '/').'/'.$folder;
    }

    /**
     * @param string $path
     * @return string
     */
    public static function generateParentPath($path = '/')
    {
        if (is_null($path) || '/' == $path) {
            return '/';
        }

        $path = explode('/', $path);
        if (2 == count($path)) {
            return self::generateParentPath();
        }

        return implode('/', array_slice($path, 0, -1));
    }

    /**
     * @return Folder|\Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function folder()
    {
        return $this->belongsTo('App\Folder');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeFolders($query)
    {
        return $query->where('type', self::TYPE_FOLDER);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeFiles($query)
    {
        return $query->where('type', self::TYPE_FILE);
    }

    /**
     * @param $query
     * @param $path
     * @return mixed
     */
    public function scopePath($query, $path)
    {
        return $query->where('path', $path);
    }

    /**
     * @return bool
     */
    public function isFile()
    {
        return self::TYPE_FILE === $this->type;
    }

    /**
     * @return bool
     */
    public function isFolder()
    {
        return !$this->isFile();
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return self::generatePath($this->path, $this->name);
    }

    /**
     * @return bool
     */
    public function isDownloadable()
    {
        $response = app(Rest::class)->getDbFile($this->folder->name, $this->name);

        return false === $response['local']['invalid'];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function getFile()
    {
        return response()->download($this->getStoragePath());
    }

    /**
     * @param int $minutes
     */
    public function setExpiration($minutes = 15)
    {
        $this->expiration_time = Carbon::now()->addMinutes($minutes);
        $this->save();
    }

    /**
     * @return string
     */
    public function getStoragePath()
    {
        return storage_path(self::PATH_SYNCTHING).'/'.$this->folder->name.$this->getPath();
    }
}
