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
Route::options('/{any?}', ['middleware' => 'cors', function(){return;}]);

Route::middleware('auth:api')->middleware('cors')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'user'], function() {
  Route::post('login', 'UserController@login');
  Route::post('travels', 'TravelController@get');
});
