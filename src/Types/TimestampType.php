<?php

namespace JnJairo\Laravel\Cast\Types;

class TimestampType extends DateTimeType
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

        if ($format === '') {
            $format = $this->defaultFormat;
        }

        return $this->asTimestamp($value, $format);
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
        return $this->cast($value, $format);
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
        return $this->cast($value, $format);
    }

    /**
     * Cast to unix timestamp.
     *
     * @param mixed $value
     * @param string $format
     * @return int
     */
    protected function asTimestamp(mixed $value, string $format): int
    {
        return $this->asDateTime($value, $format)?->getTimestamp() ?? 0;
    }
}
