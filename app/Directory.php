<?php

namespace App;

use Carbon\Carbon;
use App\Client\Rest;
use Illuminate\Database\Eloquent\Model;

class Directory extends Model
{
    const FOLDER_DEPTH = 5;
    const TYPE_FOLDER = 0;
    const TYPE_FILE = 1;

    protected $fillable = [
        'parent_id',
        'folder_id',
        'name',
        'type',
        'sync_time',
        'modification_time',
        'size',
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

    public static function syncFilesAndFoldersFromResponse($folderId, $structure, $parentId = 0)
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
                        'parent_id' => $parentId,
                        'name'      => $folder,
                        'folder_id' => $folderId,
                        'type'      => self::TYPE_FOLDER,
                    ],
                    [
                        'modification_time' => Carbon::now(),
                        'sync_time'         => Carbon::now(),
                        'size'              => 0,
                    ]
                );
                self::syncFilesAndFoldersFromResponse($folderId, $structure[$folder], $directory->id);
            }
        }
        if (!empty($files)) {
            foreach ($files as $file) {
                self::updateOrCreate(
                    [
                        'parent_id' => $parentId,
                        'name'      => $file,
                        'folder_id' => $folderId,
                        'type'      => self::TYPE_FILE,
                    ],
                    [
                        'sync_time'         => Carbon::now(),
                        'modification_time' => Rest::convertTime($structure[$file][0]),
                        'size'              => $structure[$file][1],
                    ]
                );
            }
        }
    }

    public function scopeFolders($query)
    {
        return $query->where('type', self::TYPE_FOLDER);
    }

    public function scopeFiles($query)
    {
        return $query->where('type', self::TYPE_FILE);
    }

    public function scopeParent($query, $parentId)
    {
        return $query->where('parent_id', $parentId);
    }
}
