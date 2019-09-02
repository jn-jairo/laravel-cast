<?php

namespace JnJairo\Laravel\Cast\Types;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use JnJairo\Laravel\Cast\Types\Type;
use function Safe\json_decode;
use function Safe\json_encode;

class JsonType extends Type
{
   /**
    * Default format.
    *
    * @var string
    */ 
    protected $defaultFormat = 'json';

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
     * @param strint $format
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

        switch ($format) {
            case 'json':
                $value = $this->asJson($value);
                break;
            case 'array':
                $value = $this->asArray($value);
                break;
            case 'object':
                $value = $this->asObject($value);
                break;
            case 'collection':
                $value = $this->asCollection($value);
                break;
        }

        return $value;
    }

    /**
     * Cast to database type.
     *
     * @param mixed $value
     * @param strint $format
     * @return mixed
     */
    public function castDb($value, string $format = '')
    {
        if (is_null($value)) {
            return $value;
        }

        return $this->asJson($value);
    }

    /**
     * Cast to json type.
     *
     * @param mixed $value
     * @param strint $format
     * @return mixed
     */
    public function castJson($value, string $format = '')
    {
        if (is_null($value)) {
            return $value;
        }

        return $this->asArray($value);
    }

    /**
     * Cast to array.
     *
     * @param mixed $value
     * @return array
     */
    protected function asArray($value)
    {
        if ($value instanceof Arrayable) {
            $value = $value->toArray();
        } elseif ($value instanceof Jsonable) {
            $value = json_decode($value->toJson(), true);
        } elseif (is_string($value)) {
            $value = json_decode($value, true);
        } elseif (is_object($value)) {
            $value = (array) $value;
        }

        return $value;
    }

    /**
     * Cast to object.
     *
     * @param mixed $value
     * @return object
     */
    protected function asObject($value)
    {
        if ($value instanceof Arrayable) {
            $value = (object) $value->toArray();
        } elseif ($value instanceof Jsonable) {
            $value = (object) json_decode($value->toJson(), true);
        } elseif (is_string($value)) {
            $value = (object) json_decode($value, true);
        } elseif (is_array($value)) {
            $value = (object) $value;
        }

        return $value;
    }

    /**
     * Cast to json.
     *
     * @param mixed $value
     * @return string
     */
    protected function asJson($value)
    {
        if ($value instanceof Jsonable) {
            $value = $value->toJson();
        } elseif ($value instanceof Arrayable) {
            $value = json_encode($value->toArray());
        } elseif (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }

        return $value;
    }

    /**
     * Cast to Collection.
     *
     * @param mixed $value
     * @return \Illuminate\Support\Collection
     */
    protected function asCollection($value)
    {
        if (! $value instanceof Collection) {
            $value = new Collection($this->asArray($value));
        }

        return $value;
    }
}
