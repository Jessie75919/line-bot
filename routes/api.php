<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware('auth:api')->get('/user', function (Request $request) {
	return $request->user();
});

Route::group(['middleware' => ['sanitize']], function () {
    /* Product API */
	Route::resource('products', 'Api\ProductController');
	Route::get('products/status_switch/{product}', 'Api\ProductController@statusSwitch');
	Route::post('products/update_order', 'Api\ProductController@updateOrder');
	Route::post('products/multi_delete', 'Api\ProductController@multiDelete');


	/* ProductImage */
    Route::delete('productImage/{productImage}', 'Api\ProductImageController@delete');
    Route::post("/productImage/storeImages", "Api\ProductImageController@storeImages")->name('productsImage.storeImages');

    /* Tag API */
	Route::get('tag/shop/{shop}', 'Api\TagsController@index');
	Route::get('tag/product/{product}', 'Api\TagsController@getProductTags');
	Route::post('tag/{shop}/{product}', 'Api\TagsController@store');
	Route::delete('tag/product/{product}', 'Api\TagsController@detachTag');
	Route::delete('tag/{tag}', 'Api\TagsController@destroy');
	Route::put('tag/{tag}', 'Api\TagsController@update');
});
//Route::resource('tags', 'Api\TagsController', ['only' => ['index', 'show']]);

Route::get('products/{id}/tags', 'Api\TagsController@index');
