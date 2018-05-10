<?php

namespace App;

use Carbon\Carbon;
use App\Client\Rest;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    protected $fillable = ['name', 'scan_time', 'sync_time'];

    public static function syncFromSyncthing()
    {
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
    }

    public function directory()
    {
        return $this->hasMany(Directory::class);
    }

    public function getStatus()
    {
        return app(Rest::class)->getDbStatus($this->name);
    }

    public static function fileSize($size, $delimiter = 2)
    {
        $labels = 'kMGTPEZY';
        $separator = (int)floor((strlen($size) - 1) / 3);

        return sprintf(
                "%.{$delimiter}f",
                $size / pow(1024, $separator)
            ).' '.($separator ? $labels[$separator - 1] : '').'B';
    }
}
