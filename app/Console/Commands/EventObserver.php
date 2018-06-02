<?php

namespace App\Console\Commands;

use App\Client\Process;
use App\Client\Rest;
use App\Events\FolderRejected;
use Carbon\Carbon;
use Illuminate\Console\Command;

class EventObserver extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syncthing:event';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Observes the syncthing events';
    /**
     * @var Rest
     */
    private $client;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->client = app(Rest::class);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $startTime = Carbon::now();
        $maxId = 0;
        while (true) {
            if (!Process::isRunning()) {
                sleep(60);
                continue;
            }
            $events = $this->client->getEvents($maxId);
            if (count($events)) {
                foreach ($events as $event) {
                    if ($startTime <= Rest::convertTime($event['time'])) {
                        if (FolderRejected::NAME == $event['type']) {
                            event(new FolderRejected($event));
                        }
                    }
                    $maxId = $event['id'];
                }
            } else {
                sleep(15);
            }
        }
    }
}
