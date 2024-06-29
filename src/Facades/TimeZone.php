<?php

namespace Joy2362\PhpTimezone\Facades;

use Illuminate\Support\Facades\Facade;

class TimeZone extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'TimeZoneService';
    }
}