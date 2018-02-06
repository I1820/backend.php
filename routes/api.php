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

Route::group(['namespace' => 'v1', 'prefix' => 'v1'], function () use ($router) {
    Route::post('/register', 'AuthController@register');
    Route::post('/login', 'AuthController@login');
    Route::post('/logout', 'AuthController@logout');
    Route::put('/refresh', 'AuthController@refresh');

    Route::group(['prefix' => 'user', 'middleware' => ['auth.jwt']], function () {
        Route::patch('/update', 'UserController@update');
        Route::get('/', 'UserController@profile');
        Route::post('/upload', 'UserController@upload');
    });

    Route::group(['prefix' => 'project', 'middleware' => ['auth.jwt']], function () {
        Route::get('/', 'ProjectController@all');
        Route::post('/', 'ProjectController@create');
        Route::patch('/{project}', 'ProjectController@update');
        Route::get('/{project}', 'ProjectController@get');
        Route::get('/{project}/things', 'ProjectController@things');
        Route::get('/{project}/things/{thing}', 'ProjectController@addThing');

        Route::post('/{project}/codec', 'CodecController@create');
        Route::get('/{project}/codec', 'CodecController@get');
        Route::patch('/{project}/codec', 'CodecController@update');
    });
    Route::group(['prefix' => 'thing', 'middleware' => ['auth.jwt']], function () {
        Route::get('/', 'ThingController@all');
        Route::post('/', 'ThingController@create');
        Route::get('/{thing}', 'ThingController@get');
        Route::get('/{thing}/data', 'ThingController@data');
        Route::patch('/{thing}', 'ThingController@update');
    });

    Route::group(['prefix' => 'payment', 'middleware' => ['auth.jwt']], function () {
        Route::get('/user/new', 'PaymentController@setNewUser');
        Route::post('/user/packages', 'PaymentController@getUserPackages');
        Route::post('/user/package/status', 'PaymentController@updatePackageStatus');
        Route::get('/user/package/status', 'PaymentController@getLastPackageStatus');
        Route::post('/user/package/request', 'PaymentController@paymentRequest');
        Route::get('/user/package/verification/{userId}/{merchantId}/{authority}/{amount}/{packageType}', 'PaymentController@paymentVerification');
        Route::post('/user/packages/status', 'PaymentController@getUserPackagesByStatus');

        Route::post('/user/transactions', 'PaymentController@getUserTransactions');

        Route::post('/transactions', 'PaymentController@getTransactions');

        Route::get('/packages', 'PaymentController@getPackages');
        Route::post('/package/update', 'PaymentController@updatePackage');
        Route::post('/package/create', 'PaymentController@createPackage');
        Route::post('/package/delete', 'PaymentController@deletePackage');
    });
});


