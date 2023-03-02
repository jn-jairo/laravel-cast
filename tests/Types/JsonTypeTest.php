<?php

use Illuminate\Support\Collection;
use JnJairo\Laravel\Cast\Cast;
use JnJairo\Laravel\Cast\Tests\Fixtures\DummyArrayable;
use JnJairo\Laravel\Cast\Tests\Fixtures\DummyJsonable;
use JnJairo\Laravel\Cast\Types\JsonType;

it('can cast', function () {
    $type = 'json';

    $cast = new Cast([
        'types' => [
            $type => JsonType::class,
        ],
    ]);

    $json = '{"foo":"bar"}';
    $array = ['foo' => 'bar'];
    $object = (object) $array;
    $collection = new Collection($array);
    $arrayable = new DummyArrayable();
    $jsonable = new DummyJsonable();

    expect($cast->cast($json, $type))
        ->toBe($json);
    expect($cast->cast($array, $type))
        ->toBe($json);
    expect($cast->cast($object, $type))
        ->toBe($json);
    expect($cast->cast($collection, $type))
        ->toBe($json);
    expect($cast->cast($arrayable, $type))
        ->toBe($json);
    expect($cast->cast($jsonable, $type))
        ->toBe($json);

    expect($cast->castDb($json, $type))
        ->toBe($json);
    expect($cast->castDb($array, $type))
        ->toBe($json);
    expect($cast->castDb($object, $type))
        ->toBe($json);
    expect($cast->castDb($collection, $type))
        ->toBe($json);
    expect($cast->castDb($arrayable, $type))
        ->toBe($json);
    expect($cast->castDb($jsonable, $type))
        ->toBe($json);

    expect($cast->castJson($json, $type))
        ->toBe($array);
    expect($cast->castJson($array, $type))
        ->toBe($array);
    expect($cast->castJson($object, $type))
        ->toBe($array);
    expect($cast->castJson($collection, $type))
        ->toBe($array);
    expect($cast->castJson($arrayable, $type))
        ->toBe($array);
    expect($cast->castJson($jsonable, $type))
        ->toBe($array);

    expect($cast->cast(null, $type))
        ->toBeNull();
    expect($cast->castDb(null, $type))
        ->toBeNull();
    expect($cast->castJson(null, $type))
        ->toBeNull();
});

it('returns null if the value is an invalid type', function () {
    $type = 'json';

    $cast = new Cast([
        'types' => [
            $type => JsonType::class,
        ],
    ]);

    $value = false;

    expect($cast->cast($value, $type))
        ->toBeNull();
    expect($cast->castDb($value, $type))
        ->toBeNull();
    expect($cast->castJson($value, $type))
        ->toBeNull();
});
