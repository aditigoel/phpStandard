<?php

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

    Route::get('mangopay/createAccount', 'API\PaymentController@createAccount');
    Route::get('mangopay/createWallet', 'API\PaymentController@createWallet');
    Route::get('mangopay/getUserWallet', 'API\PaymentController@getUserWallet');
    Route::get('mangopay/viewWallet', 'API\PaymentController@viewWallet');
    Route::post('mangopay/addCard', 'API\PaymentController@addCard');
    Route::get('mangopay/getUserCards', 'API\PaymentController@getUserCards');
    Route::post('mangopay/createBank', 'API\PaymentController@createBank');
    Route::post('mangopay/deleteCard', 'API\PaymentController@deleteCard');
    Route::post('mangopay/deleteBankAccount', 'API\PaymentController@deleteBankAccount');
    Route::get('mangopay/getUserBankAccount', 'API\PaymentController@getUserBankAccount');
    Route::post('mangopay/createDirectPayIn', 'API\PaymentController@createDirectPayIn');
    Route::post('mangopay/releasePayment', 'API\PaymentController@releasePayment');

    // Routing for frontend user
    Route::group(['middleware' => ['auth:api']], function () {

    });
});
