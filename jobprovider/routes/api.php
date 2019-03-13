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
    Route::group(['middleware' => ['auth:api','user_data',]], function () {

        Route::group(['prefix' => 'pro-jobs'], function () {

        Route::post('add', 'API\ProJobsController@add');
        Route::post('edit', 'API\ProJobsController@edit');
        Route::post('delete', 'API\ProJobsController@delete');
        Route::get('getSingleJob', 'API\ProJobsController@getSingleJob');
        Route::get('getAllJobs', 'API\ProJobsController@getAllJobs');
        Route::post('invite-job', 'API\ProJobsController@inviteJob');
       

       });

    });
});