<?php

namespace App\Http\Controllers;

use App\Client\Rest;
use App\Directory;
use App\Folder;
use Carbon\Carbon;
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

    /**
     * @param Request $request
     * @param Folder $folder
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function listing(Request $request, Folder $folder)
    {
        $path = $request->input('path', '/');
        if (empty($path)) {
            $request->merge(['path' => $path = '/']);
        }

        return view(
            'directory.index',
            [
                'folder'          => $folder,
                'foldersAndFiles' => $folder->directory()->path($path)->orderBy('type')->get(),
            ]
        );
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
     * @param Directory $directory
     * @return \Illuminate\Http\JsonResponse
     */
    public function markToDownload(Folder $folder, Directory $directory)
    {
        $directory->markToDownload();

        return $this->success();
    }

    /**
     * @param Folder $folder
     * @param Directory $directory
     * @return \Illuminate\Http\JsonResponse
     */
    public function isDownloadable(Folder $folder, Directory $directory)
    {
        return $this->success(
            [
                'downloadable' => $directory->isDownloadable(),
                'progress'     => $directory->progress(),
            ]
        );
    }

    /**
     * @param Folder $folder
     * @param Directory $directory
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(Folder $folder, Directory $directory)
    {
        return $directory->download();
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