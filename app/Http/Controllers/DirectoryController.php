<?php

namespace App\Http\Controllers;

use App\Client\Rest;
use App\Folder;
use Illuminate\Http\Request;

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

    public function listing(Request $request, Folder $folder)
    {
        $path = $request->input('path', '/');
        if (empty($path)) {
            $request->merge(['path' => $path = '/']);
        }

        return view(
            'directory.listing',
            ['foldersAndFiles' => $folder->directory()->path($path)->orderBy('type')->get()]
        );
    }
}