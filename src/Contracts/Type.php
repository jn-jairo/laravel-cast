<?php

namespace JnJairo\Laravel\Cast\Contracts;

interface Type
{
    /**
     * Set configuration.
     *
     * @param array $config
     * @return void
     */
    public function setConfig(array $config) : void;

    /**
     * Get configuration.
     *
     * @return array
     */
    public function getConfig() : array;

    /**
     * Cast to PHP type.
     *
     * @param mixed $value
     * @param string $format
     * @return mixed
     */
    public function cast($value, string $format = '');

    /**
     * Cast to database type.
     *
     * @param mixed $value
     * @param string $format
     * @return mixed
     */
    public function castDb($value, string $format = '');

    /**
     * Cast to json type.
     *
     * @param mixed $value
     * @param string $format
     * @return mixed
     */
    public function castJson($value, string $format = '');
}
