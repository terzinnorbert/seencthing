<?php

namespace App\Console\Commands;

use App\Folder;

class DeleteExpiredFiles extends SyncthingCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syncthing:delete-expired-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes expired files';

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
        /**
         * @var Folder $folder
         */
        foreach (Folder::all() as $folder) {
            $folder->deleteExpiredFiles();
        }
    }
}
