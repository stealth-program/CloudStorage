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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::resource('files', 'FileController')->only([
    'store', 'index',
]);
Route::post('files/delete', 'FileController@destroy')->name('files.destroy');
Route::post('files/download', 'FileController@download')->name('files.download');
Route::post('files/rename', 'FileController@rename')->name('files.rename');
Route::post('files/storerenamed', 'FileController@storeRenamed')->name('files.storerenamed');

Route::resource('folders', 'FolderController')->only([
    'store', 'index'
]);
Route::post('folders/delete', 'FolderController@destroy')->name('folders.destroy');
Route::post('folders/rename', 'FolderController@rename')->name('folders.rename');
Route::post('folders/storerenamed', 'FolderController@storeRenamed')->name('folders.storerenamed');

Route::get('/home', 'HomeController@index')->name('home');

Route::get("home/{any}", 'HomeController@path')->where("any", ".*");
