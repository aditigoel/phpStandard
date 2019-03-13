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
    Route::group(['prefix' => 'skills'], function () {
        Route::post('addUserSkills', 'API\userDataController@addUserSkills');
    });

    Route::group(['prefix' => 'search'], function () {
        Route::get('searchProviders', 'API\searchController@searchProviders');
      
    });

    Route::group(['prefix' => 'jobs'], function () {
        Route::post('applyJob', 'API\jobsController@applyJob');
        Route::post('acceptInvitation', 'API\jobsController@acceptInvitation');
    });
        
});