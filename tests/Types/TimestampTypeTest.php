<?php

use Illuminate\Support\Carbon;
use JnJairo\Laravel\Cast\Cast;
use JnJairo\Laravel\Cast\Types\TimestampType;

$dataset = [
    'Y-m-d H:i:s',
    'd/m/Y H:i',
];

it('can cast', function () {
    $type = 'timestamp';

    $cast = new Cast([
        'types' => [
            $type => TimestampType::class,
        ],
    ]);

    $now = Carbon::now();
    $date = $now->format('Y-m-d H:i:s');
    $carbon = Carbon::createFromFormat('Y-m-d H:i:s', $date) ?: null;
    $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $date);
    $timestamp = $carbon?->getTimestamp();

    expect($cast->cast($now, $type))
        ->toBeInt();
    expect($cast->cast($date, $type))
        ->toBeInt();
    expect($cast->cast($carbon, $type))
        ->toBeInt();
    expect($cast->cast($dateTime, $type))
        ->toBeInt();
    expect($cast->cast($timestamp, $type))
        ->toBeInt();

    expect($cast->cast($now, $type))
        ->toBe($timestamp);
    expect($cast->cast($date, $type))
        ->toBe($timestamp);
    expect($cast->cast($carbon, $type))
        ->toBe($timestamp);
    expect($cast->cast($dateTime, $type))
        ->toBe($timestamp);
    expect($cast->cast($timestamp, $type))
        ->toBe($timestamp);

    expect($cast->castDb($now, $type))
        ->toBe($timestamp);
    expect($cast->castDb($date, $type))
        ->toBe($timestamp);
    expect($cast->castDb($carbon, $type))
        ->toBe($timestamp);
    expect($cast->castDb($dateTime, $type))
        ->toBe($timestamp);
    expect($cast->castDb($timestamp, $type))
        ->toBe($timestamp);

    expect($cast->castJson($now, $type))
        ->toBe($timestamp);
    expect($cast->castJson($date, $type))
        ->toBe($timestamp);
    expect($cast->castJson($carbon, $type))
        ->toBe($timestamp);
    expect($cast->castJson($dateTime, $type))
        ->toBe($timestamp);
    expect($cast->castJson($timestamp, $type))
        ->toBe($timestamp);

    expect($cast->cast(null, $type))
        ->toBeNull();
    expect($cast->castDb(null, $type))
        ->toBeNull();
    expect($cast->castJson(null, $type))
        ->toBeNull();
});

it('can be configured', function (string $format) {
    $type = 'timestamp';

    $cast = new Cast([
        'types' => [
            $type => [
                'class' => TimestampType::class,
                'config' => [
                    'format' => $format,
                ],
            ],
        ],
    ]);

    $now = Carbon::now();
    $date = $now->format($format);
    $carbon = Carbon::createFromFormat($format, $date) ?: null;
    $dateTime = DateTime::createFromFormat($format, $date);
    $timestamp = $carbon?->getTimestamp();

    expect($cast->cast($now, $type))
        ->toBeInt();
    expect($cast->cast($date, $type))
        ->toBeInt();
    expect($cast->cast($carbon, $type))
        ->toBeInt();
    expect($cast->cast($dateTime, $type))
        ->toBeInt();
    expect($cast->cast($timestamp, $type))
        ->toBeInt();

    expect($cast->cast($now, $type))
        ->toBe($timestamp);
    expect($cast->cast($date, $type))
        ->toBe($timestamp);
    expect($cast->cast($carbon, $type))
        ->toBe($timestamp);
    expect($cast->cast($dateTime, $type))
        ->toBe($timestamp);
    expect($cast->cast($timestamp, $type))
        ->toBe($timestamp);

    expect($cast->castDb($now, $type))
        ->toBe($timestamp);
    expect($cast->castDb($date, $type))
        ->toBe($timestamp);
    expect($cast->castDb($carbon, $type))
        ->toBe($timestamp);
    expect($cast->castDb($dateTime, $type))
        ->toBe($timestamp);
    expect($cast->castDb($timestamp, $type))
        ->toBe($timestamp);

    expect($cast->castJson($now, $type))
        ->toBe($timestamp);
    expect($cast->castJson($date, $type))
        ->toBe($timestamp);
    expect($cast->castJson($carbon, $type))
        ->toBe($timestamp);
    expect($cast->castJson($dateTime, $type))
        ->toBe($timestamp);
    expect($cast->castJson($timestamp, $type))
        ->toBe($timestamp);

    expect($cast->cast(null, $type))
        ->toBeNull();
    expect($cast->castDb(null, $type))
        ->toBeNull();
    expect($cast->castJson(null, $type))
        ->toBeNull();
})->with($dataset);

it('can be formatted', function (string $format) {
    $type = 'timestamp';

    $cast = new Cast([
        'types' => [
            $type => TimestampType::class,
        ],
    ]);

    $now = Carbon::now();
    $date = $now->format($format);
    $carbon = Carbon::createFromFormat($format, $date) ?: null;
    $dateTime = DateTime::createFromFormat($format, $date);
    $timestamp = $carbon?->getTimestamp();

    expect($cast->cast($now, $type, $format))
        ->toBeInt();
    expect($cast->cast($date, $type, $format))
        ->toBeInt();
    expect($cast->cast($carbon, $type, $format))
        ->toBeInt();
    expect($cast->cast($dateTime, $type, $format))
        ->toBeInt();
    expect($cast->cast($timestamp, $type, $format))
        ->toBeInt();

    expect($cast->cast($now, $type, $format))
        ->toBe($timestamp);
    expect($cast->cast($date, $type, $format))
        ->toBe($timestamp);
    expect($cast->cast($carbon, $type, $format))
        ->toBe($timestamp);
    expect($cast->cast($dateTime, $type, $format))
        ->toBe($timestamp);
    expect($cast->cast($timestamp, $type, $format))
        ->toBe($timestamp);

    expect($cast->castDb($now, $type, $format))
        ->toBe($timestamp);
    expect($cast->castDb($date, $type, $format))
        ->toBe($timestamp);
    expect($cast->castDb($carbon, $type, $format))
        ->toBe($timestamp);
    expect($cast->castDb($dateTime, $type, $format))
        ->toBe($timestamp);
    expect($cast->castDb($timestamp, $type, $format))
        ->toBe($timestamp);

    expect($cast->castJson($now, $type, $format))
        ->toBe($timestamp);
    expect($cast->castJson($date, $type, $format))
        ->toBe($timestamp);
    expect($cast->castJson($carbon, $type, $format))
        ->toBe($timestamp);
    expect($cast->castJson($dateTime, $type, $format))
        ->toBe($timestamp);
    expect($cast->castJson($timestamp, $type, $format))
        ->toBe($timestamp);

    expect($cast->cast(null, $type, $format))
        ->toBeNull();
    expect($cast->castDb(null, $type, $format))
        ->toBeNull();
    expect($cast->castJson(null, $type, $format))
        ->toBeNull();
})->with($dataset);
