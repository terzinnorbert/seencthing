<?php

namespace App\Client;

class Process
{
    public static function isRunning()
    {
        return !empty(exec('ps aux | grep "syncthing -no-browser" | grep -v grep'));
    }
}