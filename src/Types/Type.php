<?php

namespace JnJairo\Laravel\Cast\Types;

use JnJairo\Laravel\Cast\Contracts\Type as TypeContract;

abstract class Type implements TypeContract
{
    /**
     * Configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * Set configuration.
     *
     * @param array $config
     * @return void
     */
    public function setConfig(array $config) : void
    {
        $this->config = $config;
    }

    /**
     * Get configuration.
     *
     * @return array
     */
    public function getConfig() : array
    {
        return $this->config;
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
        return $this->cast($value, $format);
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
        return $this->cast($value, $format);
    }
}
