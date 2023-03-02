<?php

namespace JnJairo\Laravel\Cast\Types;

use Illuminate\Support\Collection;

class CollectionType extends JsonType
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

        return $this->asCollection($value);
    }

    /**
     * Cast to Collection.
     *
     * @param mixed $value
     * @return \Illuminate\Support\Collection<array-key, mixed>|null
     */
    protected function asCollection(mixed $value): ?Collection
    {
        if (! $value instanceof Collection) {
            $value = $this->asArray($value);

            if (! is_null($value)) {
                $value = new Collection($value);
            }
        }

        return $value;
    }
}
