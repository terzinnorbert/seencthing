<?php

namespace App\Http\Controllers;

use App\Client\Rest;
use App\Folder;

class FolderController extends Controller
{
    /**
     * @var Rest
     */
    private $client;

    public function __construct()
    {
        $this->client = app(Rest::class);
    }

    public function index()
    {
        return view('folders', ['folders' => Folder::orderBy('name')->get()]);
    }
}