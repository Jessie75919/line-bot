<?php

$routeName = "/productsConsole";

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
    Route::get("/productsConsole", "ProductConsoleController@index")->name("productsConsole.index");
    Route::get("/productsConsole/search", "ProductConsoleController@search");
    Route::get("/productsConsole/create", "ProductConsoleController@create")->name('productsConsole.create');
    Route::get("/productsConsole/{product}", "ProductConsoleController@show");
    Route::get("/productsConsole/{product}/edit", "ProductConsoleController@edit");
    Route::post("/productsConsole/storeImages", "ProductConsoleController@storeImages")->name('productsConsole.storeImages');
    Route::put("/productsConsole/{product}", "ProductConsoleController@update");
    Route::delete("/productsConsole/{product}", "ProductConsoleController@destroy")->name("productsConsole.destroy");
    Route::get("/productsConsole/{product}/clone", "ProductConsoleController@clone");
});

Route::post("/productsConsole", "ProductConsoleController@store")->name('productsConsole.store')->middleware('auth');;


Route::get("/login", function () {
    return view("auth.login");
})->name("login");



