<?php

namespace Serge\Payment;

use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('pesopaypayment', function($app) {
            return new PesopayPayment();
        });

        $this->publishes([
            __DIR__.'/config/payment.php' => config_path('payment.php'),
            __DIR__.'/config/pesopay.php' => config_path('pesopay.php'),
            __DIR__.'/views' => base_path('resources/views/payment'),
        ]);

        $this->loadViewsFrom(__DIR__.'/views', 'payment');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__.'/routes/routes.php';
        $this->app->make('Serge\Payment\PaymentController');
        $this->mergeConfigFrom( __DIR__.'/config/payment.php', 'payment');
        $this->mergeConfigFrom( __DIR__.'/config/pesopay.php', 'pesopay');
    }

     /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('pesopaypayment');
    }
}
