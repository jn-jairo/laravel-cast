<?php

use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Carbon;
use JnJairo\Laravel\Cast\Cast;
use JnJairo\Laravel\Cast\Types\DateType;

$dataset = [
    'Y-m-d',
    'd/m/Y',
];

it('can cast', function () {
    $type = 'date';

    $cast = new Cast([
        'types' => [
            $type => DateType::class,
        ],
    ]);

    $now = Carbon::now();
    $date = $now->format('Y-m-d');
    $carbon = (Carbon::createFromFormat('Y-m-d', $date) ?: null)?->startOfDay();
    $dateTime = DateTime::createFromFormat('Y-m-d', $date);
    $timestamp = $carbon?->getTimestamp();

    expect($cast->cast($now, $type))
        ->toBeInstanceOf(Carbon::class);
    expect($cast->cast($date, $type))
        ->toBeInstanceOf(Carbon::class);
    expect($cast->cast($carbon, $type))
        ->toBeInstanceOf(Carbon::class);
    expect($cast->cast($dateTime, $type))
        ->toBeInstanceOf(Carbon::class);
    expect($cast->cast($timestamp, $type))
        ->toBeInstanceOf(Carbon::class);

    expect($cast->cast($now, $type))
        ->toEqual($carbon);
    expect($cast->cast($date, $type))
        ->toEqual($carbon);
    expect($cast->cast($carbon, $type))
        ->toEqual($carbon);
    expect($cast->cast($dateTime, $type))
        ->toEqual($carbon);
    expect($cast->cast($timestamp, $type))
        ->toEqual($carbon);

    expect($cast->castDb($now, $type))
        ->toBe($date);
    expect($cast->castDb($date, $type))
        ->toBe($date);
    expect($cast->castDb($carbon, $type))
        ->toBe($date);
    expect($cast->castDb($dateTime, $type))
        ->toBe($date);
    expect($cast->castDb($timestamp, $type))
        ->toBe($date);

    expect($cast->castJson($now, $type))
        ->toBe($date);
    expect($cast->castJson($date, $type))
        ->toBe($date);
    expect($cast->castJson($carbon, $type))
        ->toBe($date);
    expect($cast->castJson($dateTime, $type))
        ->toBe($date);
    expect($cast->castJson($timestamp, $type))
        ->toBe($date);

    expect($cast->cast(null, $type))
        ->toBeNull();
    expect($cast->castDb(null, $type))
        ->toBeNull();
    expect($cast->castJson(null, $type))
        ->toBeNull();
});

it('returns null if the date is invalid and strict mode disabled', function () {
    $type = 'date';

    $cast = new Cast([
        'types' => [
            $type => DateType::class,
        ],
    ]);

    $strict = Carbon::isStrictModeEnabled();

    Carbon::useStrictMode(false);

    $date = 'invalid';

    expect($cast->cast($date, $type))
        ->toBeNull();
    expect($cast->castDb($date, $type))
        ->toBeNull();
    expect($cast->castJson($date, $type))
        ->toBeNull();

    Carbon::useStrictMode($strict);
});

it('throws an exception if the date is invalid and strict mode enabled', function () {
    $type = 'date';

    $cast = new Cast([
        'types' => [
            $type => DateType::class,
        ],
    ]);

    $strict = Carbon::isStrictModeEnabled();

    Carbon::useStrictMode(true);

    $date = 'invalid';

    expect(fn() => $cast->cast($date, $type))
        ->toThrow(InvalidFormatException::class);
    expect(fn() => $cast->castDb($date, $type))
        ->toThrow(InvalidFormatException::class);
    expect(fn() => $cast->castJson($date, $type))
        ->toThrow(InvalidFormatException::class);

    Carbon::useStrictMode($strict);
});

it('can be configured', function (string $format) {
    $type = 'date';

    $cast = new Cast([
        'types' => [
            $type => [
                'class' => DateType::class,
                'config' => [
                    'format' => $format,
                ],
            ],
        ],
    ]);

    $now = Carbon::now();
    $date = $now->format($format);
    $carbon = (Carbon::createFromFormat($format, $date) ?: null)?->startOfDay();
    $dateTime = DateTime::createFromFormat($format, $date);
    $timestamp = $carbon?->getTimestamp();

    expect($cast->cast($now, $type))
        ->toBeInstanceOf(Carbon::class);
    expect($cast->cast($date, $type))
        ->toBeInstanceOf(Carbon::class);
    expect($cast->cast($carbon, $type))
        ->toBeInstanceOf(Carbon::class);
    expect($cast->cast($dateTime, $type))
        ->toBeInstanceOf(Carbon::class);
    expect($cast->cast($timestamp, $type))
        ->toBeInstanceOf(Carbon::class);

    expect($cast->cast($now, $type))
        ->toEqual($carbon);
    expect($cast->cast($date, $type))
        ->toEqual($carbon);
    expect($cast->cast($carbon, $type))
        ->toEqual($carbon);
    expect($cast->cast($dateTime, $type))
        ->toEqual($carbon);
    expect($cast->cast($timestamp, $type))
        ->toEqual($carbon);

    expect($cast->castDb($now, $type))
        ->toBe($date);
    expect($cast->castDb($date, $type))
        ->toBe($date);
    expect($cast->castDb($carbon, $type))
        ->toBe($date);
    expect($cast->castDb($dateTime, $type))
        ->toBe($date);
    expect($cast->castDb($timestamp, $type))
        ->toBe($date);

    expect($cast->castJson($now, $type))
        ->toBe($date);
    expect($cast->castJson($date, $type))
        ->toBe($date);
    expect($cast->castJson($carbon, $type))
        ->toBe($date);
    expect($cast->castJson($dateTime, $type))
        ->toBe($date);
    expect($cast->castJson($timestamp, $type))
        ->toBe($date);

    expect($cast->cast(null, $type))
        ->toBeNull();
    expect($cast->castDb(null, $type))
        ->toBeNull();
    expect($cast->castJson(null, $type))
        ->toBeNull();
})->with($dataset);

it('can be formatted', function (string $format) {
    $type = 'date';

    $cast = new Cast([
        'types' => [
            $type => DateType::class,
        ],
    ]);

    $now = Carbon::now();
    $date = $now->format($format);
    $carbon = (Carbon::createFromFormat($format, $date) ?: null)?->startOfDay();
    $dateTime = DateTime::createFromFormat($format, $date);
    $timestamp = $carbon?->getTimestamp();

    expect($cast->cast($now, $type, $format))
        ->toBeInstanceOf(Carbon::class);
    expect($cast->cast($date, $type, $format))
        ->toBeInstanceOf(Carbon::class);
    expect($cast->cast($carbon, $type, $format))
        ->toBeInstanceOf(Carbon::class);
    expect($cast->cast($dateTime, $type, $format))
        ->toBeInstanceOf(Carbon::class);
    expect($cast->cast($timestamp, $type, $format))
        ->toBeInstanceOf(Carbon::class);

    expect($cast->cast($now, $type, $format))
        ->toEqual($carbon);
    expect($cast->cast($date, $type, $format))
        ->toEqual($carbon);
    expect($cast->cast($carbon, $type, $format))
        ->toEqual($carbon);
    expect($cast->cast($dateTime, $type, $format))
        ->toEqual($carbon);
    expect($cast->cast($timestamp, $type, $format))
        ->toEqual($carbon);

    expect($cast->castDb($now, $type, $format))
        ->toBe($date);
    expect($cast->castDb($date, $type, $format))
        ->toBe($date);
    expect($cast->castDb($carbon, $type, $format))
        ->toBe($date);
    expect($cast->castDb($dateTime, $type, $format))
        ->toBe($date);
    expect($cast->castDb($timestamp, $type, $format))
        ->toBe($date);

    expect($cast->castJson($now, $type, $format))
        ->toBe($date);
    expect($cast->castJson($date, $type, $format))
        ->toBe($date);
    expect($cast->castJson($carbon, $type, $format))
        ->toBe($date);
    expect($cast->castJson($dateTime, $type, $format))
        ->toBe($date);
    expect($cast->castJson($timestamp, $type, $format))
        ->toBe($date);

    expect($cast->cast(null, $type, $format))
        ->toBeNull();
    expect($cast->castDb(null, $type, $format))
        ->toBeNull();
    expect($cast->castJson(null, $type, $format))
        ->toBeNull();
})->with($dataset);
