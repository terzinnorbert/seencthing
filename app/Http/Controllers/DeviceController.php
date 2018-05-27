<?php

namespace App\Http\Controllers;

use App\Client\Rest;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * @var Rest
     */
    private $client;

    public function __construct()
    {
        $this->client = app(Rest::class);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view(
            'device/devices',
            [
                'devices'     => $this->client->getDevices(),
                'connections' => $this->client->getConnections(),
            ]
        );
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function add()
    {
        $deviceId = request()->input('deviceId');

        if (empty($deviceId)) {
            return $this->error(['error' => 'Device ID is empty']);
        }

        $validate = $this->client->getSvcDeviceid($deviceId);
        if (array_key_exists('id', $validate)) {
            return $this->success($this->client->addNewDevice($deviceId));
        } else {
            return $this->error(['error' => $validate['error']]);
        }
    }
}
