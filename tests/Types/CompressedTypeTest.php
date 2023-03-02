<?php

use JnJairo\Laravel\Cast\Cast;
use JnJairo\Laravel\Cast\Types\CompressedType;

use function Safe\gzdeflate;

$datasetSeparator = [
    'pipe' => '|',
    'comma' => ',',
];

$dataset = (function () {
    $decompressed = 'Bar';
    $compressed = gzdeflate($decompressed, -1, ZLIB_ENCODING_RAW);
    $doubleCompressed = gzdeflate($compressed, -1, ZLIB_ENCODING_RAW);
    $tripleCompressed = gzdeflate($doubleCompressed, -1, ZLIB_ENCODING_RAW);

    $levels = (function () {
        $values = [];

        $decompressed = '';

        for ($i = 1; $i <= 255; $i++) {
            $decompressed .= chr($i);
        }

        $decompressed = str_repeat($decompressed, 100);

        for ($level = -1; $level <= 9; $level++) {
            $compressed = gzdeflate($decompressed, $level, ZLIB_ENCODING_RAW);
            $doubleCompressed = gzdeflate($compressed, $level, ZLIB_ENCODING_RAW);

            $values[$level] = [
                'decompressed' => $decompressed,
                'compressed' => $compressed,
                'doubleCompressed' => $doubleCompressed,
            ];
        }

        return $values;
    })();

    $encodings = (function () {
        $values = [];

        $decompressed = 'Bar';

        foreach (
            [
            'raw' => ZLIB_ENCODING_RAW,
            'deflate' => ZLIB_ENCODING_DEFLATE,
            'gzip' => ZLIB_ENCODING_GZIP,
            ] as $key => $encoding
        ) {
            $compressed = gzdeflate($decompressed, -1, $encoding);
            $doubleCompressed = gzdeflate($compressed, -1, $encoding);

            $values[$key] = [
            'decompressed' => $decompressed,
            'compressed' => $compressed,
            'doubleCompressed' => $doubleCompressed,
            ];
        }

        return $values;
    })();

    $cases = [
        'default_decompressed' => ['', '', '', '', $decompressed, $decompressed, $compressed, $decompressed],
        'default_compressed' => ['', '', '', '', $compressed, $decompressed, $doubleCompressed, $decompressed],

        'invalid_decompressed' => ['*', '*', '*', '*', $decompressed, $decompressed, $compressed, $decompressed],
        'invalid_compressed' => ['*', '*', '*', '*', $compressed, $decompressed, $doubleCompressed, $decompressed],

        'always_decompressed' => ['always', '', '', '', $decompressed, $decompressed, $compressed, $decompressed],
        'always_compressed' => ['always', '', '', '', $compressed, $decompressed, $doubleCompressed, $decompressed],

        'smaller_decompressed' => [
            'smaller',
            '',
            '',
            '',
            $decompressed,
            $decompressed,
            $decompressed,
            $decompressed,
        ],
        'smaller_compressed' => ['smaller', '', '', '', $compressed, $decompressed, $compressed, $decompressed],

        'one_decompressed' => ['', 'one', '', '', $decompressed, $decompressed, $compressed, $decompressed],
        'one_compressed' => ['', 'one', '', '', $compressed, $decompressed, $doubleCompressed, $decompressed],
        'one_double_compressed' => [
            '',
            'one',
            '',
            '',
            $doubleCompressed,
            $compressed,
            $tripleCompressed,
            $compressed,
        ],

        'all_decompressed' => ['', 'all', '', '', $decompressed, $decompressed, $compressed, $decompressed],
        'all_compressed' => ['', 'all', '', '', $compressed, $decompressed, $compressed, $decompressed],
        'all_double_compressed' => [
            '',
            'all',
            '',
            '',
            $doubleCompressed,
            $decompressed,
            $compressed,
            $decompressed,
        ],
        'all_triple_compressed' => [
            '',
            'all',
            '',
            '',
            $tripleCompressed,
            $decompressed,
            $compressed,
            $decompressed,
        ],

        'smaller_all_decompressed' => [
            'smaller',
            'all',
            '',
            '',
            $decompressed,
            $decompressed,
            $decompressed,
            $decompressed,
        ],
        'smaller_all_compressed' => [
            'smaller',
            'all',
            '',
            '',
            $compressed,
            $decompressed,
            $decompressed,
            $decompressed,
        ],
        'smaller_all_double_compressed' => [
            'smaller',
            'all',
            '',
            '',
            $doubleCompressed,
            $decompressed,
            $decompressed,
            $decompressed,
        ],
        'smaller_all_triple_compressed' => [
            'smaller',
            'all',
            '',
            '',
            $tripleCompressed,
            $decompressed,
            $decompressed,
            $decompressed,
        ],
    ];

    foreach ($levels as $level => $value) {
        $cases['level_' . $level . '_decompressed'] = [
            '',
            '',
            $level,
            '',
            $value['decompressed'],
            $value['decompressed'],
            $value['compressed'],
            $value['decompressed'],
        ];
        $cases['level_' . $level . '_compressed'] = [
            '',
            '',
            $level,
            '',
            $value['compressed'],
            $value['decompressed'],
            $value['doubleCompressed'],
            $value['decompressed'],
        ];
    }

    foreach ($encodings as $encoding => $value) {
        $cases['encoding_' . $encoding . '_decompressed'] = [
            '',
            '',
            '',
            $encoding,
            $value['decompressed'],
            $value['decompressed'],
            $value['compressed'],
            $value['decompressed'],
        ];
        $cases['encoding_' . $encoding . '_compressed'] = [
            '',
            '',
            '',
            $encoding,
            $value['compressed'],
            $value['decompressed'],
            $value['doubleCompressed'],
            $value['decompressed'],
        ];
    }

    return $cases;
})();

it('can cast', function () {
    $type = 'compressed';

    $cast = new Cast([
        'types' => [
            $type => CompressedType::class,
        ],
    ]);

    $decompressed = '';

    for ($i = 1; $i <= 255; $i++) {
        $decompressed .= chr($i);
    }

    $decompressed = str_repeat($decompressed, 100);
    $compressed = gzdeflate($decompressed, -1, ZLIB_ENCODING_RAW);
    $doubleCompressed = gzdeflate($compressed, -1, ZLIB_ENCODING_RAW);

    expect($cast->cast($decompressed, $type))
        ->toBe($decompressed);
    expect($cast->cast($compressed, $type))
        ->toBe($decompressed);
    expect($cast->cast($doubleCompressed, $type))
        ->toBe($compressed);

    expect($cast->castDb($decompressed, $type))
        ->toBe($compressed);
    expect($cast->castDb($compressed, $type))
        ->toBe($doubleCompressed);

    expect($cast->castJson($decompressed, $type))
        ->toBe($decompressed);
    expect($cast->castJson($compressed, $type))
        ->toBe($decompressed);
    expect($cast->castJson($doubleCompressed, $type))
        ->toBe($compressed);

    expect($cast->cast(null, $type))
        ->toBeNull();
    expect($cast->castDb(null, $type))
        ->toBeNull();
    expect($cast->castJson(null, $type))
        ->toBeNull();
});

it('can be configured', function (
    string $compress,
    string $decompress,
    int|string $level,
    string $encoding,
    string $before,
    string $php,
    string $db,
    string $json,
) {
    $type = 'compressed';

    $cast = new Cast([
        'types' => [
            $type => [
                'class' => CompressedType::class,
                'config' => [
                    'compress' => $compress,
                    'decompress' => $decompress,
                    'level' => $level,
                    'encoding' => $encoding,
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
})->with($dataset);

it('can be formatted', function (
    string $separator,
    string $compress,
    string $decompress,
    int|string $level,
    string $encoding,
    string $before,
    string $php,
    string $db,
    string $json,
) {
    $type = 'compressed';
    $format = implode($separator, array_filter([
        $compress,
        $decompress,
        $level,
        $encoding,
    ], function (mixed $config) {
        return $config !== '';
    }));

    $cast = new Cast([
        'types' => [
            $type => CompressedType::class,
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
  ->with($dataset);
