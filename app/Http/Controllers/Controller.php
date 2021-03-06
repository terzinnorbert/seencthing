<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success(array $data = [])
    {
        return response()->json(['success' => true] + $data);
    }

    /**
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error(array $data = [])
    {
        return response()->json(['success' => false] + $data);
    }
}
