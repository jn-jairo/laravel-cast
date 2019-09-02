<?php

namespace JnJairo\Laravel\Cast\Tests\Fixtures;

use Illuminate\Contracts\Support\Arrayable;

class DummyArrayable implements Arrayable
{
    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return ['foo' => 'bar'];
    }
}
