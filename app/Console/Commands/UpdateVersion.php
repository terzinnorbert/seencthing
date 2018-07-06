<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Artisan;

class UpdateVersion extends AbstractEnvironmentCommand
{
    const CONFIG_VARIABLE = 'app.version';
    const ENV_VARIABLE = 'APP_VERSION';
    protected $signature = 'update-version';
    protected $description = 'Update application version';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $version = $this->fetchVersion();

        $this->setKeyInEnvironmentFile($version, true);
        Artisan::call('config:cache');

        $this->info('Current version: '.$version);
    }

    /**
     * Fetch the current version from git
     *
     * @return string
     */
    protected function fetchVersion()
    {
        $describe = exec('git describe --tags');
        $describe = explode('-', $describe);

        // v0.1.0-3-g2f6c6a6 -> v0.1.0 (g2f6c6a6)
        if (count($describe) > 2) {
            return $describe[0].' ('.$describe[2].')';
        }

        return current($describe);
    }
}
