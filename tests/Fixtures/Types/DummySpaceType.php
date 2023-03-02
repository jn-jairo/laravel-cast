<?php

namespace JnJairo\Laravel\Cast\Tests\Fixtures\Types;

use JnJairo\Laravel\Cast\Types\Type;

class DummySpaceType extends Type
{
    /**
     * Cast to PHP type.
     *
     * @param mixed $value
     * @param string $format
     * @return mixed
     */
    public function cast(mixed $value, string $format = ''): mixed
    {
        if (is_null($value) || ! is_string($value)) {
            return $value;
        }

        $formatParts = explode(':', $format);

        $step = (int) ($formatParts[0] ?? 1);

        if ($step < 1) {
            $step = 1;
        }

        $space = $formatParts[1] ?? ' ';

        return implode($space, str_split($value, $step));
    }
}
