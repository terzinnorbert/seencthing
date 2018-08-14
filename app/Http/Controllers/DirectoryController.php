<?php

namespace App\Http\Controllers;

use App\Client\Rest;
use App\Directory;
use App\Folder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DirectoryController extends Controller
{
    const DIR_ASC = 'ASC';
    const DIR_DESC = 'DESC';

    const ORDER_NAME = 'name';
    const ORDER_MODIFICATION = 'modification_time';
    const ORDER_SIZE = 'size';

    private $availableOrders = [
        self::ORDER_NAME,
        self::ORDER_MODIFICATION,
        self::ORDER_SIZE,
    ];
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
        $order = $this->getCustomOrder();
        if (!$order) {
            $order = [self::ORDER_NAME, self::DIR_ASC];
        }

        return view(
            'directory.index',
            [
                'folder'          => $folder,
                'order'           => $order,
                'foldersAndFiles' => $folder->directory()->path($path)->orderBy('type')
                    ->when(
                        $order,
                        function ($query, $order) {
                            $query = $query->orderBy($order[0], $order[1]);
                            // secondary order by name
                            if (self::ORDER_NAME != $order[0]) {
                                return $query->orderBy(self::ORDER_NAME);
                            }
                        }
                    )
                    ->get(),
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

    /**
     * @return array|bool
     */
    private function getCustomOrder()
    {
        if (!($order = request()->cookie('order', false))) {
            return false;
        }
        list($order, $direction) = explode('|', $order);

        return $this->getValidatedOrder($order, $direction);
    }

    /**
     * @param $order
     * @param $direction
     * @return array|bool
     */
    private function getValidatedOrder($order, $direction)
    {
        if (!in_array($order, $this->availableOrders)) {
            return false;
        }
        $direction = in_array($direction, [self::DIR_ASC, self::DIR_DESC]) ? $direction : self::DIR_ASC;

        return [$order, $direction];
    }
}