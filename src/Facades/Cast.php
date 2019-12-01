<?php

namespace JnJairo\Laravel\Cast\Facades;

use Illuminate\Support\Facades\Facade;
use JnJairo\Laravel\Cast\Contracts\Cast as CastContract;

/**
 * @method static mixed cast($value, string $type, string $format = '')
 * @method static mixed castDb($value, string $type, string $format = '')
 * @method static mixed castJson($value, string $type, string $format = '')
 */
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
