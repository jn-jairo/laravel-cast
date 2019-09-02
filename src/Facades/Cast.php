<?php

namespace JnJairo\Laravel\Cast\Facades;

use Illuminate\Support\Facades\Facade;
use JnJairo\Laravel\Cast\Contracts\Cast as CastContract;

class Cast extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        return CastContract::class;
    }
}
