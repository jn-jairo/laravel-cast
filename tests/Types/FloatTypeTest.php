<?php

use JnJairo\Laravel\Cast\Cast;
use JnJairo\Laravel\Cast\Types\FloatType;

it('can cast', function () {
    $type = 'float';

    $cast = new Cast([
        'types' => [
            $type => FloatType::class,
        ],
    ]);

    expect($cast->cast('1.23', $type))
        ->toBe(1.23);
    expect($cast->castDb('1.23', $type))
        ->toBe(1.23);
    expect($cast->castJson('1.23', $type))
        ->toBe(1.23);

    expect($cast->cast(null, $type))
        ->toBeNull();
    expect($cast->castDb(null, $type))
        ->toBeNull();
    expect($cast->castJson(null, $type))
        ->toBeNull();
});
