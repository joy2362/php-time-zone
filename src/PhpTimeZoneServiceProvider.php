<?php

namespace Joy2362\PhpTimezone;

use Illuminate\Support\ServiceProvider;
use Joy2362\PhpTimezone\{Contract\TimeZoneManager,Service\TimeZoneService};

class PhpTimeZoneServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/./config/config.php' => $this->app->configPath('Timezone.php')
            ], 'config');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/config.php', 'Timezone');
        $this->app->bind(TimeZoneManager::class, TimeZoneService::class);
    }
}