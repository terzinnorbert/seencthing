<?php

namespace App\Listeners;

use App\Client\Rest;
use App\Events\FolderRejected;
use App\Folder;

class AddNewFolder
{
    /**
     * @var Rest
     */
    private $client;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->client = app(Rest::class);
    }

    /**
     * Handle the event.
     *
     * @param  FolderRejected $event
     * @return void
     */
    public function handle(FolderRejected $event)
    {
        $folders = $this->client->getFolders();
        if (!in_array($event->getFolder(), array_keys($folders))) {
            $this->client->addFolder($event->getFolder(), $event->getFolderLabel(), $event->getDevice());
            Folder::syncFromSyncthing();
        }
    }
}
