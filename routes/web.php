<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Folder;
use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\ShareController;
use App\Http\Middleware\SyncthingIsAvailable;
use App\Share;
use App\Directory;

Route::get(
    '/',
    function () {
        if (!Auth::user()) {
            return redirect('/login');
        }

        return redirect('/folders');
    }
);

Auth::routes();
Route::get(
    '/logout',
    function () {
        Auth::logout();

        return redirect('/');
    }
);

Route::group(
    ['middleware' => ['auth', SyncthingIsAvailable::class]],
    function () {
        Route::get('/devices', 'DeviceController@index')->name('devices');
        Route::post('/devices', 'DeviceController@add');
        Route::get('/folders', 'FolderController@index')->name('folders');
        Route::prefix('/folders/{folder}')->group(
            function () {
                Route::get('/refresh', 'FolderController@refresh');
                Route::get('/', 'DirectoryController@listing')->name('files');
                Route::get('/view/{view}', 'DirectoryController@view')->name('directory.view');
                Route::get('/order/{order}/{direction}', 'DirectoryController@order')->name(
                    'directory.order'
                );
                Route::group(
                    ['middleware' => [\App\Http\Middleware\HasOnlineDevice::class]],
                    function () {
                        Route::post(
                            '/directory/{directory}/download',
                            function (Folder $folder, Directory $directory) {
                                return app(DirectoryController::class)->markToDownload($directory);
                            }
                        );
                        Route::get(
                            '/directory/{directory}/download',
                            function (Folder $folder, Directory $directory) {
                                return app(DirectoryController::class)->download($directory);
                            }
                        );
                        Route::get(
                            '/directory/{directory}/state',
                            function (Folder $folder, Directory $directory) {
                                return app(DirectoryController::class)->isDownloadable($directory);
                            }
                        );
                    }
                );
                Route::get('/directory/{directory}/share', 'DirectoryController@getShareUrl');
                Route::get('/directory/{directory}/preview', 'DirectoryController@getPreview');
            }
        );
    }
);

Route::get(
    '/share',
    function () {
        return redirect('/');
    }
);
Route::prefix('/share/{share}')->group(
    function () {
        Route::get('/', 'ShareController@index');
        Route::get('/download', 'ShareController@downloadFile');
        Route::post('/download', 'ShareController@markToDownloadFile');
        Route::get('/progress', 'ShareController@progress');
        Route::group(
            ['middleware' => [\App\Http\Middleware\HasOnlineDevice::class]],
            function () {
                Route::post(
                    '/directory/{directory}/download',
                    function (Share $share, Directory $directory) {
                        return app(ShareController::class)->markToDownload($directory);
                    }
                );
                Route::get(
                    '/directory/{directory}/download',
                    function (Share $share, Directory $directory) {
                        return app(ShareController::class)->download($directory);
                    }
                );
                Route::get(
                    '/directory/{directory}/state',
                    function (Share $share, Directory $directory) {
                        return app(ShareController::class)->isDownloadable($directory);
                    }
                );
            }
        );
    }
);