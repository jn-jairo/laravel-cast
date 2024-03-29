<?php

namespace JnJairo\Laravel\Cast\Tests\Fixtures;

use Illuminate\Contracts\Support\Jsonable;

class DummyJsonable implements Jsonable
{
    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options
     * @return string
     */
    public function toJson($options = 0): string
    {
        return '{"foo":"bar"}';
    }
}
