<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StartSyncthing extends Command
{
    const SYNCTHING = 'syncthing';
    const PATH = 'storage/syncthing';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syncthing:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start syncthing service';

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
        system(
            self::SYNCTHING.' -no-browser -gui-address="'.config('syncthing.host').'" -gui-apikey="'.config(
                'syncthing.key'
            ).'" -home='.self::PATH
        );
    }
}
