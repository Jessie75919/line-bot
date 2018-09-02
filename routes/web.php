<?php

$routeName = "/product/content";

//Route::get("/", function () {
//    return view("consoles.products.index");
//});
Route::get("/test", function () {
    return view("consoles.products.test");
})->name("test");

Route::post("/webhook", "LineController@index");
//Route::post("/pushMessage", "PushMessageController@index")->name("pushConsole");

Auth::routes();

//Route::get("/pushConsole", "HomeController@index");

Route::group(["middleware" => ["sanitize", "auth"]], function () {

    /* 商品管理 / 內容管理 */
    Route::get("/product/content", "ProductConsoleController@index")
         ->name("product.content.index");
    Route::get("/product/content/search", "ProductConsoleController@search");
    Route::get("/product/content/create", "ProductConsoleController@create")
         ->name('product.content.create');
    Route::get("/product/content/{product}", "ProductConsoleController@show");
    Route::get("/product/content/{product}/edit", "ProductConsoleController@edit");
    Route::put("/product/content/{product}", "ProductConsoleController@update");
    Route::delete("/product/content/{product}", "ProductConsoleController@destroy")
         ->name("product.content.destroy");
    Route::get("/product/content/{product}/clone", "ProductConsoleController@clone");

    /* 商品管理 / 類別管理 */
    Route::get("/product/type", "ProductTypeController@index")
         ->name("product.type.index");
    Route::delete("/product/type/{productType}", "ProductTypeController@destroy")
         ->name("product.type.destroy");
    Route::get("/product/type/{productType}/edit", "ProductTypeController@edit");
    Route::put("/product/type/{productType}", "ProductTypeController@update");

    /* 貼心小提醒 */
    Route::get("/product/notices", "ProductNoticeController@index")
         ->name("product.notices.index");

});


/* No Sanitize */ 
Route::group(["middleware" => ["auth"]], function () {
    Route::post("/product/content", "ProductConsoleController@store")
         ->name('product.content.store')
         ->middleware('auth');
    Route::put("/product/content/{product}", "ProductConsoleController@update")
         ->name('product.content.update')
         ->middleware('auth');
    Route::post("/product/notices", "ProductNoticeController@create")
         ->name("product.notices.create");
});





/* Reset Password */

Route::post("/send_reset_pwd_mail", "Auth\ResetPasswordController@sendResetPasswordEmail");
Route::get("/update_pwd", "Auth\ResetPasswordController@UpdatePassword");
Route::post("/reset_pwd", "Auth\ResetPasswordController@ResetPassword");

Route::get("/logout", "Auth\LoginController@logout")->name("logOut");



