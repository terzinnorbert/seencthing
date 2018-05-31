<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateApiKey extends Command
{
    const KEY_LENGTH = 32;
    const CONFIG_VARIABLE = 'syncthing.key';
    const ENV_VARIABLE = "SYNCTHING_API_KEY";
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syncthing:key:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates api key';

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

    /**
     * Set the key in the environment file.
     *
     * @param  string $key
     * @return bool
     */
    protected function setKeyInEnvironmentFile($key)
    {
        if (0 !== strlen($this->laravel['config'][self::CONFIG_VARIABLE])) {
            return false;
        }

        $this->writeNewEnvironmentFileWith($key);

        return true;
    }

    /**
     * Write a new environment file with the given key.
     *
     * @param  string $key
     * @return void
     */
    protected function writeNewEnvironmentFileWith($key)
    {
        file_put_contents(
            $this->laravel->environmentFilePath(),
            preg_replace(
                $this->keyReplacementPattern(),
                self::ENV_VARIABLE.'='.$key,
                file_get_contents($this->laravel->environmentFilePath())
            )
        );
    }

    /**
     * Get a regex pattern that will match ENV_VARIABLE with any random key.
     *
     * @return string
     */
    protected function keyReplacementPattern()
    {
        $escaped = preg_quote('='.$this->laravel['config'][self::CONFIG_VARIABLE], '/');

        return "/^".self::ENV_VARIABLE."{$escaped}/m";
    }
}
