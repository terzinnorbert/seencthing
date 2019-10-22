<?php

namespace App\Http\Middleware;

use App\Directory;
use App\Folder;
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
        if (
            $request->directory->state !== Directory::STATE_DOWNLOADED &&
            !$this->getFolder($request)->hasOnlineDevice()
        ) {
            return response()->json(['success' => false, 'error' => 'There are no active instances']);
        }

        return $next($request);
    }

    /**
     * @param $request
     * @return Folder
     */
    protected function getFolder($request)
    {
        if (!empty($request->share)) {
            return $request->share->directory->folder;
        }

        return $request->folder;
    }
}
