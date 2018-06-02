<?php

namespace App\Console\Commands;

use App\Client\Process;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class SyncthingCommand extends Command
{
    /**
     * Execute the console command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!Process::isRunning()) {
            Log::error(static::class.': Syncthing is not running');

            return false;
        }

        return $this->laravel->call([$this, 'handle']);
    }
}