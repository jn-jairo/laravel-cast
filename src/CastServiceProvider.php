<?php

namespace JnJairo\Laravel\Cast;

use Illuminate\Support\ServiceProvider;
use JnJairo\Laravel\Cast\Cast;
use JnJairo\Laravel\Cast\Contracts\Cast as CastContract;

class CastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() : void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/cast.php' => config_path('cast.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() : void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/cast.php', 'cast');

        $this->app->singleton('cast', CastContract::class);

        $this->app->singleton(CastContract::class, function ($app) {
            return new Cast($app['config']['cast']);
        });
    }
}
