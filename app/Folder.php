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
}
