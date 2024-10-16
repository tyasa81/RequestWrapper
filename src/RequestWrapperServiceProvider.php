<?php

namespace tyasa81\RequestWrapper;

use Illuminate\Support\ServiceProvider;
use tyasa81\RequestWrapper\Services\GuzzleWrapper;

class RequestWrapperServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
        $this->mergeConfigFrom(__DIR__.'/../config/requestwrapper.php', 'requestwrapper');
    }

    public function boot()
    {
        //
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/requestwrapper.php' => config_path('requestwrapper.php'),
            ], 'requestwrapper-config');
        }
        $this->app->bind('guzzlewrapper', function () {
            return new GuzzleWrapper;
        });
    }
}
