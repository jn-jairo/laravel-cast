<?php

namespace JnJairo\Laravel\Cast\Types;

class FloatType extends Type
{
    /**
     * Cast to PHP type.
     *
     * @param mixed $value
     * @param string $format
     * @return mixed
     */
    public function cast(mixed $value, string $format = ''): mixed
    {
        if (is_null($value)) {
            return $value;
        }

        return floatval($value);
    }
}
