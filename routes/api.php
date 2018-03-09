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

Route::get('/delete-all-things', 'TestController@delete')->middleware('auth.jwt');
Route::get('/itest', 'TestController@index')->middleware('auth.jwt');
Route::group(['namespace' => 'v1', 'prefix' => 'v1'], function () use ($router) {
    Route::post('/register', 'AuthController@register');
    Route::post('/login', 'AuthController@login');
    Route::post('/logout', 'AuthController@logout');
    Route::put('/refresh', 'AuthController@refresh');


    Route::group(['prefix' => 'authorization', 'middleware' => ['auth.jwt']], function () {
        Route::get('/permissions', 'PermissionController@permissionsList');
        Route::get('/roles', 'PermissionController@rolesList');
    });

    Route::group(['prefix' => 'user', 'middleware' => ['auth.jwt']], function () {
        Route::patch('/update', 'UserController@update');
        Route::get('/', 'UserController@profile');
        Route::post('/upload', 'UserController@upload');
    });

    Route::group(['prefix' => 'project', 'middleware' => ['auth.jwt']], function () {
        Route::get('/', 'ProjectController@all');
        Route::post('/', 'ProjectController@create');
        Route::patch('/{project}', 'ProjectController@update');
        Route::delete('/{project}', 'ProjectController@stop');
        Route::get('/{project}', 'ProjectController@get');
        Route::get('/{project}/things', 'ProjectController@things');

        Route::post('/{project}/scenario', 'ScenarioController@create');
        Route::get('/{project}/scenario', 'ScenarioController@list');
        Route::get('/{project}/scenario/{scenario}', 'ScenarioController@get');
        Route::get('/{project}/scenario/{scenario}/activate', 'ScenarioController@activate');

        Route::group(['prefix' => '/{project}/things', 'middleware' => ['auth.jwt']], function () {
            Route::get('/', 'ThingController@all');
            Route::post('/', 'ThingController@create');
            Route::post('/from-excel', 'ThingController@fromExcel');
            Route::get('/{thing}', 'ThingController@get');
            Route::get('/{thing}/data', 'ThingController@data');
            Route::patch('/{thing}', 'ThingController@update');
            Route::delete('/{thing}', 'ThingController@delete');
            Route::post('/{thing}/activate', 'ThingController@activate');
            Route::post('/{thing}/send', 'DownLinkController@sendThing');

            Route::post('/{thing}/codec', 'CodecController@create');
            Route::get('/{thing}/codec', 'CodecController@get');
            Route::patch('/{thing}/codec', 'CodecController@update');
        });
    });


    Route::group(['prefix' => 'thing-profile', 'middleware' => ['auth.jwt']], function () {
        Route::get('/', 'ThingProfileController@all');
        Route::post('/', 'ThingProfileController@create');
        Route::delete('/{thing_profile}', 'ThingProfileController@delete');

    });

    Route::group(['prefix' => 'gateway', 'middleware' => ['auth.jwt']], function () {
        Route::post('/', 'GatewayController@create');
        Route::get('/', 'GatewayController@list');
        Route::delete('/{gateway}', 'GatewayController@delete');
    });

    Route::group(['prefix' => 'payment', 'middleware' => ['auth.jwt']], function () {
        Route::get('/user/new', 'PaymentController@setNewUser');
        Route::post('/user/packages', 'PaymentController@getUserPackages');
        Route::post('/user/package/status', 'PaymentController@updatePackageStatus');
        Route::get('/user/package/status', 'PaymentController@getLastPackageStatus');
        Route::post('/user/package/buy', 'PaymentController@paymentRequest');
        Route::post('/user/packages/status', 'PaymentController@getUserPackagesByStatus');

        Route::get('/user/transactions', 'PaymentController@getUserTransactions');

        Route::get('/packages', 'PaymentController@getPackages');
        Route::post('/package/update', 'PaymentController@updatePackage');
        Route::post('/package/delete', 'PaymentController@deletePackage');
    });
});


