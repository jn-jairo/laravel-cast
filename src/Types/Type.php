<?php

namespace JnJairo\Laravel\Cast\Types;

use JnJairo\Laravel\Cast\Contracts\Cast;
use JnJairo\Laravel\Cast\Contracts\Type as TypeContract;

abstract class Type implements TypeContract
{
    /**
     * Cast instance.
     *
     * @var \JnJairo\Laravel\Cast\Contracts\Cast
     */
    protected Cast $cast;

    /**
     * Configuration.
     *
     * @var array<string, mixed>
     */
    protected array $config;

    /**
     * Set cast instance.
     *
     * @param \JnJairo\Laravel\Cast\Contracts\Cast $cast
     * @return void
     */
    public function setCast(Cast $cast): void
    {
        $this->cast = $cast;
    }

    /**
     * Get cast instance.
     *
     * @return \JnJairo\Laravel\Cast\Contracts\Cast
     */
    public function getCast(): Cast
    {
        return $this->cast;
    }

    /**
     * Set configuration.
     *
     * @param array<string, mixed> $config
     * @return void
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * Get configuration.
     *
     * @return array<string, mixed>
     */
    public function getConfig(): array
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
    public function cast(mixed $value, string $format = ''): mixed
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
}
