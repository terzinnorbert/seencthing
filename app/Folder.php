<?php

namespace App;

use Carbon\Carbon;
use App\Client\Rest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    protected $fillable = ['name', 'scan_time', 'sync_time'];

    public static function syncFromSyncthing()
    {
        $syncStartDate = Carbon::now();
        $folders = app(Rest::class)->getStatsFolder();
        foreach ($folders as $name => $data) {
            self::updateOrCreate(
                ['name' => $name],
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

        dd($client->getDbFile($file->folder->name, $file->name));

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

        foreach ($files as $file) {
            unset($ignores[array_search('!'.$file->getPath(), $ignores)]);
        }

        $client->postDbIgnores($this->name, $ignores);

        foreach ($files as $file) {
            $file->state = Directory::STATE_AVAILABLE;
            $file->expiration_time = null;
            $file->save();

            unlink($file->getStoragePath());
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
        Directory::where('sync_time', '<', $syncStartDate)->where('folder_id', $this->id)->delete();
    }
}
