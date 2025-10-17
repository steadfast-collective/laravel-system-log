<?php

namespace SteadfastCollective\LaravelSystemLog\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \SteadfastCollective\LaravelSystemLog\LaravelSystemLog
 */
class LaravelSystemLog extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \SteadfastCollective\LaravelSystemLog\LaravelSystemLog::class;
    }
}
