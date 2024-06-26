<?php

namespace Joy2362\PhpTimezone\Facades;

use Illuminate\Support\Facades\Facade;

class TimeZoneFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'TimeZoneService';
    }
}
