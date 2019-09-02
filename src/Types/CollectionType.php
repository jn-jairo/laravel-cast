<?php

namespace JnJairo\Laravel\Cast\Types;

use JnJairo\Laravel\Cast\Types\JsonType;

class CollectionType extends JsonType
{
   /**
    * Default format.
    *
    * @var string
    */ 
    protected $defaultFormat = 'collection';
}
