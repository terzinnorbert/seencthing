<?php

namespace App\Client;

use SyncthingRest\Client;

class Rest extends Client
{
    private $cache = [];

    public function __construct()
    {
        parent::__construct(env('SYNCTHING_HOST'), env('SYNCTHING_API_KEY'));
    }

    /**
     * @param string $folder
     * @return array
     */
    public function getIgnores($folder)
    {
        $response = $this->getDbIgnores($folder);
        if (empty($response['ignore'])) {
            return [];
        }

        return $response['ignore'];
    }

    /**
     * @return array
     */
    public function getConnections()
    {
        $connections = $this->getSystemConnections();

        $status = $this->getSystemStatus();

        if (array_key_exists($status['myID'], $connections['connections'])) {
            unset($connections['connections'][$status['myID']]);
        }

        return $connections['connections'];
    }

    /**
     * @return array|mixed
     */
    public function getSystemConfig()
    {
        if (array_key_exists('getSystemConfig', $this->cache)) {
            return $this->cache['getSystemConfig'];
        }

        return $this->cache['getSystemConfig'] = parent::getSystemConfig();
    }

    /**
     * @param array $config
     * @return array
     */
    public function postSystemConfig(array $config)
    {
        foreach ($config['folders'] as &$folder) {
            if (array_key_exists('versioning', $folder) && empty($folder['versioning']['type'])) {
                unset($folder['versioning']);
            }
        }

        return parent::postSystemConfig($config);
    }

    /**
     * @param string $deviceId
     * @return array
     */
    public function addNewDevice($deviceId)
    {
        $config = $this->getSystemConfig();
        $config['devices'][] = [
            "deviceID"      => $deviceId,
            "_addressesStr" => "dynamic",
            "compression"   => "metadata",
            "introducer"    => true,
            "addresses"     => [
                "dynamic",
            ],
        ];

        $this->postSystemConfig($config);

        return $this->getSystemConfigInsync();
    }

    /**
     * @return array
     */
    public function getDevices()
    {
        $config = $this->getSystemConfig();

        $devices = [];
        foreach ($config['devices'] as $device) {
            $devices[$device['deviceID']] = $device;
        }

        return $devices;
    }

    /**
     * @return array
     */
    public function getFolders()
    {
        $config = $this->getSystemConfig();

        $folders = [];
        foreach ($config['folders'] as $folder) {
            $folders[$folder['id']] = $folder;
        }

        return $folders;
    }
}