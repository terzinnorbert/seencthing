<?php

namespace App\Http\Controllers;

use App\Client\Rest;
use App\Folder;

class DirectoryController extends Controller
{
    /**
     * @var Rest
     */
    private $client;

    public function __construct()
    {
        $this->client = app(Rest::class);
    }

    public function listing(Folder $folder)
    {
        return view(
            'directory.listing',
            ['foldersAndFiles' => $folder->directory()->parent(0)->orderBy('type')->get()]
        );
    }
}