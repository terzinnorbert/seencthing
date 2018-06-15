<?php

namespace App\Console\Commands;

use App\Folder;

class SyncDirectories extends SyncthingCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syncthing:sync:directories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes syncthing directories';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach (Folder::all() as $folder) {
            $folder->syncDirectoryFromSyncthing();
        }
    }
}
