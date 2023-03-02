<?php

use JnJairo\Laravel\Cast\Cast;
use JnJairo\Laravel\Cast\Types\StringType;

it('can cast', function () {
    $type = 'string';

    $cast = new Cast([
        'types' => [
            $type => StringType::class,
        ],
    ]);

    expect($cast->cast(1.23, $type))
        ->toBe('1.23');
    expect($cast->castDb(1.23, $type))
        ->toBe('1.23');
    expect($cast->castJson(1.23, $type))
        ->toBe('1.23');

    expect($cast->cast(null, $type))
        ->toBeNull();
    expect($cast->castDb(null, $type))
        ->toBeNull();
    expect($cast->castJson(null, $type))
        ->toBeNull();
});
