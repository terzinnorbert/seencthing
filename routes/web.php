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

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/folders', 'FolderController@index')->name('folders');
Route::get('/folders/{folder}', 'DirectoryController@listing')->name('files');