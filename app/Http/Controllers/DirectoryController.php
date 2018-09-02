<?php

namespace App\Http\Controllers;

use App\Client\Rest;
use App\Directory;
use App\Folder;

class DirectoryController extends AbstractFileManagerController
{
    protected $shareable = true;
    /**
     * @var Rest
     */
    private $client;

    public function __construct()
    {
        $this->client = app(Rest::class);
    }

    /**
     * @param Folder $folder
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function listing(Folder $folder)
    {
        return $this->folderListing($folder);
    }

    /**
     * @param Folder $folder
     * @param $view
     * @return \Illuminate\Http\RedirectResponse
     */
    public function view(Folder $folder, $view)
    {
        $view = in_array($view, ['grid', 'list']) ? $view : 'list';

        return back()->withCookie(cookie()->forever('directory_view', $view));
    }

    /**
     * @param Folder $folder
     * @param $order
     * @param $direction
     * @return \Illuminate\Http\RedirectResponse
     */
    public function order(Folder $folder, $order, $direction)
    {
        $orderAndDirection = $this->getValidatedOrder($order, $direction);

        if (!$orderAndDirection) {
            return back();
        }

        return back()->withCookie(cookie()->forever('order', implode('|', $orderAndDirection)));
    }

    /**
     * @param Folder $folder
     * @param Directory $directory
     * @return \Illuminate\Http\JsonResponse
     */
    public function getShareUrl(Folder $folder, Directory $directory)
    {
        return $this->success(['url' => $directory->getShareUrl()]);
    }

    /**
     * @param Folder $folder
     * @param Directory $directory
     * @return string|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function getPreview(Folder $folder, Directory $directory)
    {
        if ($directory->hasPreview()) {
            return response()->download($directory->preview, $directory->name);
        }

        return '';
    }
}