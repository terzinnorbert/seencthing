<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class StartSyncthing extends Command
{
    const SYNCTHING = 'syncthing';
    const PATH = 'syncthing';
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
        if (false === config('syncthing.host', false)) {
            $this->error('Missing SYNCTHING_API_KEY: run php artisan syncthing:key:generate');
            exit(1);
        }

        system(
            self::SYNCTHING.' -no-browser -gui-address="'.config('syncthing.host').'" -gui-apikey="'.config(
                'syncthing.key'
            ).'" -home='.storage_path(self::PATH)
        );
    }
}
