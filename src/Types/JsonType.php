<?php

namespace JnJairo\Laravel\Cast\Types;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

use function Safe\json_decode;
use function Safe\json_encode;

class JsonType extends Type
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

        return $this->asJson($value);
    }

    /**
     * Cast to database type.
     *
     * @param mixed $value
     * @param string $format
     * @return mixed
     */
    public function castDb(mixed $value, string $format = ''): mixed
    {
        if (is_null($value)) {
            return $value;
        }

        return $this->asJson($value);
    }

    /**
     * Cast to json type.
     *
     * @param mixed $value
     * @param string $format
     * @return mixed
     */
    public function castJson(mixed $value, string $format = ''): mixed
    {
        if (is_null($value)) {
            return $value;
        }

        return $this->asArray($value);
    }

    /**
     * Cast to array.
     *
     * @param mixed $value
     * @return array<array-key, mixed>|null
     */
    protected function asArray(mixed $value): ?array
    {
        if ($value instanceof Arrayable) {
            $value = $value->toArray();
        } elseif ($value instanceof Jsonable) {
            $value = json_decode($value->toJson(), true);
        } elseif (is_string($value)) {
            $value = json_decode($value, true);
        } elseif (is_object($value)) {
            $value = json_decode(json_encode($value), true);
        }

        if (! is_array($value)) {
            $value = null;
        }

        return $value;
    }

    /**
     * Cast to json.
     *
     * @param mixed $value
     * @return string|null
     */
    protected function asJson(mixed $value): ?string
    {
        if ($value instanceof Jsonable) {
            $value = $value->toJson();
        } elseif ($value instanceof Arrayable) {
            $value = json_encode($value->toArray());
        } elseif (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }

        if (! is_string($value)) {
            $value = null;
        }

        return $value;
    }
}
