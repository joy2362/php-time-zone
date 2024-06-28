<?php

namespace Joy2362\PhpTimezone;

use Illuminate\Support\ServiceProvider;

class PhpTimeZoneServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/./config/config.php' => config_path('Timezone.php')
            ], 'config');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/config.php', 'Timezone');
        $this->app->bind('TimeZoneService', function () {
            return new TimeZoneService();
        });
    }
}
