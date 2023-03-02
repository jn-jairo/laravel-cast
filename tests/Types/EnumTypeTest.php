<?php

use JnJairo\Laravel\Cast\Cast;
use JnJairo\Laravel\Cast\Tests\Fixtures\Enums\DummyArrayableEnum;
use JnJairo\Laravel\Cast\Tests\Fixtures\Enums\DummyIntegerEnum;
use JnJairo\Laravel\Cast\Tests\Fixtures\Enums\DummyJsonableEnum;
use JnJairo\Laravel\Cast\Tests\Fixtures\Enums\DummyStringEnum;
use JnJairo\Laravel\Cast\Tests\Fixtures\Types\DummyType;
use JnJairo\Laravel\Cast\Types\EnumType;

it('can cast string enum', function () {
    $type = 'enum';
    $format = DummyStringEnum::class;

    $cast = new Cast([
        'types' => [
            $type => EnumType::class,
        ],
    ]);

    expect($cast->cast('foo', $type, $format))
        ->toBe($format::foo);
    expect($cast->cast($format::foo, $type, $format))
        ->toBe($format::foo);

    expect($cast->castDb('foo', $type, $format))
        ->toBe('foo');
    expect($cast->castDb($format::foo, $type, $format))
        ->toBe('foo');

    expect($cast->castJson('foo', $type, $format))
        ->toBe('foo');
    expect($cast->castJson($format::foo, $type, $format))
        ->toBe('foo');

    expect($cast->cast(null, $type))
        ->toBeNull();
    expect($cast->castDb(null, $type))
        ->toBeNull();
    expect($cast->castJson(null, $type))
        ->toBeNull();
});

it('can cast integer enum', function () {
    $type = 'enum';
    $format = DummyIntegerEnum::class;

    $cast = new Cast([
        'types' => [
            $type => EnumType::class,
        ],
    ]);

    expect($cast->cast(1, $type, $format))
        ->toBe($format::one);
    expect($cast->cast($format::one, $type, $format))
        ->toBe($format::one);

    expect($cast->castDb(1, $type, $format))
        ->toBe(1);
    expect($cast->castDb($format::one, $type, $format))
        ->toBe(1);

    expect($cast->castJson(1, $type, $format))
        ->toBe(1);
    expect($cast->castJson($format::one, $type, $format))
        ->toBe(1);

    expect($cast->cast(null, $type))
        ->toBeNull();
    expect($cast->castDb(null, $type))
        ->toBeNull();
    expect($cast->castJson(null, $type))
        ->toBeNull();
});

it('can cast arrayable enum', function () {
    $type = 'enum';
    $format = DummyArrayableEnum::class;

    $cast = new Cast([
        'types' => [
            $type => EnumType::class,
        ],
    ]);

    expect($cast->cast(1, $type, $format))
        ->toBe($format::foo);
    expect($cast->cast($format::foo, $type, $format))
        ->toBe($format::foo);

    expect($cast->castDb(1, $type, $format))
        ->toBe(1);
    expect($cast->castDb($format::foo, $type, $format))
        ->toBe(1);

    expect($cast->castJson(1, $type, $format))
        ->toBe([
            'name' => 'foo',
            'value' => 1,
            'description' => 'foo description',
        ]);
    expect($cast->castJson($format::foo, $type, $format))
        ->toBe([
            'name' => 'foo',
            'value' => 1,
            'description' => 'foo description',
        ]);

    expect($cast->cast(null, $type))
        ->toBeNull();
    expect($cast->castDb(null, $type))
        ->toBeNull();
    expect($cast->castJson(null, $type))
        ->toBeNull();
});

it('can cast jsonable enum', function () {
    $type = 'enum';
    $format = DummyJsonableEnum::class;

    $cast = new Cast([
        'types' => [
            $type => EnumType::class,
        ],
    ]);

    expect($cast->cast(1, $type, $format))
        ->toBe($format::foo);
    expect($cast->cast($format::foo, $type, $format))
        ->toBe($format::foo);

    expect($cast->castDb(1, $type, $format))
        ->toBe(1);
    expect($cast->castDb($format::foo, $type, $format))
        ->toBe(1);

    expect($cast->castJson(1, $type, $format))
        ->toBe([
            'name' => 'foo',
            'value' => 1,
            'description' => 'foo description',
        ]);
    expect($cast->castJson($format::foo, $type, $format))
        ->toBe([
            'name' => 'foo',
            'value' => 1,
            'description' => 'foo description',
        ]);

    expect($cast->cast(null, $type))
        ->toBeNull();
    expect($cast->castDb(null, $type))
        ->toBeNull();
    expect($cast->castJson(null, $type))
        ->toBeNull();
});

it('does not cast invalid enum', function () {
    $type = 'enum';
    $format = DummyType::class;

    $cast = new Cast([
        'types' => [
            $type => EnumType::class,
        ],
    ]);

    expect($cast->cast('foo', $type, $format))
        ->toBe('foo');
    expect($cast->cast(1, $type, $format))
        ->toBe(1);

    expect($cast->castDb('foo', $type, $format))
        ->toBe('foo');
    expect($cast->castDb(1, $type, $format))
        ->toBe(1);

    expect($cast->castJson('foo', $type, $format))
        ->toBe('foo');
    expect($cast->castJson(1, $type, $format))
        ->toBe(1);

    expect($cast->cast(null, $type))
        ->toBeNull();
    expect($cast->castDb(null, $type))
        ->toBeNull();
    expect($cast->castJson(null, $type))
        ->toBeNull();
});
