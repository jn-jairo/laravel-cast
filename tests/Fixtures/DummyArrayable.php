<?php

namespace JnJairo\Laravel\Cast\Tests\Fixtures;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements \Illuminate\Contracts\Support\Arrayable<array-key, mixed>
 */
class DummyArrayable implements Arrayable
{
    /**
     * Get the instance as an array.
     *
     * @return array<array-key, mixed>
     */
    public function toArray(): array
    {
        return ['foo' => 'bar'];
    }
}
