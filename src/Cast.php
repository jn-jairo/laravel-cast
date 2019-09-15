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
     * @var array
     */
    protected $config = [];

    /**
     * Types instances.
     *
     * @var array
     */
    protected $typeInstances = [];

    /**
     * Callable that creates the type instance.
     *
     * The callable receives the class as the first parameter.
     *
     * @var callable
     */
    protected $typeCreator;

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'types' => [],
        ], $config);
    }

    /**
     * Cast to PHP types.
     *
     * @param mixed $value
     * @param string $type
     * @param strint $format
     * @return mixed
     */
    public function cast($value, string $type, string $format = '')
    {
        return $this->getTypeInstance($type)->cast($value, $format);
    }

    /**
     * Cast to database types.
     *
     * @param mixed $value
     * @param string $type
     * @param strint $format
     * @return mixed
     */
    public function castDb($value, string $type, string $format = '')
    {
        return $this->getTypeInstance($type)->castDb($value, $format);
    }

    /**
     * Cast to json types.
     *
     * @param mixed $value
     * @param string $type
     * @param strint $format
     * @return mixed
     */
    public function castJson($value, string $type, string $format = '')
    {
        return $this->getTypeInstance($type)->castJson($value, $format);
    }

    /**
     * Get the type instance.
     *
     * @param string $type
     * @return \JnJairo\Laravel\Cast\Contracts\Type
     *
     * @throws \JnJairo\Laravel\Cast\Exceptions\InvalidTypeException
     */
    protected function getTypeInstance(string $type) : Type
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

            $instance = app($class);

            if (! is_a($instance, Type::class)) {
                throw new InvalidTypeException('[' . $type . ']: Class "' . $class
                    . '" does not implements the interface "' . Type::class . '".');
            }

            $instance->setConfig($config);

            $this->typeInstances[$type] = $instance;
        }

        return $this->typeInstances[$type];
    }
}
