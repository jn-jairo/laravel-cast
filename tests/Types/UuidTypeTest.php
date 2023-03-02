<?php

use JnJairo\Laravel\Cast\Cast;
use JnJairo\Laravel\Cast\Types\UuidType;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

$dataset = [
    'uuid1' => [1, 'uuid1'],
    'uuid4' => [4, 'uuid4'],
    'ordered' => [4, 'ordered'],
];

it('can cast', function () {
    $type = 'uuid';

    $cast = new Cast([
        'types' => [
            $type => UuidType::class,
        ],
    ]);

    $uuid = Uuid::uuid4();
    $string = $uuid->toString();
    $binary = $uuid->getBytes();
    $hex = bin2hex($binary);

    expect($cast->cast($uuid, $type))
        ->toBe($uuid);
    expect($cast->cast($string, $type))
        ->toEqual($uuid);
    expect($cast->cast($binary, $type))
        ->toEqual($uuid);
    expect($cast->cast($hex, $type))
        ->toEqual($uuid);

    expect($cast->castDb($uuid, $type))
        ->toBe($binary);
    expect($cast->castDb($string, $type))
        ->toBe($binary);
    expect($cast->castDb($binary, $type))
        ->toBe($binary);
    expect($cast->castDb($hex, $type))
        ->toBe($binary);

    expect($cast->castJson($uuid, $type))
        ->toBe($string);
    expect($cast->castJson($string, $type))
        ->toBe($string);
    expect($cast->castJson($binary, $type))
        ->toBe($string);
    expect($cast->castJson($hex, $type))
        ->toBe($string);

    expect($cast->cast(null, $type))
        ->toBeNull();
    expect($cast->castDb(null, $type))
        ->toBeNull();
    expect($cast->castJson(null, $type))
        ->toBeNull();
});

it('can create a new UUID', function () {
    $type = 'uuid';

    $cast = new Cast([
        'types' => [
            $type => UuidType::class,
        ],
    ]);

    $value = '';

    /**
     * @var \Ramsey\Uuid\UuidInterface $uuid
     */
    $uuid = $cast->cast($value, $type);

    expect($uuid)
        ->toBeInstanceOf(UuidInterface::class);
    expect($uuid->getVersion())
        ->toBe(4);

    /**
     * @var string $uuid
     */
    $uuid = $cast->castDb($value, $type);

    expect($uuid)
        ->toBeString();
    expect(strlen($uuid))
        ->toBe(16);

    /**
     * @var string $uuid
     */
    $uuid = $cast->castJson($value, $type);

    expect($uuid)
        ->toBeString();
    expect(strlen($uuid))
        ->toBe(36);
});

it('can be configured', function (int $version, string $format) {
    $type = 'uuid';

    $cast = new Cast([
        'types' => [
            $type => [
                'class' => UuidType::class,
                'config' => [
                    'format' => $format,
                ],
            ],
        ],
    ]);

    $value = '';

    /**
     * @var \Ramsey\Uuid\UuidInterface $uuid
     */
    $uuid = $cast->cast($value, $type);

    expect($uuid)
        ->toBeInstanceOf(UuidInterface::class);
    expect($uuid->getVersion())
        ->toBe($version);

    /**
     * @var string $uuid
     */
    $uuid = $cast->castDb($value, $type);

    expect($uuid)
        ->toBeString();
    expect(strlen($uuid))
        ->toBe(16);

    /**
     * @var string $uuid
     */
    $uuid = $cast->castJson($value, $type);

    expect($uuid)
        ->toBeString();
    expect(strlen($uuid))
        ->toBe(36);
})->with($dataset);

it('can be formatted', function (int $version, string $format) {
    $type = 'uuid';

    $cast = new Cast([
        'types' => [
            $type => UuidType::class,
        ],
    ]);

    $value = '';

    /**
     * @var \Ramsey\Uuid\UuidInterface $uuid
     */
    $uuid = $cast->cast($value, $type, $format);

    expect($uuid)
        ->toBeInstanceOf(UuidInterface::class);
    expect($uuid->getVersion())
        ->toBe($version);

    /**
     * @var string $uuid
     */
    $uuid = $cast->castDb($value, $type, $format);

    expect($uuid)
        ->toBeString();
    expect(strlen($uuid))
        ->toBe(16);

    /**
     * @var string $uuid
     */
    $uuid = $cast->castJson($value, $type, $format);

    expect($uuid)
        ->toBeString();
    expect(strlen($uuid))
        ->toBe(36);
})->with($dataset);
