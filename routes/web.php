<?php

$routeName = "/product/product";

// ------------------------------------------------------------------------------
//  Line API
// ------------------------------------------------------------------------------
Route::group(['prefix' => 'line'], function () {
    /* Line Message Api webhook */
    Route::post("webhook", "Line\LineController@index");

    /* Line Liff pages */
    Route::group(['prefix' => 'liff'], function () {
        Route::group(['prefix' => 'weight'], function () {
            Route::get("index", 'Line\LiffWeightController@index');
            Route::get("my-setting/{userId}", 'Line\LiffWeightController@mySetting');
        });
    });
});

Route::get(
    "/place-api/image-preview",
    "LineController@imagePreview"
)->name('place-api.image-preview');
//Route::post("/pushMessage", "PushMessageController@index")->name("pushConsole");

//Route::post("/webhook", "FacebookBotController@post");
//Route::get("/webhook", "FacebookBotController@get");

Auth::routes();

Route::get('/', function () {
    return view('auth.login');
});

Route::group(["middleware" => ["sanitize", "auth"]], function () {

    Route::get('/body_temperature', "BodyTemperatureController@index");

    /* 商品管理 / 內容管理 */
    Route::get("/merchandise/product", "ProductConsoleController@index")
        ->name("merchandise.product.index");
    Route::get("/merchandise/product/search", "ProductConsoleController@search");
    Route::get("/merchandise/product/create", "ProductConsoleController@create")
        ->name('merchandise.product.create');
    Route::get("/merchandise/product/{product}", "ProductConsoleController@show");
    Route::get("/merchandise/product/{product}/edit", "ProductConsoleController@edit");
    Route::put("/merchandise/product/{product}", "ProductConsoleController@update");
    Route::delete("/merchandise/product/{product}", "ProductConsoleController@destroy")
        ->name("merchandise.product.destroy");
    Route::get("/merchandise/product/{product}/clone", "ProductConsoleController@clone");

    /* 商品管理 / 類別管理 */
    Route::get("/merchandise/productType", "ProductTypeController@index")
        ->name("merchandise.productType.index");
    Route::delete("/merchandise/productType/{productType}", "ProductTypeController@destroy")
        ->name("merchandise.productType.destroy");
    Route::get("/merchandise/productType/{productType}/edit", "ProductTypeController@edit");
    Route::put("/merchandise/productType/{productType}", "ProductTypeController@update");

    /* 貼心小提醒 */
    Route::get("/merchandise/notices", "ProductNoticeController@index")
        ->name("merchandise.notices.index");

    /* 首頁主圖管理 */
    Route::get("/homeImage", "HomeImageController@index")
        ->name("homeImage.index");
    Route::get("/homeImage/search", "HomeImageController@search")
        ->name("homeImage.search");

});

/* No Sanitize */
Route::group(["middleware" => ["auth"]], function () {
    Route::post("/merchandise/product", "ProductConsoleController@store")
        ->name('merchandise.product.store')
        ->middleware('auth');
    Route::put("/merchandise/product/{product}", "ProductConsoleController@update")
        ->name('merchandise.product.update')
        ->middleware('auth');
    Route::post("/merchandise/notices", "ProductNoticeController@create")
        ->name("merchandise.notices.create");
});

/* Reset Password */

Route::post("/send_reset_pwd_mail", "Auth\ResetPasswordController@sendResetPasswordEmail");
Route::get("/update_pwd", "Auth\ResetPasswordController@UpdatePassword");
Route::post("/reset_pwd", "Auth\ResetPasswordController@ResetPassword");

Route::get("/logout", "Auth\LoginController@logout")->name("logOut");



