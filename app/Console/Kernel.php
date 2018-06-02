<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('syncthing:sync:folders')->cron('*/'.config('syncthing.cron.folders', 5).' * * * *');
        $schedule->command('syncthing:sync:directories')->cron('*/'.config('syncthing.cron.directories', 5).' * * * *');
        $schedule->command('syncthing:delete-expired-files')->cron(
            '*/'.config('syncthing.cron.expiration', 5).' * * * *'
        );
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
