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
        $this->loadMigrationsFrom(__DIR__.'/migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'simple_admin_api');
        $this->publishes([
            __DIR__ . '/views/assets' => public_path('vendor/simple_admin_api'),
            __DIR__ . '/views/templates' => resource_path('views/simple_admin_api'),
            __DIR__.'/config/simple_admin_api.php' => config_path('simple_admin_api.php'),
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

        $this->mergeConfigFrom(
            __DIR__.'/config/simple_admin_api.php', 'simple_admin_api'
        );
        
        $this->app['router']->aliasMiddleware('simple.admin',\Amet\SimpleAdminAPI\Middleware\SimpleAdminMiddleware::class);

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