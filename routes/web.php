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
    Route::get("/product/content", "ProductConsoleController@index")->name("product.content.index");
    Route::get("/product/content/search", "ProductConsoleController@search");
    Route::get("/product/content/create", "ProductConsoleController@create")->name('product.content.create');
    Route::get("/product/content/{product}", "ProductConsoleController@show");
    Route::get("/product/content/{product}/edit", "ProductConsoleController@edit");
    Route::put("/product/content/{product}", "ProductConsoleController@update");
    Route::delete("/product/content/{product}", "ProductConsoleController@destroy")->name("product.content.destroy");
    Route::get("/product/content/{product}/clone", "ProductConsoleController@clone");

    /* 商品管理 / 類別管理 */
    Route::get("/product/category", "ProductCategoryController@index")->name("product.category.index");
});


Route::post("/product/content", "ProductConsoleController@store")->name('product.content.store')->middleware('auth');
Route::put("/product/content/{product}", "ProductConsoleController@update")->name('product.content.update')->middleware('auth');


Route::get("/login", function () {
    return view("auth.login");
})->name("login");



