<?php

namespace Amet\SimpleAdminAPI;

use Illuminate\Support\ServiceProvider;
use Amet\SimpleAdminAPI\Commands\GeneratorApiAdmin;
use Amet\SimpleAdminAPI\Commands\RebuildMenu;

class SimpleAdminAPIServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot() {
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadViewsFrom(__DIR__.'/views', 'simple_admin_api');
        $this->publishes([
            __DIR__ . '/views/assets' => public_path('vendor/simple_admin_api'),
            __DIR__ . '/views/templates' => resource_path('views/simple_admin_api'),
        ], 'simple_admin_api');
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

        $this->app->singleton('ametsuramet.simple_admin_api_rebuild_menu', function ($app) {
            return new RebuildMenu();
        });

        

        $this->commands([
            'ametsuramet.simple_admin_api',
            'ametsuramet.simple_admin_api_rebuild_menu',
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
            'ametsuramet.simple_admin_api_rebuild_menu',
        ];
    }
}