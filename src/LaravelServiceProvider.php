<?php

namespace LTBackup\Extension;


use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application as LaravelApplication;
use LTBackup\Extension\Console\Commands\BackupCommand;
use LTBackup\Extension\Console\Commands\ClearCommand;
use LTBackup\Extension\Console\Commands\InstallCommand;

//use Laravel\Lumen\Application as LumenApplication;

class LaravelServiceProvider extends  ServiceProvider
{
    protected $commands = [
            InstallCommand::class,
            BackupCommand::class,
            ClearCommand::class
        ];
     /**
     * Booting the package.
     */
    public function boot()
    {

        $this->loadViewsFrom(__DIR__.'/Resources/views', 'laravel-admin-backup');

        if (method_exists($this, 'loadViewsFrom')) {
            $this->loadViewsFrom(__DIR__.'/Resources/views', 'laravel-admin-backup');
        }
        if (method_exists($this, 'publishes')) {
            $this->publishes([
                __DIR__.'/Resources/views' => base_path('/resources/views/vendor/laravel-admin-backup'),
            ], 'views');
            $this->setupConfig();
        }
        $this->loadMigrationsFrom(__DIR__.'/Databases/migrations');
        $this->publishes([__DIR__.'/Resources/assets' => public_path('vendor/laravel-admin-backup')], 'laravel-admin-backup');
        $this->loadRoutesFrom(__DIR__.'/Route/routes.php');
    }



    /**
     * Register the service provider.
     */
    public function register()
    {
         $this->commands($this->commands);
    }

    /**
     * Setup the config.
     */
    protected function setupConfig()
    {
        $configSource = realpath(__DIR__ . '/config.php');
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([
                $configSource => config_path('laravel-admin-backup.php')
            ]);
        }
//        elseif ($this->app instanceof LumenApplication) {
//            $this->app->configure('ltbackup');
//        }
        $this->mergeConfigFrom($configSource, 'laravel-admin-backup');

    }



}
