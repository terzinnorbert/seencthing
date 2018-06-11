<?php

namespace App\Http\Middleware;

use App\Directory;
use Closure;

class HasOnlineDevice
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
        if ($request->directory->state !== Directory::STATE_DOWNLOADED && !$request->folder->hasOnlineDevice()) {
            return response()->json(['success' => false, 'error' => 'There are no active instances']);
        }

        return $next($request);
    }
}
