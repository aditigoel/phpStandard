<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */
Route::get('/phpinfo', function () {
    return phpinfo();
});




Auth::routes();

Route::namespace ('FRONTEND')->group(function () {
    Route::get('verification/{key}', ['uses' => 'UsersController@verifyUser']); # verify user on click email template
    Route::get('/reset-password/{token}', ['uses' => 'UsersController@showResetForm']); #show reset form on web
    Route::post('resetPassword', ['uses' => 'UsersController@resetPassword']); # user reset password
});

Route::get('/', function () {
    return 'Welcome to laravel';
});

