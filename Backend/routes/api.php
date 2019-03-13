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

     Route::group(['prefix' => 'user'], function () {
       
    Route::post('login', 'API\UsersController@login');
    Route::post('sendOtp', 'API\UsersController@sendOtp');
    Route::post('resendOtp', 'API\UsersController@resendOtp');
    Route::post('varify_otp', 'API\UsersController@varify_otp');
    Route::post('socialLogin', 'API\UsersController@socialLogin');
    Route::post('signUp', 'API\UsersController@signUp');
    Route::post('resendVerification', 'API\UsersController@resendVerification');
    Route::post('forgotPassword', 'API\UsersController@forgotPassword');

    });
    // Routing for frontend user
    Route::group(['middleware' => ['auth:api','user_data',]], function () {

        Route::group(['prefix' => 'user'], function () {

        Route::get('logout', 'API\UsersController@logout');
        Route::post('changePassword', 'API\UsersController@changePassword');
        Route::post('reportUser', 'API\UsersController@reportUser');
        Route::post('uploadProfileImage', 'API\UsersController@uploadProfileImage');
        });


        Route::group(['prefix' => 'settings'], function () {
        Route::post('settingsOnOff', 'API\SettingsController@settingsOnOff');
        Route::get('getPages', 'API\SettingsController@getPages');
        Route::post('contactUs', 'API\SettingsController@contactUs');
        Route::post('changeEmail', 'API\SettingsController@changeEmail');
       
        });

        // blockunblock
        Route::group(['prefix' => 'blockunblock'], function () {
        
        Route::post('blockedUser', 'API\BlockUnblockController@blockedUser');
        Route::get('getBlockedUser', 'API\BlockUnblockController@getBlockedUser');
        Route::post('unblockedUser', 'API\BlockUnblockController@unblockedUser');
        
       });

        // report unreport user
        Route::group(['prefix' => 'reportUser'], function () {
        
        Route::post('reportUser', 'API\ReportUserController@reportUser');
        Route::get('getReportedUser', 'API\ReportUserController@getReportedUser');
        Route::post('unReportUser', 'API\ReportUserController@unReportUser');
        
       });
   
        
    });

});