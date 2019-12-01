<?php

namespace JnJairo\Laravel\Cast\Types;

use Carbon\CarbonInterface;
use DateTimeInterface;
use Illuminate\Support\Carbon;
use JnJairo\Laravel\Cast\Types\Type;

class DateTimeType extends Type
{
    /**
     * Default format.
     *
     * @var string
     */
    protected $defaultFormat = 'Y-m-d H:i:s';

    /**
     * Set configuration.
     *
     * @param array $config
     * @return void
     */
    public function setConfig(array $config) : void
    {
        parent::setConfig($config);

        if (isset($this->config['format'])) {
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
    public function cast($value, string $format = '')
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
    public function castDb($value, string $format = '')
    {
        if (is_null($value)) {
            return $value;
        }

        if ($format === '') {
            $format = $this->defaultFormat;
        }

        $value = $this->asDateTime($value, $format);

        return $this->serializeDate($value, $format);
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
        return $this->castDb($value, $format);
    }

    /**
     * Cast to DateTime object with time set to 00:00:00.
     *
     * @param mixed $value
     * @return \Illuminate\Support\Carbon
     */
    protected function asDate($value, $format)
    {
        return $this->asDateTime($value, $format)->startOfDay();
    }

    /**
     * Cast to DateTime object.
     *
     * @param mixed $value
     * @return \Illuminate\Support\Carbon
     */
    protected function asDateTime($value, $format)
    {
        if ($value instanceof Carbon || $value instanceof CarbonInterface) {
            return Carbon::instance($value);
        }

        if ($value instanceof DateTimeInterface) {
            return Carbon::parse($value->format('Y-m-d H:i:s.u'), $value->getTimezone());
        }

        if (is_numeric($value)) {
            return Carbon::createFromTimestamp($value);
        }

        if ($this->isStandardDateFormat($value)) {
            return Carbon::instance(Carbon::createFromFormat('Y-m-d', $value)->startOfDay());
        }

        // @codeCoverageIgnoreStart
        // https://bugs.php.net/bug.php?id=75577
        if (version_compare(PHP_VERSION, '7.3.0-dev', '<')) {
            $format = str_replace('.v', '.u', $format);
        }
        // @codeCoverageIgnoreEnd

        return Carbon::createFromFormat($format, $value);
    }

    /**
     * Determine if the given value is a standard date format.
     *
     * @param string $value
     * @return bool
     */
    protected function isStandardDateFormat($value)
    {
        return preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $value);
    }

    /**
     * Cast to unix timestamp.
     *
     * @param mixed $value
     * @return int
     */
    protected function asTimestamp($value, $format)
    {
        return $this->asDateTime($value, $format)->getTimestamp();
    }

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param \DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date, $format)
    {
        return $date->format($format);
    }
}
