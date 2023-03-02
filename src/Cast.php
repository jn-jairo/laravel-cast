<?php

namespace JnJairo\Laravel\Cast;

use JnJairo\Laravel\Cast\Contracts\Cast as CastContract;
use JnJairo\Laravel\Cast\Contracts\Type;
use JnJairo\Laravel\Cast\Exceptions\InvalidTypeException;

class Cast implements CastContract
{
    /**
     * Configuration.
     *
     * @var array<string, mixed>
     */
    protected array $config = [];

    /**
     * Types instances.
     *
     * @var \JnJairo\Laravel\Cast\Contracts\Type[]
     */
    protected array $typeInstances = [];

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    /**
     * Cast to PHP types.
     *
     * @param mixed $value
     * @param string $type
     * @param string $format
     * @return mixed
     */
    public function cast(mixed $value, string $type, string $format = ''): mixed
    {
        return $this->getTypeInstance($type)->cast($value, $format);
    }

    /**
     * Cast to database types.
     *
     * @param mixed $value
     * @param string $type
     * @param string $format
     * @return mixed
     */
    public function castDb(mixed $value, string $type, string $format = ''): mixed
    {
        return $this->getTypeInstance($type)->castDb($value, $format);
    }

    /**
     * Cast to json types.
     *
     * @param mixed $value
     * @param string $type
     * @param string $format
     * @return mixed
     */
    public function castJson(mixed $value, string $type, string $format = ''): mixed
    {
        return $this->getTypeInstance($type)->castJson($value, $format);
    }

    /**
     * Set configuration.
     *
     * @param array<string, mixed> $config
     * @return void
     */
    public function setConfig(array $config = []): void
    {
        $this->config = array_merge([
            'types' => [],
        ], $config);

        $this->typeInstances = [];
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
     * Set type configuration.
     *
     * @param string $type
     * @param string|array<string, mixed> $config
     * @return void
     */
    public function setTypeConfig(string $type, string|array $config): void
    {
        $this->config['types'][$type] = $config;
        unset($this->typeInstances[$type]);
    }

    /**
     * Get type configuration.
     *
     * @param string $type
     * @return string|array<string, mixed>|null
     */
    public function getTypeConfig(string $type): string|array|null
    {
        /**
         * @var string|array<string, mixed>|null $config
         */
        $config = $this->config['types'][$type] ?? null;

        return $config;
    }

    /**
     * Remove type configuration.
     *
     * @param string $type
     * @return void
     */
    public function removeTypeConfig(string $type): void
    {
        unset($this->config['types'][$type]);
        unset($this->typeInstances[$type]);
    }

    /**
     * Get the type instance.
     *
     * @param string $type
     * @return \JnJairo\Laravel\Cast\Contracts\Type
     *
     * @throws \JnJairo\Laravel\Cast\Exceptions\InvalidTypeException
     */
    protected function getTypeInstance(string $type): Type
    {
        if (! isset($this->config['types'][$type])) {
            throw new InvalidTypeException('[' . $type . ']: Type not found in the configuration.');
        }

        if (! isset($this->typeInstances[$type])) {
            $class = $this->config['types'][$type];
            $config = array();

            if (is_array($class)) {
                if (! isset($class['class'])) {
                    throw new InvalidTypeException('[' . $type . ']: Missing class in the configuration.');
                }

                if (isset($class['config'])) {
                    $config = $class['config'];
                }

                $class = $class['class'];
            }

            if (! class_exists($class)) {
                throw new InvalidTypeException('[' . $type . ']: Class "' . $class . '" does not exists.');
            }

            /**
             * @var object $instance
             */
            $instance = app($class);

            if (! is_a($instance, Type::class)) {
                throw new InvalidTypeException('[' . $type . ']: Class "' . $class
                    . '" does not implements the interface "' . Type::class . '".');
            }

            $instance->setConfig($config);

            $instance->setCast($this);

            $this->typeInstances[$type] = $instance;
        }

        return $this->typeInstances[$type];
    }
}
