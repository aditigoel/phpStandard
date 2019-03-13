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



Route::group(['middleware' => 'cors'], function () {

    
    // Routing for frontend user
    Route::group(['middleware' => ['auth:api','user_data',]], function () {

        Route::group(['prefix' => 'connections'], function () {

        Route::post('sendFriendRequest', 'API\ConnectionsController@sendFriendRequest');

        Route::post('acceptFriendRequest', 'API\ConnectionsController@acceptFriendRequest');

        Route::post('rejectFriendRequest', 'API\ConnectionsController@rejectFriendRequest');

        Route::get('getFriends', 'API\ConnectionsController@getFriends');
        
        Route::get('getRecomendedFriends', 'API\ConnectionsController@getRecomendedFriends'); 

        Route::get('getFriendRequestList', 'API\ConnectionsController@getFriendRequestList');
        
        Route::post('unfriend', 'API\ConnectionsController@unfriend');
       
        Route::get('mutualFriends', 'API\ConnectionsController@mutualFriends');

       

       });

    });
});