<?php

namespace App\Console\Commands;

use App\Folder;
use Illuminate\Console\Command;

class SyncPreview extends Command
{
    protected $signature = 'syncthing:sync:preview';
    protected $description = 'Synchronizes preview';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach (Folder::all() as $folder) {
            $folder->syncDirectoryPreview();
        }
    }
}
