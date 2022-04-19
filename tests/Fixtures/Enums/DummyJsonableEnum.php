<?php

namespace JnJairo\Laravel\Cast\Tests\Fixtures\Enums;

use Illuminate\Contracts\Support\Jsonable;

enum DummyJsonableEnum : int implements Jsonable
{
    case foo = 1;
    case bar = 2;

    public function description() : string
    {
        return match ($this) {
            self::foo => 'foo description',
            self::bar => 'bar description',
        };
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode([
            'name' => $this->name,
            'value' => $this->value,
            'description' => $this->description(),
        ]);
    }
}
