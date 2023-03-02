<?php

namespace JnJairo\Laravel\Cast\Tests\Fixtures\Enums;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements \Illuminate\Contracts\Support\Arrayable<array-key, mixed>
 */
enum DummyArrayableEnum : int implements Arrayable
{
    case foo = 1;
    case bar = 2;

    public function description(): string
    {
        return match ($this) {
            self::foo => 'foo description',
            self::bar => 'bar description',
        };
    }

    /**
     * Get the instance as an array.
     *
     * @return array<array-key, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'value' => $this->value,
            'description' => $this->description(),
        ];
    }
}
