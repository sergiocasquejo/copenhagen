<?php
//
Route::get('payment/pesopay', [
    'middleware' => 'web',
    'as' => 'paymentPesopay',
    'uses' => 'serge\payment\PaymentController@pesopay']
);

// Route::get('payment/pesopay/{status}', [
//     'middleware' => 'web',
//     'as' => 'paymentPesopayStatus',
//     'uses' => 'serge\payment\PaymentController@pesopay']
// );

Route::post('payment/datafeed', [
    'middleware' => 'web',
    'as' => 'datafeed',
    'uses' => 'serge\payment\PaymentController@datafeed']);
