<?php

namespace JnJairo\Laravel\Cast\Types;

use JnJairo\Laravel\Cast\Types\Type;

class IntegerType extends Type
{
    /**
     * Cast to PHP type.
     *
     * @param mixed $value
     * @param string $format
     * @return mixed
     */
    public function cast($value, string $format = '')
    {
        if (is_null($value)) {
            return $value;
        }

        return (int) $value;
    }
}
