<?php

use JnJairo\Laravel\Cast\Cast;
use JnJairo\Laravel\Cast\Tests\Fixtures\Types\DummySpaceType;
use JnJairo\Laravel\Cast\Types\PipeType;

$datasetConfig = [
    'default' => ['', '', '', 'FooBar', 'Fo_-oB_-ar', 'Fo_o-_Ba_r', 'Fo_-oB_-ar'],
    'php:>,db:>,json:>' => ['>', '>', '>', 'FooBar', 'Fo_-oB_-ar', 'Fo_-oB_-ar', 'Fo_-oB_-ar'],
    'php:<,db:<,json:<' => ['<', '<', '<', 'FooBar', 'Fo_o-_Ba_r', 'Fo_o-_Ba_r', 'Fo_o-_Ba_r'],
];

$datasetFormat = [
    'default' => ['', 'FooBar', 'Fo_-oB_-ar', 'Fo_o-_Ba_r', 'Fo_-oB_-ar'],
    '>' => ['>', 'FooBar', 'Fo_-oB_-ar', 'Fo_-oB_-ar', 'Fo_-oB_-ar'],
    '<' => ['<', 'FooBar', 'Fo_o-_Ba_r', 'Fo_o-_Ba_r', 'Fo_o-_Ba_r'],
    'php:>,db:>,json:>' => ['php:>,db:>,json:>', 'FooBar', 'Fo_-oB_-ar', 'Fo_-oB_-ar', 'Fo_-oB_-ar'],
    'php:<,db:<,json:<' => ['php:<,db:<,json:<', 'FooBar', 'Fo_o-_Ba_r', 'Fo_o-_Ba_r', 'Fo_o-_Ba_r'],
];


it('can cast', function () {
    $type = 'pipe';
    $format = '|dummy:2:_|dummy:3:-|';

    $cast = new Cast([
        'types' => [
            $type => PipeType::class,
            'dummy' => DummySpaceType::class,
        ],
    ]);

    $before = 'FooBar';
    $json = $php = 'Fo_-oB_-ar';
    $db = 'Fo_o-_Ba_r';

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
});

it('can be configured', function (
    string $phpDirection,
    string $dbDirection,
    string $jsonDirection,
    string $before,
    string $php,
    string $db,
    string $json,
) {
    $type = 'pipe';
    $format = '|dummy:2:_|dummy:3:-|';

    $cast = new Cast([
        'types' => [
            $type => [
                'class' => PipeType::class,
                'config' => [
                    'php_direction' => $phpDirection,
                    'db_direction' => $dbDirection,
                    'json_direction' => $jsonDirection,
                ],
            ],
            'dummy' => DummySpaceType::class,
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
})->with($datasetConfig);

it('can be formatted', function (
    string $format,
    string $before,
    string $php,
    string $db,
    string $json,
) {
    $type = 'pipe';
    $format = '|dummy:2:_|dummy:3:-|' . $format;

    $cast = new Cast([
        'types' => [
            $type => PipeType::class,
            'dummy' => DummySpaceType::class,
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
})->with($datasetFormat);
