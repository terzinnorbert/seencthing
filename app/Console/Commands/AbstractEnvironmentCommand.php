<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

abstract class AbstractEnvironmentCommand extends Command
{
    const CONFIG_VARIABLE = '';
    const ENV_VARIABLE = '';
    protected $signature = 'command:name';
    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Set the key in the environment file.
     *
     * @param  string $key
     * @param bool $force
     * @return bool
     */
    protected function setKeyInEnvironmentFile($key, $force = false)
    {
        if (!$force && 0 !== strlen($this->laravel['config'][static::CONFIG_VARIABLE])) {
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
                static::ENV_VARIABLE.'='.$this->quote($key),
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
        $escaped = preg_quote('='.$this->laravel['config'][static::CONFIG_VARIABLE], '/');

        return "/^".static::ENV_VARIABLE."{$escaped}/m";
    }

    protected function quote($key)
    {
        return '"'.$key.'"';
    }
}
