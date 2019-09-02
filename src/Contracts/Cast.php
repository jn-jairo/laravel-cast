<?php

namespace JnJairo\Laravel\Cast\Contracts;

interface Cast
{
    /**
     * Cast to PHP types.
     *
     * @param mixed $value
     * @param string $type
     * @param strint $format
     * @return mixed
     */
    public function cast($value, string $type, string $format = '');

    /**
     * Cast to database types.
     *
     * @param mixed $value
     * @param string $type
     * @param strint $format
     * @return mixed
     */
    public function castDb($value, string $type, string $format = '');

    /**
     * Cast to json types.
     *
     * @param mixed $value
     * @param string $type
     * @param strint $format
     * @return mixed
     */
    public function castJson($value, string $type, string $format = '');
}
