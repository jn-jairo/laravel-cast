<?php

use JnJairo\Laravel\Cast\Cast;
use JnJairo\Laravel\Cast\Contracts\Cast as CastContract;
use JnJairo\Laravel\Cast\Contracts\Type;
use JnJairo\Laravel\Cast\Exceptions\InvalidTypeException;
use JnJairo\Laravel\Cast\Tests\Fixtures\Types\DummyType;

it('throws an exception when the type is not found', function () {
    $cast = new Cast();
    $cast->cast('foo', 'bar');
})->throws(InvalidTypeException::class, '[bar]: Type not found in the configuration.');

it('throws an exception when the type is missing the class', function () {
    $cast = new Cast([
        'types' => [
            'bar' => [],
        ],
    ]);
    $cast->cast('foo', 'bar');
})->throws(InvalidTypeException::class, '[bar]: Missing class in the configuration.');

it('throws an exception when the type\'s class does not exists', function () {
    $cast = new Cast([
        'types' => [
            'bar' => 'Bar',
        ],
    ]);
    $cast->cast('foo', 'bar');
})->throws(InvalidTypeException::class, '[bar]: Class "Bar" does not exists.');

it('throws an exception when the type\'s class does not implements the interface', function () {
    $cast = new Cast([
        'types' => [
            'bar' => stdClass::class,
        ],
    ]);
    $cast->cast('foo', 'bar');
})->throws(InvalidTypeException::class, '[bar]: Class "' . stdClass::class . '" does not implements the interface "'
    . Type::class . '".');

it('can cast', function () {
    $dummyType = new DummyType();

    expect($dummyType->cast('123'))
        ->toBe('123');
    expect($dummyType->castDb('123'))
        ->toBe('123');
    expect($dummyType->castJson('123'))
        ->toBe('123');

    expect($dummyType->cast(null))
        ->toBeNull();
    expect($dummyType->castDb(null))
        ->toBeNull();
    expect($dummyType->castJson(null))
        ->toBeNull();
});

it('can remove the configuration', function () {
    $type = 'dummy';

    $cast = new Cast([
        'types' => [
            $type => DummyType::class,
        ],
    ]);

    expect($cast->cast('123', $type))
        ->toBe('123');
    expect($cast->castDb('123', $type))
        ->toBe('123');
    expect($cast->castJson('123', $type))
        ->toBe('123');

    expect($cast->cast(null, $type))
        ->toBeNull();
    expect($cast->castDb(null, $type))
        ->toBeNull();
    expect($cast->castJson(null, $type))
        ->toBeNull();

    $cast->setConfig();

    expect(fn() => $cast->cast('123', $type))
        ->toThrow(InvalidTypeException::class, '[' . $type . ']: Type not found in the configuration.');
});

it('can remove the type from the configuration', function () {
    $type = 'dummy';

    $cast = new Cast([
        'types' => [
            $type => DummyType::class,
        ],
    ]);

    expect($cast->cast('123', $type))
        ->toBe('123');
    expect($cast->castDb('123', $type))
        ->toBe('123');
    expect($cast->castJson('123', $type))
        ->toBe('123');

    expect($cast->cast(null, $type))
        ->toBeNull();
    expect($cast->castDb(null, $type))
        ->toBeNull();
    expect($cast->castJson(null, $type))
        ->toBeNull();

    $cast->removeTypeConfig($type);

    expect(fn() => $cast->cast('123', $type))
        ->toThrow(InvalidTypeException::class, '[' . $type . ']: Type not found in the configuration.');
});

it('can set the configuration', function () {
    $type = 'dummy';

    $configClass = [
        'types' => [
            $type => DummyType::class,
        ],
    ];

    $configArray = [
        'types' => [
            $type => [
                'class' => DummyType::class,
                'config' => [
                    'foo' => 'bar',
                ],
            ],
        ],
    ];

    $cast = new Cast();

    expect($cast->getConfig())
        ->toBe(['types' => []]);

    $cast->setConfig([]);

    expect($cast->getConfig())
        ->toBe(['types' => []]);

    $cast->setConfig($configClass);

    expect($cast->getConfig())
        ->toBe($configClass);

    $cast->setConfig($configArray);

    expect($cast->getConfig())
        ->toBe($configArray);
});

it('can set the type configuration', function () {
    $type = 'dummy';

    $configTypeClass = DummyType::class;

    $configClass = [
        'types' => [
            $type => $configTypeClass,
        ],
    ];

    $configTypeArray = [
        'class' => DummyType::class,
        'config' => [
            'foo' => 'bar',
        ],
    ];

    $configArray = [
        'types' => [
            $type => $configTypeArray,
        ],
    ];

    $cast = new Cast();

    expect($cast->getTypeConfig($type))
        ->toBeNull();
    expect($cast->getConfig())
        ->toBe(['types' => []]);

    $cast->setTypeConfig($type, $configTypeClass);

    expect($cast->getTypeConfig($type))
        ->toBe($configTypeClass);
    expect($cast->getConfig())
        ->toBe($configClass);

    $cast->setTypeConfig($type, $configTypeArray);

    expect($cast->getTypeConfig($type))
        ->toBe($configTypeArray);
    expect($cast->getConfig())
        ->toBe($configArray);

    $cast->removeTypeConfig($type);

    expect($cast->getTypeConfig($type))
        ->toBeNull();
    expect($cast->getConfig())
        ->toBe(['types' => []]);
});

it('can configure the type using class', function () {
    $type = 'dummy';

    $cast = new Cast([
        'types' => [
            $type => DummyType::class,
        ],
    ]);

    expect($cast->cast('123', $type))
        ->toBe('123');
    expect($cast->castDb('123', $type))
        ->toBe('123');
    expect($cast->castJson('123', $type))
        ->toBe('123');

    expect($cast->cast(null, $type))
        ->toBeNull();
    expect($cast->castDb(null, $type))
        ->toBeNull();
    expect($cast->castJson(null, $type))
        ->toBeNull();
});

it('can configure the type using array', function () {
    $type = 'dummy';

    $dummy = new DummyType();

    /**
     * @var \Illuminate\Contracts\Container\Container $app
     */
    $app = app();
    $app->instance(DummyType::class, $dummy);

    $cast = new Cast([
        'types' => [
            $type => [
                'class' => DummyType::class,
                'config' => [
                    'foo' => 'bar',
                ],
            ],
        ],
    ]);

    expect($cast->cast('123', $type))
        ->toBe('123');
    expect($cast->castDb('123', $type))
        ->toBe('123');
    expect($cast->castJson('123', $type))
        ->toBe('123');

    expect($cast->cast(null, $type))
        ->toBeNull();
    expect($cast->castDb(null, $type))
        ->toBeNull();
    expect($cast->castJson(null, $type))
        ->toBeNull();

    expect($dummy->getConfig())
        ->toBe(['foo' => 'bar']);
});

it('can get the cast instance through the type instance', function () {
    $type = 'dummy';

    $dummy = new DummyType();

    /**
     * @var \Illuminate\Contracts\Container\Container $app
     */
    $app = app();
    $app->instance(DummyType::class, $dummy);

    $cast = new Cast([
        'types' => [
            $type => DummyType::class,
        ],
    ]);

    expect($cast->cast('123', $type))
        ->toBe('123');
    expect($cast->castDb('123', $type))
        ->toBe('123');
    expect($cast->castJson('123', $type))
        ->toBe('123');

    expect($cast->cast(null, $type))
        ->toBeNull();
    expect($cast->castDb(null, $type))
        ->toBeNull();
    expect($cast->castJson(null, $type))
        ->toBeNull();

    expect($dummy->getCast())
        ->toBeInstanceOf(CastContract::class);
});
