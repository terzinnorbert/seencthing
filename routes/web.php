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

Route::get(
    '/',
    function () {
        if (!Auth::user()) {
            return redirect('/home');
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
    ['middleware' => ['auth']],
    function () {
        Route::get('/home', 'HomeController@index')->name('home');
        Route::get('/folders', 'FolderController@index')->name('folders');
        Route::get('/folders/{folder}', 'DirectoryController@listing')->name('files');
        Route::post('/folders/{folder}/directory/{directory}/download', 'DirectoryController@markToDownload');
        Route::get('/folders/{folder}/directory/{directory}/download', 'DirectoryController@download');
        Route::get('/folders/{folder}/directory/{directory}/state', 'DirectoryController@isDownloadable');
    }
);