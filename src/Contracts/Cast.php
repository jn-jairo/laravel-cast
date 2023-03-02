<?php

namespace JnJairo\Laravel\Cast\Contracts;

interface Cast
{
    /**
     * Cast to PHP types.
     *
     * @param mixed $value
     * @param string $type
     * @param string $format
     * @return mixed
     */
    public function cast(mixed $value, string $type, string $format = ''): mixed;

    /**
     * Cast to database types.
     *
     * @param mixed $value
     * @param string $type
     * @param string $format
     * @return mixed
     */
    public function castDb(mixed $value, string $type, string $format = ''): mixed;

    /**
     * Cast to json types.
     *
     * @param mixed $value
     * @param string $type
     * @param string $format
     * @return mixed
     */
    public function castJson(mixed $value, string $type, string $format = ''): mixed;

    /**
     * Set configuration.
     *
     * @param array<string, mixed> $config
     * @return void
     */
    public function setConfig(array $config = []): void;

    /**
     * Get configuration.
     *
     * @return array<string, mixed>
     */
    public function getConfig(): array;

    /**
     * Set type configuration.
     *
     * @param string $type
     * @param string|array<string, mixed> $config
     * @return void
     */
    public function setTypeConfig(string $type, string|array $config): void;

    /**
     * Get type configuration.
     *
     * @param string $type
     * @return string|array<string, mixed>|null
     */
    public function getTypeConfig(string $type): string|array|null;

    /**
     * Remove type configuration.
     *
     * @param string $type
     * @return void
     */
    public function removeTypeConfig(string $type): void;
}
