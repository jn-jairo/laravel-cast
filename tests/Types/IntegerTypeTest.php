<?php

use JnJairo\Laravel\Cast\Cast;
use JnJairo\Laravel\Cast\Types\IntegerType;

it('can cast', function () {
    $type = 'integer';

    $cast = new Cast([
        'types' => [
            $type => IntegerType::class,
        ],
    ]);

    expect($cast->cast('123', $type))
        ->toBe(123);
    expect($cast->castDb('123', $type))
        ->toBe(123);
    expect($cast->castJson('123', $type))
        ->toBe(123);

    expect($cast->cast(null, $type))
        ->toBeNull();
    expect($cast->castDb(null, $type))
        ->toBeNull();
    expect($cast->castJson(null, $type))
        ->toBeNull();
});
