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
	Route::resource('products/content/', 'Api\ApiProductController');
	Route::get('products/content/status_switch/{product}', 'Api\ApiProductController@statusSwitch');
	Route::post('products/content/update_order', 'Api\ApiProductController@updateOrder');
	Route::post('products/content/multi_delete', 'Api\ApiProductController@multiDelete');

	/* Type API */
	Route::get('products/type/status_switch/{productType}', 'Api\ApiProductTypeController@statusSwitch');
	Route::post('products/type/update_order', 'Api\ApiProductTypeController@updateOrder');
	Route::post('products/type/multi_delete', 'Api\ApiProductTypeController@multiDelete');


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
