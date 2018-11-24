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


Route::post('user/send_reset_pw_email', 'Api\ApiUserController@sendResetPasswordEmail');

Route::group(['middleware' => ['sanitize']], function () {
    /* Product API */
	Route::resource('merchandise/product/', 'Api\ApiProductController');
	Route::post('merchandise/product/update_order', 'Api\ApiProductController@updateOrder');
	Route::post('merchandise/product/multi_delete', 'Api\ApiProductController@multiDelete');

	/* Type API */
	Route::post('merchandise/productType/update_order', 'Api\ApiProductTypeController@updateOrder');
	Route::post('merchandise/productType/multi_delete', 'Api\ApiProductTypeController@multiDelete');


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


	/* Common Action */
    Route::post('action/status_switch', 'Api\ApiCommonActionController@statusSwitch');
    Route::post('action/update_order', 'Api\ApiCommonActionController@updateOrder');


    /* Body Temperature */
    Route::post('body_temperature/update', 'Api\ApiBodyTemperatureController@update');


});
//Route::resource('tags', 'Api\TagsController', ['only' => ['index', 'show']]);

Route::get('merchandise/{id}/tags', 'Api\TagsController@index');
