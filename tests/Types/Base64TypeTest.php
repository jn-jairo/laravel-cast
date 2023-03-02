<?php

use JnJairo\Laravel\Cast\Cast;
use JnJairo\Laravel\Cast\Types\Base64Type;

$datasetSeparator = [
    'pipe' => '|',
    'comma' => ',',
];

$datasetConfig = (function () {
    $decoded = '';
    for ($i = 1; $i <= 255; $i++) {
        $decoded .= chr($i);
    }
    $encoded = base64_encode($decoded);
    $doubleEncoded = base64_encode($encoded);
    $tripleEncoded = base64_encode($doubleEncoded);

    $prefix = 'base64:';
    $prefixEncoded = $prefix . base64_encode($decoded);
    $prefixDoubleEncoded = $prefix . base64_encode($prefixEncoded);
    $prefixTripleEncoded = $prefix . base64_encode($prefixDoubleEncoded);

    return [
        'default_decoded' => ['', '', $decoded, $decoded, $encoded, $decoded],
        'default_encoded' => ['', '', $encoded, $decoded, $doubleEncoded, $decoded],

        'invalid_decoded' => ['*', '', $decoded, $decoded, $encoded, $decoded],
        'invalid_encoded' => ['*', '', $encoded, $decoded, $doubleEncoded, $decoded],

        'one_decoded' => ['one', '', $decoded, $decoded, $encoded, $decoded],
        'one_encoded' => ['one', '', $encoded, $decoded, $doubleEncoded, $decoded],
        'one_double_encoded' => ['one', '', $doubleEncoded, $encoded, $tripleEncoded, $encoded],

        'all_decoded' => ['all', '', $decoded, $decoded, $encoded, $decoded],
        'all_encoded' => ['all', '', $encoded, $decoded, $doubleEncoded, $decoded],
        'all_double_encoded' => ['all', '', $doubleEncoded, $encoded, $tripleEncoded, $encoded],

        'prefix_decoded' => ['', $prefix, $decoded, $decoded, $prefixEncoded, $decoded],
        'prefix_encoded' => ['', $prefix, $prefixEncoded, $decoded, $prefixDoubleEncoded, $decoded],
        'prefix_double_encoded' => [
            '',
            $prefix,
            $prefixDoubleEncoded,
            $prefixEncoded,
            $prefixTripleEncoded,
            $prefixEncoded,
        ],

        'one_prefix_decoded' => ['one', $prefix, $decoded, $decoded, $prefixEncoded, $decoded],
        'one_prefix_encoded' => ['one', $prefix, $prefixEncoded, $decoded, $prefixDoubleEncoded, $decoded],
        'one_prefix_double_encoded' => [
            'one',
            $prefix,
            $prefixDoubleEncoded,
            $prefixEncoded,
            $prefixTripleEncoded,
            $prefixEncoded,
        ],

        'all_prefix_decoded' => ['all', $prefix, $decoded, $decoded, $prefixEncoded, $decoded],
        'all_prefix_encoded' => ['all', $prefix, $prefixEncoded, $decoded, $prefixEncoded, $decoded],
        'all_prefix_double_encoded' => ['all', $prefix, $prefixDoubleEncoded, $decoded, $prefixEncoded, $decoded],
    ];
})();

$datasetFormat = (function () {
    $decoded = '';
    for ($i = 1; $i <= 255; $i++) {
        $decoded .= chr($i);
    }
    $encoded = base64_encode($decoded);
    $doubleEncoded = base64_encode($encoded);
    $tripleEncoded = base64_encode($doubleEncoded);

    $prefix = 'base64:';
    $prefixEncoded = $prefix . base64_encode($decoded);
    $prefixDoubleEncoded = $prefix . base64_encode($prefixEncoded);
    $prefixTripleEncoded = $prefix . base64_encode($prefixDoubleEncoded);

    return [
        'default_decoded' => ['', '', $decoded, $decoded, $encoded, $decoded],
        'default_encoded' => ['', '', $encoded, $decoded, $doubleEncoded, $decoded],

        'invalid_decoded' => [$prefix, '', $decoded, $decoded, $prefixEncoded, $decoded],
        'invalid_encoded' => [$prefix, '', $prefixEncoded, $decoded, $prefixDoubleEncoded, $decoded],
        'invalid_double_encoded' => [
            $prefix,
            '',
            $prefixDoubleEncoded,
            $prefixEncoded,
            $prefixTripleEncoded,
            $prefixEncoded,
        ],

        'one_decoded' => ['one', '', $decoded, $decoded, $encoded, $decoded],
        'one_encoded' => ['one', '', $encoded, $decoded, $doubleEncoded, $decoded],
        'one_double_encoded' => ['one', '', $doubleEncoded, $encoded, $tripleEncoded, $encoded],

        'all_decoded' => ['all', '', $decoded, $decoded, $encoded, $decoded],
        'all_encoded' => ['all', '', $encoded, $decoded, $doubleEncoded, $decoded],
        'all_double_encoded' => ['all', '', $doubleEncoded, $encoded, $tripleEncoded, $encoded],

        'prefix_decoded' => ['', $prefix, $decoded, $decoded, $prefixEncoded, $decoded],
        'prefix_encoded' => ['', $prefix, $prefixEncoded, $decoded, $prefixDoubleEncoded, $decoded],
        'prefix_double_encoded' => [
            '',
            $prefix,
            $prefixDoubleEncoded,
            $prefixEncoded,
            $prefixTripleEncoded,
            $prefixEncoded,
        ],

        'one_prefix_decoded' => ['one', $prefix, $decoded, $decoded, $prefixEncoded, $decoded],
        'one_prefix_encoded' => ['one', $prefix, $prefixEncoded, $decoded, $prefixDoubleEncoded, $decoded],
        'one_prefix_double_encoded' => [
            'one',
            $prefix,
            $prefixDoubleEncoded,
            $prefixEncoded,
            $prefixTripleEncoded,
            $prefixEncoded,
        ],

        'all_prefix_decoded' => ['all', $prefix, $decoded, $decoded, $prefixEncoded, $decoded],
        'all_prefix_encoded' => ['all', $prefix, $prefixEncoded, $decoded, $prefixEncoded, $decoded],
        'all_prefix_double_encoded' => ['all', $prefix, $prefixDoubleEncoded, $decoded, $prefixEncoded, $decoded],
    ];
})();

it('can cast', function () {
    $type = 'base64';

    $cast = new Cast([
        'types' => [
            $type => Base64Type::class,
        ],
    ]);

    $decoded = 'foo_bar';
    $encoded = base64_encode($decoded);

    expect($cast->cast($encoded, $type))
        ->toBe($decoded);
    expect($cast->castDb($decoded, $type))
        ->toBe($encoded);
    expect($cast->castJson($encoded, $type))
        ->toBe($decoded);

    expect($cast->cast(null, $type))
        ->toBeNull();
    expect($cast->castDb(null, $type))
        ->toBeNull();
    expect($cast->castJson(null, $type))
        ->toBeNull();
});

it('can be configured', function (
    string $decode,
    string $prefix,
    string $before,
    string $php,
    string $db,
    string $json,
) {
    $type = 'base64';

    $cast = new Cast([
        'types' => [
            $type => [
                'class' => Base64Type::class,
                'config' => [
                    'decode' => $decode,
                    'prefix' => $prefix,
                ],
            ],
        ],
    ]);

    expect($cast->cast($before, $type))
        ->toBe($php);
    expect($cast->castDb($before, $type))
        ->toBe($db);
    expect($cast->castJson($before, $type))
        ->toBe($json);

    expect($cast->cast(null, $type))
        ->toBeNull();
    expect($cast->castDb(null, $type))
        ->toBeNull();
    expect($cast->castJson(null, $type))
        ->toBeNull();
})->with($datasetConfig);

it('can be formatted', function (
    string $separator,
    string $decode,
    string $prefix,
    string $before,
    string $php,
    string $db,
    string $json,
) {
    $type = 'base64';
    $format = implode($separator, array_filter([
        $decode,
        $prefix,
    ], function ($config) {
        return $config !== '';
    }));

    $cast = new Cast([
        'types' => [
            $type => Base64Type::class,
        ],
    ]);

    expect($cast->cast($before, $type, $format))
        ->toBe($php);
    expect($cast->castDb($before, $type, $format))
        ->toBe($db);
    expect($cast->castJson($before, $type, $format))
        ->toBe($json);

    expect($cast->cast(null, $type, $format))
        ->toBeNull();
    expect($cast->castDb(null, $type, $format))
        ->toBeNull();
    expect($cast->castJson(null, $type, $format))
        ->toBeNull();
})->with($datasetSeparator)
  ->with($datasetFormat);
