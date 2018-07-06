<?php

namespace App\Console\Commands;

class GenerateApiKey extends AbstractEnvironmentCommand
{
    const KEY_LENGTH = 32;
    const CONFIG_VARIABLE = 'syncthing.key';
    const ENV_VARIABLE = 'SYNCTHING_API_KEY';
    protected $signature = 'syncthing:key:generate';
    protected $description = 'Generates api key';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $key = $this->generateRandomKey();

        if (!$this->setKeyInEnvironmentFile($key)) {
            return;
        }

        $this->info("Syncthing key [$key] set successfully.");
    }

    /**
     * Generate a random key for syncthing.
     *
     * @return string
     */
    protected function generateRandomKey()
    {
        return str_random(self::KEY_LENGTH);
    }
}
