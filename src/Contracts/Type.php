<?php

namespace JnJairo\Laravel\Cast\Contracts;

interface Type
{
    /**
     * Set cast instance.
     *
     * @param \JnJairo\Laravel\Cast\Contracts\Cast $cast
     * @return void
     */
    public function setCast(Cast $cast): void;

    /**
     * Get cast instance.
     *
     * @return \JnJairo\Laravel\Cast\Contracts\Cast
     */
    public function getCast(): Cast;

    /**
     * Set configuration.
     *
     * @param array<string, mixed> $config
     * @return void
     */
    public function setConfig(array $config): void;

    /**
     * Get configuration.
     *
     * @return array<string, mixed>
     */
    public function getConfig(): array;

    /**
     * Cast to PHP type.
     *
     * @param mixed $value
     * @param string $format
     * @return mixed
     */
    public function cast(mixed $value, string $format = ''): mixed;

    /**
     * Cast to database type.
     *
     * @param mixed $value
     * @param string $format
     * @return mixed
     */
    public function castDb(mixed $value, string $format = ''): mixed;

    /**
     * Cast to json type.
     *
     * @param mixed $value
     * @param string $format
     * @return mixed
     */
    public function castJson(mixed $value, string $format = ''): mixed;
}
