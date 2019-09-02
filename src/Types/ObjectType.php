<?php

namespace JnJairo\Laravel\Cast\Types;

use JnJairo\Laravel\Cast\Types\JsonType;

class ObjectType extends JsonType
{
    /**
     * Default format.
     *
     * @var string
     */
    protected $defaultFormat = 'object';
}
