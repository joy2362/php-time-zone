<?php

namespace Joy2362\PhpTimezone\Facades;

use Illuminate\Support\Facades\Facade;
use Joy2362\PhpTimezone\Contract\TimeZoneManager;

class TimeZone extends Facade
{
    protected static function getFacadeAccessor()
    {
        return TimeZoneManager::class;
    }
}