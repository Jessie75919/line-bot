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

Route::post('/webhook', 'LineController@index');
Route::post('/pushMessage', 'PushMessageController@index')->name('pushConsole');

//Auth::routes();

Route::get('/pushConsole', 'HomeController@index');



