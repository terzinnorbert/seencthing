<?php

namespace App;

use App\Directory\Preview;
use Carbon\Carbon;
use App\Client\Rest;
use Illuminate\Database\Eloquent\Model;

class Directory extends Model
{
    const PATH_SYNCTHING = 'share';

    const FOLDER_DEPTH = 5;
    const TYPE_FOLDER = 0;
    const TYPE_FILE = 1;

    const STATE_AVAILABLE = 0;
    const STATE_DOWNLOAD_IN_PROGRESS = 1;
    const STATE_DOWNLOADED = 2;

    protected $fillable = [
        'folder_id',
        'path',
        'name',
        'preview',
        'type',
        'sync_time',
        'modification_time',
        'size',
        'state',
        'expiration_time',
        'hash',
    ];

    /**
     * @param integer $folderId
     * @param array $structure
     * @param string $path
     */
    public static function syncFilesAndFoldersFromResponse($folderId, array $structure, $path = '/')
    {
        $files = $folders = [];
        foreach ($structure as $name => $data) {
            if (array_key_exists(0, $data)) {
                $files[] = $name;
            } else {
                $folders[] = $name;
            }
        }

        if (!empty($folders)) {
            foreach ($folders as $folder) {
                $directory = self::updateOrCreate(
                    [
                        'name'      => $folder,
                        'path'      => $path,
                        'folder_id' => $folderId,
                        'type'      => self::TYPE_FOLDER,
                    ],
                    [
                        'modification_time' => Carbon::now(),
                        'sync_time'         => Carbon::now(),
                        'size'              => 0,
                    ]
                );
                self::syncFilesAndFoldersFromResponse(
                    $folderId,
                    $structure[$folder],
                    self::generatePath($path, $folder)
                );
            }
        }
        if (!empty($files)) {
            foreach ($files as $file) {
                self::updateOrCreate(
                    [
                        'name'      => $file,
                        'path'      => $path,
                        'folder_id' => $folderId,
                        'type'      => self::TYPE_FILE,
                    ],
                    [
                        'modification_time' => Rest::convertTime($structure[$file][0]),
                        'sync_time'         => Carbon::now(),
                        'size'              => $structure[$file][1],
                    ]
                );
            }
        }
    }

    /**
     * @param string $basePath
     * @param string $folder
     * @return string
     */
    public static function generatePath($basePath, $folder)
    {
        return rtrim($basePath, '/').'/'.$folder;
    }

    /**
     * @param string $path
     * @return string
     */
    public static function generateParentPath($path = '/')
    {
        if (is_null($path) || '/' == $path) {
            return '/';
        }

        $path = explode('/', $path);
        if (2 == count($path)) {
            return self::generateParentPath();
        }

        return implode('/', array_slice($path, 0, -1));
    }

    /**
     * @param string $currentPath
     * @return array
     */
    public static function generateBreadcrumbItems($currentPath = '/')
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
                'name'   => $index ? $item : 'Home',
                'path'   => $pathUrl.$currentPath,
                'active' => $isLast === $index,
            ];
        }

        return $breadcrumbs;
    }

    /**
     * @return Folder|\Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function folder()
    {
        return $this->belongsTo('App\Folder');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeFolders($query)
    {
        return $query->where('type', self::TYPE_FOLDER);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeFiles($query)
    {
        return $query->where('type', self::TYPE_FILE);
    }

    /**
     * @param $query
     * @param $path
     * @return mixed
     */
    public function scopePath($query, $path)
    {
        return $query->where('path', $path);
    }

    /**
     * @return bool
     */
    public function isFile()
    {
        return self::TYPE_FILE === $this->type;
    }

    /**
     * @return bool
     */
    public function isFolder()
    {
        return !$this->isFile();
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return self::generatePath($this->path, $this->name);
    }

    /**
     * @return mixed
     */
    public function markToDownload()
    {
        return $this->folder->includeFile($this);
    }

    /**
     * @return mixed
     */
    public function markToExclude()
    {
        return $this->folder->excludeFiles(collect([$this]));
    }

    /**
     * @return bool
     */
    public function isDownloadable()
    {
        $response = app(Rest::class)->getDbFile($this->folder->name, trim($this->getPath(), '/'));

        return false === $response['local']['invalid'] && 100 == $this->progress();
    }

    /**
     * @return float|int
     */
    public function progress()
    {
        $localSize = 0;
        if (file_exists($this->getStoragePath())) {
            $localSize = filesize($this->getStoragePath());
        }

        return number_format($localSize / (int)$this->size, 0) * 100;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download()
    {
        while (!$this->isDownloadable()) {
            sleep(2);
        }

        $this->state = Directory::STATE_DOWNLOADED;
        $this->expiration_time = Carbon::now()->addMinutes(15);
        $this->save();

        return $this->getFile();
    }

    /**'
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function getFile()
    {
        return response()->download($this->getStoragePath());
    }

    /**
     * @param int $minutes
     */
    public function setExpiration($minutes = 15)
    {
        $this->expiration_time = Carbon::now()->addMinutes($minutes);
        $this->save();
    }

    /**
     * @return string
     */
    public function getStoragePath()
    {
        return $this->getStorageBasePath().$this->getPath();
    }

    /**
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getShareUrl()
    {
        if (empty($this->hash)) {
            $this->generateHash();
        }

        return url('share/'.$this->hash);
    }

    public function deleteEmptyParentFolders()
    {
        $baseDirectory = $this->getStorageBasePath();
        $parentDirectory = dirname($this->getStoragePath());
        while ($parentDirectory != $baseDirectory) {
            if (!$this->isEmptyLocalFolder($parentDirectory)) {
                return;
            }
            rmdir($parentDirectory);
            $parentDirectory = dirname($parentDirectory);
        }
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeHasNoPreview($query)
    {
        return $query->whereNull('preview')->where(
            function ($query) {
                foreach (Preview::getSupportedExtensions() as $extension) {
                    $query->orWhere('name', 'like', '%.'.$extension);
                }
            }
        );
    }

    /**
     * @return bool
     */
    public function hasPreview()
    {
        return !empty($this->preview);
    }

    /**
     * @return string
     */
    public function getPreviewUrl()
    {
        return '/'.implode(
                '/',
                [
                    'folders',
                    $this->folder_id,
                    'directory',
                    $this->id,
                    'preview',
                ]
            );
    }

    /**
     * @return bool
     */
    public function isPreviewable()
    {
        return ($this->isFile() && Preview::isSupported($this->name) && !$this->hasPreview());
    }

    /**
     * @return bool
     */
    public function createPreview()
    {
        $this->markToDownload();

        $sleep = 0;
        do {
            sleep(2);
            if (++$sleep > 30) {
                return false;
            }
        } while (!$this->isDownloadable());
        $this->preview = Preview::create($this->getStoragePath());
        $this->save();


        $this->markToExclude();
    }

    protected function generateHash()
    {
        do {
            $hash = str_random(64);
        } while (Directory::where('hash', $hash)->get()->count());

        $this->hash = $hash;
        $this->save();
    }

    /**
     * @param $path
     * @return bool
     */
    protected function isEmptyLocalFolder($path)
    {
        if (!is_dir($path) || 2 < count(scandir($path))) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    protected function getStorageBasePath()
    {
        return storage_path(self::PATH_SYNCTHING).'/'.$this->folder->name;
    }
}
