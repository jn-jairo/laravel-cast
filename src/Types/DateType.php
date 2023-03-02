<?php

namespace JnJairo\Laravel\Cast\Types;

use Illuminate\Support\Carbon;

class DateType extends DateTimeType
{
    /**
     * Default format.
     *
     * @var string
     */
    protected string $defaultFormat = 'Y-m-d';

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

        return $this->asDate($value, $format);
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

        if ($format === '') {
            $format = $this->defaultFormat;
        }

        $value = $this->asDate($value, $format);

        if (is_null($value)) {
            return $value;
        }

        return $this->serializeDate($value, $format);
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
        return $this->castDb($value, $format);
    }

    /**
     * Cast to DateTime object with time set to 00:00:00.
     *
     * @param mixed $value
     * @param string $format
     * @return \Illuminate\Support\Carbon|null
     */
    protected function asDate(mixed $value, string $format): ?Carbon
    {
        return $this->asDateTime($value, $format)?->startOfDay();
    }
}
