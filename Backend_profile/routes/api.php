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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => 'cors'], function () {
    // Routing for frontend user
    Route::group(['prefix' => 'profile'], function () {
        Route::post('uploadProfileImage', 'API\profileController@uploadProfileImage');
        Route::post('updatePersonalProfile', 'API\profileController@updatePersonalProfile');
        Route::get('getPersonalProfile', 'API\profileController@getPersonalProfile');
        Route::get('getGuzzleRequest', 'API\profileController@getGuzzleRequest');
    });
        
});