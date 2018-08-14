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

use App\Http\Middleware\SyncthingIsAvailable;

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
        Route::get('/folders/{folder}/refresh', 'FolderController@refresh');
        Route::get('/folders/{folder}', 'DirectoryController@listing')->name('files');
        Route::get('/folders/{folder}/view/{view}', 'DirectoryController@view')->name('directory.view');
        Route::get('/folders/{folder}/order/{order}/{direction}', 'DirectoryController@order')->name('directory.order');
        Route::group(
            ['middleware' => [\App\Http\Middleware\HasOnlineDevice::class]],
            function () {
                Route::post('/folders/{folder}/directory/{directory}/download', 'DirectoryController@markToDownload');
                Route::get('/folders/{folder}/directory/{directory}/download', 'DirectoryController@download');
                Route::get('/folders/{folder}/directory/{directory}/state', 'DirectoryController@isDownloadable');
            }
        );
        Route::get('/folders/{folder}/directory/{directory}/share', 'DirectoryController@getShareUrl');
        Route::get('/folders/{folder}/directory/{directory}/preview', 'DirectoryController@getPreview');
    }
);

Route::get(
    '/share',
    function () {
        return redirect('/');
    }
);
Route::get('/share/{hash}', 'ShareController@index');
Route::get('/share/{hash}/download', 'ShareController@download');
Route::post('/share/{hash}/download', 'ShareController@markToDownload');
Route::get('/share/{hash}/progress', 'ShareController@progress');