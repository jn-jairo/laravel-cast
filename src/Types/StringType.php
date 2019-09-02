<?php

namespace JnJairo\Laravel\Cast\Types;

use JnJairo\Laravel\Cast\Types\Type;

class StringType extends Type
{
    /**
     * Cast to PHP type.
     *
     * @param mixed $value
     * @param strint $format
     * @return mixed
     */
    public function cast($value, string $format = '')
    {
        if (is_null($value)) {
            return $value;
        }

        return (string) $value;
    }
}
