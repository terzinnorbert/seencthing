<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class FolderRejected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    const NAME = 'FolderRejected';
    private $event;

    /**
     * Create a new event instance.
     *
     * @param array $event
     */
    public function __construct(array $event)
    {
        $this->event = $event;
    }

    /**
     * @return string
     */
    public function getDevice()
    {
        return $this->event['data']['device'];
    }

    /**
     * @return string
     */
    public function getFolder()
    {
        return $this->event['data']['folder'];
    }

    /**
     * @return string
     */
    public function getFolderLabel()
    {
        return $this->event['data']['folderLabel'];
    }
}
