<?php

namespace Amet\SimpleORM;

use Illuminate\Support\ServiceProvider;
use Amet\SimpleORM\Commands\GeneratorApiAdmin;

class SimpleAdminAPIServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot() {
         $this->loadViewsFrom(__DIR__.'/views', 'simple_api_admin');
         $this->publishes([
            __DIR__ . '/views/assets' => public_path('vendor/simple_api_admin'),
            __DIR__ . '/views/templates' => resource_path('views/simple_api_admin'),
        ], 'simple_api_admin');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('ametsuramet.simple_admin_api', function ($app) {
            return new GeneratorApiAdmin();
        });

        

        $this->commands([
            'ametsuramet.simple_admin_api',
        ]);
    
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'ametsuramet.simple_admin_api',
        ];
    }
}