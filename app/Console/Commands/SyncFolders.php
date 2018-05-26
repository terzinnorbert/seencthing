<?php

namespace App\Console\Commands;

use App\Folder;
use Illuminate\Console\Command;

class SyncFolders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syncthing:sync:folders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes syncthing folders';

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
        Folder::syncFromSyncthing();
    }
}
