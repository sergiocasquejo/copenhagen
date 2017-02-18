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
            __DIR__.'/Config/payment.php' => config_path('payment'),
            __DIR__.'/views' => base_path('resources/views/vendor/courier'),
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
        $this->mergeConfigFrom( __DIR__.'/Config/payment.php', 'payment');
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
