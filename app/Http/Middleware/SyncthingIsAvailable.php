<?php

namespace App\Http\Middleware;

use App\Client\Process;
use Closure;

class SyncthingIsAvailable
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Process::isRunning()) {
            return response(view('layouts.error', ['error' => 'Syncthing client service is not available']));
        }

        return $next($request);
    }
}
