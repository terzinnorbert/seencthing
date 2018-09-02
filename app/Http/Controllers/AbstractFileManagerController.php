<?php

namespace App\Http\Controllers;

use App\Directory;
use App\Folder;
use App\Share;

class AbstractFileManagerController extends Controller
{
    const DIR_ASC = 'ASC';
    const DIR_DESC = 'DESC';

    const ORDER_NAME = 'name';
    const ORDER_MODIFICATION = 'modification_time';
    const ORDER_SIZE = 'size';

    const VIEW_FOLDER = 'directory.index';

    protected $shareable = false;
    protected $availableOrders = [
        self::ORDER_NAME,
        self::ORDER_MODIFICATION,
        self::ORDER_SIZE,
    ];

    /**
     * @param string $currentPath
     * @return array
     */
    public function generateBreadcrumbItems($currentPath = '/')
    {
        $breadcrumbs = [];
        $pathUrl = url()->current().'?path=';
        if ('/' == $currentPath) {
            $pieces = [''];
        } else {
            $pieces = explode('/', $currentPath);
        }
        $isLast = count($pieces) - 1;
        $currentPath = '';

        foreach ($pieces as $index => $item) {

            if ('/' == $currentPath) {
                $currentPath = '';
            }

            $currentPath .= '/'.$item;
            $breadcrumbs[] = [
                'name'   => $index ? $item : $this->getBreadcrumbHomeTitle(),
                'path'   => $pathUrl.$currentPath,
                'active' => $isLast === $index,
            ];
        }

        return $breadcrumbs;
    }

    /**
     * @param string $path
     * @return string
     */
    public function generateParentPath($path = '/')
    {
        if (is_null($path) || '/' == $path) {
            return '/';
        }

        $path = explode('/', $path);
        if (2 == count($path)) {
            return $this->generateParentPath();
        }

        return implode('/', array_slice($path, 0, -1));
    }

    /**
     * @return bool
     */
    public function isShareable()
    {
        return $this->shareable;
    }

    /**
     * @param Directory $directory
     * @return \Illuminate\Http\JsonResponse
     */
    public function markToDownload(Directory $directory)
    {
        $directory->markToDownload();

        return $this->success();
    }

    /**
     * @param Directory $directory
     * @return \Illuminate\Http\JsonResponse
     */
    public function isDownloadable(Directory $directory)
    {
        return $this->success(
            [
                'downloadable' => $directory->isDownloadable(),
                'progress'     => $directory->progress(),
            ]
        );
    }

    /**
     * @param Directory $directory
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(Directory $directory)
    {
        return $directory->download();
    }

    /**
     * @param Directory $directory
     * @return string
     */
    public function getDirectoryPath(Directory $directory)
    {
        return $directory->getPath();
    }

    /**
     * @return array|bool
     */
    protected function getCustomOrder()
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
    protected function getValidatedOrder($order, $direction)
    {
        if (!in_array($order, $this->availableOrders)) {
            return false;
        }
        $direction = in_array($direction, [self::DIR_ASC, self::DIR_DESC]) ? $direction : self::DIR_ASC;

        return [$order, $direction];
    }

    /**
     * @param Folder $folder
     * @param Share|null $share
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function folderListing(Folder $folder, Share $share = null)
    {
        $order = $this->getOrder();

        return view(
            static::VIEW_FOLDER,
            [
                'handler'         => $this,
                'folder'          => $folder,
                'order'           => $order,
                'foldersAndFiles' => $this->getFoldersAndFiles($folder, $this->getPath($share), $order),
            ]
        );
    }

    /**
     * @param Share|null $share
     * @return array|null|string
     */
    protected function getPath(Share $share = null)
    {
        $path = request()->input('path', '/');
        if (empty($path)) {
            request()->merge(['path' => $path = '/']);
        }

        if ($share) {
            return $this->getNormalizedPath($share->directory, $path);
        }

        return $path;
    }

    /**
     * @return array|bool
     */
    protected function getOrder()
    {
        $order = $this->getCustomOrder();
        if (!$order) {
            $order = [self::ORDER_NAME, self::DIR_ASC];
        }

        return $order;
    }

    /**
     * @param Folder $folder
     * @param $path
     * @param $order
     * @return mixed
     */
    protected function getFoldersAndFiles(Folder $folder, $path, $order)
    {
        return $folder->directory()->with('shares')->path($path)->orderBy('type')
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
            ->get();
    }

    /**
     * @return string
     */
    protected function getBreadcrumbHomeTitle()
    {
        return __('Home');
    }
}