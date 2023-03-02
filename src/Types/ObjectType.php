<?php

namespace JnJairo\Laravel\Cast\Types;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class ObjectType extends JsonType
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

        return $this->asObject($value);
    }

    /**
     * Cast to object.
     *
     * @param mixed $value
     * @return object|null
     */
    protected function asObject(mixed $value): ?object
    {
        if ($value instanceof Arrayable) {
            $value = (object) $value->toArray();
        } elseif ($value instanceof Jsonable) {
            $value = (object) json_decode($value->toJson(), true);
        } elseif (is_string($value)) {
            $value = (object) json_decode($value, true);
        } elseif (is_array($value)) {
            $value = (object) $value;
        }

        if (! is_object($value)) {
            $value = null;
        }

        return $value;
    }
}
