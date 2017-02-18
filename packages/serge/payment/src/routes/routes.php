<?php
//
Route::get('payment/pesopay', [
    'middleware' => 'web',
    'as' => 'paymentPesopay',
    'uses' => 'serge\payment\PaymentController@pesopay']
);

Route::get('payment/datafeed', [
    'middleware' => 'web',
    'as' => 'datafeed',
    'uses' => 'serge\payment\PaymentController@datafeed']);