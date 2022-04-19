<?php

namespace JnJairo\Laravel\Cast\Types;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JnJairo\Laravel\Cast\Types\Type;

class EnumType extends Type
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

        if (! function_exists('enum_exists') || ! enum_exists($format)) {
            return $value;
        }

        if (! $value instanceof $format) {
            $value = $format::from($value);
        }

        return $value;
    }

    /**
     * Cast to database type.
     *
     * @param mixed $value
     * @param string $format
     * @return mixed
     */
    public function castDb($value, string $format = '')
    {
        if (is_null($value)) {
            return $value;
        }

        if (! function_exists('enum_exists') || ! enum_exists($format)) {
            return $value;
        }

        if (! $value instanceof $format) {
            $value = $format::from($value);
        }

        return $value->value;
    }

    /**
     * Cast to json type.
     *
     * @param mixed $value
     * @param string $format
     * @return mixed
     */
    public function castJson($value, string $format = '')
    {
        if (is_null($value)) {
            return $value;
        }

        if (! function_exists('enum_exists') || ! enum_exists($format)) {
            return $value;
        }

        if (! $value instanceof $format) {
            $value = $format::from($value);
        }

        if ($value instanceof Arrayable) {
            $value = $value->toArray();
        } elseif ($value instanceof Jsonable) {
            $value = json_decode($value->toJson(), true);
        } else {
            $value = $value->value;
        }

        return $value;
    }
}
