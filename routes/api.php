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
    Route::get('/verify/{user}/{token}', 'AuthController@verifyEmail')->name('verify-email');


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
        Route::get('/lint', 'ProjectController@lint');
        Route::patch('/{project}', 'ProjectController@update');
        Route::delete('/{project}', 'ProjectController@stop');
        Route::get('/{project}', 'ProjectController@get');
        Route::get('/{project}/things', 'ProjectController@things');
        Route::post('/{project}/aliases', 'ProjectController@aliases');
        Route::get('/{project}/log', 'ProjectController@log');


        Route::post('/{project}/scenario', 'ScenarioController@create');
        Route::get('/{project}/scenario', 'ScenarioController@list');
        Route::get('/{project}/scenario/{scenario}', 'ScenarioController@get');
        Route::patch('/{project}/scenario/{scenario}', 'ScenarioController@update');
        Route::delete('/{project}/scenario/{scenario}', 'ScenarioController@delete');
        Route::get('/{project}/scenario/{scenario}/activate', 'ScenarioController@activate');
        Route::post('/{project}/codec', 'CodecController@create');
        Route::get('/{project}/codec', 'CodecController@list');
        Route::delete('/{project}/codec/{codec}', 'CodecController@delete');

        Route::group(['prefix' => '/{project}/things', 'middleware' => ['auth.jwt']], function () {
            Route::get('/', 'ThingController@all');
            Route::post('/', 'ThingController@create');
            Route::post('/from-excel', 'ThingController@fromExcel');
            Route::get('/{thing}', 'ThingController@get');

            Route::get('/{thing}/data', 'ThingController@data');
            Route::post('data', 'ThingController@multiThingData');

            Route::patch('/{thing}', 'ThingController@update');
            Route::delete('/{thing}', 'ThingController@delete');
            Route::post('/{thing}/activate', 'ThingController@activate');
            Route::post('/{thing}/send', 'DownLinkController@sendThing');

            Route::post('/{thing}/codec', 'CodecController@send');
            Route::get('/{thing}/codec', 'CodecController@get');
        });
    });


    Route::group(['prefix' => 'thing-profile', 'middleware' => ['auth.jwt']], function () {
        Route::get('/', 'ThingProfileController@all');
        Route::post('/', 'ThingProfileController@create');
        Route::delete('/{thing_profile}', 'ThingProfileController@delete');
        Route::get('/{thing_profile}', 'ThingProfileController@get');

    });

    Route::group(['prefix' => 'gateway', 'middleware' => ['auth.jwt']], function () {
        Route::post('/', 'GatewayController@create');
        Route::get('/', 'GatewayController@list');
        Route::delete('/{gateway}', 'GatewayController@delete');
        Route::get('/{gateway}', 'GatewayController@info');
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


Route::group(['namespace' => 'admin', 'prefix' => 'admin'], function () use ($router) {
    Route::group(['prefix' => 'users', 'middleware' => ['auth.jwt','admin']], function () {
        Route::get('/', 'UserController@list');
    });
});


