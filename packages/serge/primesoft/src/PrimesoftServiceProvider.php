<?php

namespace Serge\Primesoft;

use Illuminate\Support\ServiceProvider;

class PrimesoftServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('primesoft', function($app) {
            return new PrimeSoft();
        });

        $this->publishes([
            __DIR__.'/config/primesoft.php' => config_path('primesoft.php'),
            // __DIR__.'/views' => base_path('resources/views/primesoft'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom( __DIR__.'/config/primesoft.php', 'primesoft');
    }

     /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('primesoft');
    }
}
