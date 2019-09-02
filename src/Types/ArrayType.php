<?php

namespace JnJairo\Laravel\Cast\Types;

use JnJairo\Laravel\Cast\Types\JsonType;

class ArrayType extends JsonType
{
   /**
    * Default format.
    *
    * @var string
    */ 
    protected $defaultFormat = 'array';
}
