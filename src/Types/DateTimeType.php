<?php

namespace JnJairo\Laravel\Cast\Types;

use DateTimeInterface;
use Illuminate\Support\Carbon;

class DateTimeType extends Type
{
    /**
     * Default format.
     *
     * @var string
     */
    protected string $defaultFormat = 'Y-m-d H:i:s';

    /**
     * Set configuration.
     *
     * @param array<string, mixed> $config
     * @return void
     */
    public function setConfig(array $config): void
    {
        parent::setConfig($config);

        if (
            isset($this->config['format'])
            && is_string($this->config['format'])
            && $this->config['format'] !== ''
        ) {
            $this->defaultFormat = $this->config['format'];
        }
    }

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

        return $this->asDateTime($value, $format);
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

        $value = $this->asDateTime($value, $format);

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
     * Cast to DateTime object.
     *
     * @param mixed $value
     * @param string $format
     * @return \Illuminate\Support\Carbon|null
     */
    protected function asDateTime(mixed $value, string $format): ?Carbon
    {
        if ($value instanceof DateTimeInterface) {
            $value = $value->format($format);
        }

        if (is_numeric($value)) {
            return Carbon::createFromTimestamp($value);
        }

        if (is_string($value)) {
            $value = Carbon::createFromFormat($format, $value);

            if (! $value instanceof Carbon) {
                $value = null;
            }

            return $value;
        }

        return null;
    }

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param \DateTimeInterface $date
     * @param string $format
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date, string $format): string
    {
        return $date->format($format);
    }
}
