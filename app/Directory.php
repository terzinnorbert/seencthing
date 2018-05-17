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
            $structure = app(Rest::class)->getDbBrowse($folder->name, self::FOLDER_DEPTH);
            self::syncFilesAndFoldersFromResponse($folder->id, $structure);
        }
        self::where('sync_time', '<', $syncStartDate)->delete();
    }

    public static function syncFilesAndFoldersFromResponse($folderId, $structure, $path = '/')
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

    public static function generatePath($basePath, $folder)
    {
        return rtrim($basePath, '/').'/'.$folder;
    }

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

    public function folder()
    {
        return $this->belongsTo('App\Folder');
    }

    public function scopeFolders($query)
    {
        return $query->where('type', self::TYPE_FOLDER);
    }

    public function scopeFiles($query)
    {
        return $query->where('type', self::TYPE_FILE);
    }

    public function scopePath($query, $path)
    {
        return $query->where('path', $path);
    }

    public function isFile()
    {
        return self::TYPE_FILE === $this->type;
    }

    public function isFolder()
    {
        return !$this->isFile();
    }

    public function getPath()
    {
        return self::generatePath($this->path, $this->name);
    }

    public function isDownloadable()
    {
        $response = app(Rest::class)->getDbFile($this->folder->name, $this->name);

        return false === $response['local']['invalid'];
    }

    public function getFile()
    {
        return response()->download($this->getStoragePath());
    }

    public function setExpiration($minutes = 15)
    {
        $this->expiration_time = Carbon::now()->addMinutes($minutes);
        $this->save();
    }

    public function getStoragePath()
    {
        return storage_path(self::PATH_SYNCTHING).'/'.$this->folder->name.$this->getPath();
    }
}
