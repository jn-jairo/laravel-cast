<?php

use JnJairo\Laravel\Cast\Cast;
use JnJairo\Laravel\Cast\Types\BooleanType;

it('can cast', function () {
    $type = 'bool';

    $cast = new Cast([
        'types' => [
            $type => BooleanType::class,
        ],
    ]);

    expect($cast->cast(1, $type))
        ->toBeTrue();
    expect($cast->castDb('0', $type))
        ->toBeFalse();
    expect($cast->castJson('1', $type))
        ->toBeTrue();

    expect($cast->cast(null, $type))
        ->toBeNull();
    expect($cast->castDb(null, $type))
        ->toBeNull();
    expect($cast->castJson(null, $type))
        ->toBeNull();
});
