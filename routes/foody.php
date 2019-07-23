<?php


Route::get('/', 'FoodyMainController@index');


Route::group(['prefix' => 'api/'], function () {
    Route::post('shops', 'FoodyMainController@shops');
});

