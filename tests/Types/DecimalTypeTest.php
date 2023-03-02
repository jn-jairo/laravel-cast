<?php

use Decimal\Decimal;
use JnJairo\Laravel\Cast\Cast;
use JnJairo\Laravel\Cast\Types\DecimalType;

$datasetSeparator = [
    'pipe' => '|',
    'comma' => ',',
];

$dataset = [
    'up' => [10, 1, 'up', '1234.55', '1234.6'],
    'down' => [10, 1, 'down', '1234.55', '1234.5'],
    'ceiling' => [10, 1, 'ceiling', '1234.51', '1234.6'],
    'floor' => [10, 1, 'floor', '1234.59', '1234.5'],
    'half_up' => [10, 1, 'half_up', '1234.55', '1234.6'],
    'half_down' => [10, 1, 'half_down', '1234.55', '1234.5'],
    'half_even' => [10, 1, 'half_even', '1234.55', '1234.6'],
    'half_odd' => [10, 1, 'half_odd', '1234.55', '1234.5'],
    'truncate' => [10, 1, 'truncate', '1234.55', '1234.5'],
    'default' => [10, 1, 'default', '1234.55', '1234.6'],
    'zero' => [10, 3, 'default', '1234.5', '1234.500'],
    'precision_places_only' => [10, 3, null, '1234.5555', '1234.556'],
    'places_only' => [null, 3, null, '1234.5555', '1234.556'],
    'round_mode_only' => [null, null, 'half_up', '1234.5555', '1234.56'],
];

it('can cast', function () {
    $type = 'decimal';

    $cast = new Cast([
        'types' => [
            $type => DecimalType::class,
        ],
    ]);

    $decimal = new Decimal('1234.56', 28);
    $float = 1234.56;
    $string = '1234.56';

    expect($cast->cast($decimal, $type))
        ->toEqual($decimal);
    expect($cast->cast($float, $type))
        ->toEqual($decimal);
    expect($cast->cast($string, $type))
        ->toEqual($decimal);

    expect($cast->castDb($decimal, $type))
        ->toBe($string);
    expect($cast->castDb($float, $type))
        ->toBe($string);
    expect($cast->castDb($string, $type))
        ->toBe($string);

    expect($cast->castJson($decimal, $type))
        ->toBe($string);
    expect($cast->castJson($float, $type))
        ->toBe($string);
    expect($cast->castJson($string, $type))
        ->toBe($string);

    expect($cast->cast(null, $type))
        ->toBeNull();
    expect($cast->castDb(null, $type))
        ->toBeNull();
    expect($cast->castJson(null, $type))
        ->toBeNull();
});

it('returns null if the value is an invalid type', function () {
    $type = 'decimal';

    $cast = new Cast([
        'types' => [
            $type => DecimalType::class,
        ],
    ]);

    $value = [];

    expect($cast->cast($value, $type))
        ->toBeNull();
    expect($cast->castDb($value, $type))
        ->toBeNull();
    expect($cast->castJson($value, $type))
        ->toBeNull();
});

it('can be configured', function (
    ?int $precision,
    ?int $places,
    ?string $roundMode,
    string $string,
    string $stringRounded,
) {
    $type = 'decimal';

    $cast = new Cast([
        'types' => [
            $type => [
                'class' => DecimalType::class,
                'config' => [
                    'precision' => $precision,
                    'places' => $places,
                    'round_mode' => $roundMode,
                ],
            ],
        ],
    ]);

    $decimal = new Decimal($string);
    $float = (float) $string;

    $decimalRounded = new Decimal($stringRounded);

    expect($cast->cast($decimal, $type))
        ->toEqual($decimalRounded);
    expect($cast->cast($float, $type))
        ->toEqual($decimalRounded);
    expect($cast->cast($string, $type))
        ->toEqual($decimalRounded);

    expect($cast->castDb($decimal, $type))
        ->toBe($stringRounded);
    expect($cast->castDb($float, $type))
        ->toBe($stringRounded);
    expect($cast->castDb($string, $type))
        ->toBe($stringRounded);

    expect($cast->castJson($decimal, $type))
        ->toBe($stringRounded);
    expect($cast->castJson($float, $type))
        ->toBe($stringRounded);
    expect($cast->castJson($string, $type))
        ->toBe($stringRounded);

    expect($cast->cast(null, $type))
        ->toBeNull();
    expect($cast->castDb(null, $type))
        ->toBeNull();
    expect($cast->castJson(null, $type))
        ->toBeNull();
})->with($dataset);

it('can be formatted', function (
    string $separator,
    ?int $precision,
    ?int $places,
    ?string $roundMode,
    string $string,
    string $stringRounded,
) {
    $type = 'decimal';

    $cast = new Cast([
        'types' => [
            $type => DecimalType::class,
        ],
    ]);

    $decimal = new Decimal($string);
    $float = (float) $string;

    $decimalRounded = new Decimal($stringRounded);

    $format = [];

    if (! is_null($precision) && ! is_null($places)) {
        $format[] = $precision . ':' . $places;
    } elseif (! is_null($places)) {
        $format[] = $places;
    }

    if (! is_null($roundMode)) {
        $format[] = $roundMode;
    }

    $format = implode($separator, $format);

    expect($cast->cast($decimal, $type, $format))
        ->toEqual($decimalRounded);
    expect($cast->cast($float, $type, $format))
        ->toEqual($decimalRounded);
    expect($cast->cast($string, $type, $format))
        ->toEqual($decimalRounded);

    expect($cast->castDb($decimal, $type, $format))
        ->toBe($stringRounded);
    expect($cast->castDb($float, $type, $format))
        ->toBe($stringRounded);
    expect($cast->castDb($string, $type, $format))
        ->toBe($stringRounded);

    expect($cast->castJson($decimal, $type, $format))
        ->toBe($stringRounded);
    expect($cast->castJson($float, $type, $format))
        ->toBe($stringRounded);
    expect($cast->castJson($string, $type, $format))
        ->toBe($stringRounded);

    expect($cast->cast(null, $type, $format))
        ->toBeNull();
    expect($cast->castDb(null, $type, $format))
        ->toBeNull();
    expect($cast->castJson(null, $type, $format))
        ->toBeNull();
})->with($datasetSeparator)
  ->with($dataset);
