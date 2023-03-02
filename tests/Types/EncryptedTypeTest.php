<?php

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Crypt;
use JnJairo\Laravel\Cast\Cast;
use JnJairo\Laravel\Cast\Types\EncryptedType;

$defaultKey = 'base64:rTMVgIlCIRoOnAtGwI2sHCwwRaI2kwIWPnhSjgOhb14=';
$defaultCipher = 'AES-256-CBC';

beforeEach(function () use ($defaultKey, $defaultCipher) {
    config([
        'app.key' => $defaultKey,
        'app.cipher' => $defaultCipher,
    ]);
});

$datasetSeparator = [
    'pipe' => '|',
    'comma' => ',',
];

$dataset = (function () use ($defaultKey, $defaultCipher) {
    $encrypter = new Encrypter(base64_decode(explode(':', $defaultKey)[1]), $defaultCipher);

    $decrypted = 'FooBar';
    $encrypted = $encrypter->encryptString($decrypted);
    $doubleEncrypted = $encrypter->encryptString($encrypted);
    $tripleEncrypted = $encrypter->encryptString($doubleEncrypted);

    $decrypt = function (int $times = 1, Encrypter $defaultEncrypter = null) use ($encrypter) {
        $e = $defaultEncrypter ?? $encrypter;

        return function (string $value) use ($times, $e) {
            for ($i = 0; $i < $times; $i++) {
                try {
                    $value = $e->decryptString($value);
                } catch (DecryptException $exception) {
                    $value = '';
                }
            }

            return $value;
        };
    };

    $ciphers = (function () {
        $values = [];

        $decrypted = 'FooBar';

        $ciphers = [
            'aes-128-cbc' => 'base64:s1kweF68LZ8LgQK0ewmH0w==',
            'aes-256-cbc' => 'base64:N38XBrdzg505959nDEqefo6fNpeTmGy0wTBHRSxrpcQ=',
            'aes-128-gcm' => 'base64:Hkw0py0tBwFVPHoJBmmveg==',
            'aes-256-gcm' => 'base64:O3u1u5RrERRPLP5sgqeWkhx3f7qbIJijO0o4Mf2Tpe0=',
        ];

        foreach ($ciphers as $cipher => $key) {
            $encrypter = new Encrypter(base64_decode(explode(':', $key)[1]), $cipher);

            $encrypted = $encrypter->encryptString($decrypted);
            $doubleEncrypted = $encrypter->encryptString($encrypted);

            $values[$cipher] = [
                'encrypter' => $encrypter,
                'key' => $key,
                'cipher' => $cipher,
                'decrypted' => $decrypted,
                'encrypted' => $encrypted,
                'doubleEncrypted' => $doubleEncrypted,
            ];
        }

        return $values;
    })();

    $cases = [
        'default_decrypted' => ['', '', '', $decrypted, $decrypted, [$decrypted, $decrypt()], $decrypted],
        'default_encrypted' => ['', '', '', $encrypted, $decrypted, [$decrypted, $decrypt(2)], $decrypted],

        'invalid_decrypted' => ['*', '*', '*', $decrypted, $decrypted, [$decrypted, $decrypt()], $decrypted],
        'invalid_encrypted' => ['*', '*', '*', $encrypted, $decrypted, [$decrypted, $decrypt(2)], $decrypted],

        'one_decrypted' => ['one', '', '', $decrypted, $decrypted, [$decrypted, $decrypt()], $decrypted],
        'one_encrypted' => ['one', '', '', $encrypted, $decrypted, [$decrypted, $decrypt(2)], $decrypted],
        'one_double_encrypted' => [
            'one',
            '',
            '',
            $doubleEncrypted,
            [$decrypted, $decrypt()],
            [$decrypted, $decrypt(3)],
            [$decrypted, $decrypt()],
        ],
        'one_triple_encrypted' => [
            'one',
            '',
            '',
            $tripleEncrypted,
            [$decrypted, $decrypt(2)],
            [$decrypted, $decrypt(4)],
            [$decrypted, $decrypt(2)],
        ],

        'all_decrypted' => ['all', '', '', $decrypted, $decrypted, [$decrypted, $decrypt()], $decrypted],
        'all_encrypted' => ['all', '', '', $encrypted, $decrypted, [$decrypted, $decrypt()], $decrypted],
        'all_double_encrypted' => [
            'all',
            '',
            '',
            $doubleEncrypted,
            $decrypted,
            [$decrypted, $decrypt()],
            $decrypted,
        ],
        'all_triple_encrypted' => [
            'all',
            '',
            '',
            $tripleEncrypted,
            $decrypted,
            [$decrypted, $decrypt()],
            $decrypted,
        ],
    ];

    foreach ($ciphers as $cipher => $value) {
        $cases['cipher_' . $cipher . '_decrypted'] = [
            '',
            $value['key'],
            $value['cipher'],
            $value['decrypted'],
            $value['decrypted'],
            [$value['decrypted'], $decrypt(1, $value['encrypter'])],
            $value['decrypted'],
        ];
        $cases['cipher_' . $cipher . '_encrypted'] = [
            '',
            $value['key'],
            $value['cipher'],
            $value['encrypted'],
            $value['decrypted'],
            [$value['decrypted'], $decrypt(2, $value['encrypter'])],
            $value['decrypted'],
        ];
    }

    return $cases;
})();

it('can cast', function () {
    $type = 'encrypted';

    $cast = new Cast([
        'types' => [
            $type => EncryptedType::class,
        ],
    ]);

    $decrypted = 'FooBar';
    $encrypted = Crypt::encryptString($decrypted);
    $doubleEncrypted = Crypt::encryptString($encrypted);

    expect($cast->cast($encrypted, $type))
        ->toBe($decrypted);
    expect($cast->cast($doubleEncrypted, $type))
        ->toBe($encrypted);

    /**
     * @var string $value
     */
    $value = $cast->castDb($encrypted, $type);
    expect(Crypt::decryptString(Crypt::decryptString($value)))
        ->toBe($decrypted);
    /**
     * @var string $value
     */
    $value = $cast->castDb($decrypted, $type);
    expect(Crypt::decryptString($value))
        ->toBe($decrypted);

    expect($cast->castJson($encrypted, $type))
        ->toBe($decrypted);
    expect($cast->castJson($doubleEncrypted, $type))
        ->toBe($encrypted);

    expect($cast->cast(null, $type))
        ->toBeNull();
    expect($cast->castDb(null, $type))
        ->toBeNull();
    expect($cast->castJson(null, $type))
        ->toBeNull();
});

it('can be configured', function (
    string $decrypt,
    string $key,
    string $cipher,
    string $before,
    mixed $php,
    mixed $db,
    mixed $json,
) {
    $type = 'encrypted';

    $cast = new Cast([
        'types' => [
            $type => [
                'class' => EncryptedType::class,
                'config' => [
                    'decrypt' => $decrypt,
                    'key' => $key,
                    'cipher' => $cipher,
                ],
            ],
        ],
    ]);

    $expectToBe = function (mixed $actual, mixed $expected) {
        if (is_array($expected) && is_string($actual)) {
            $actual = $expected[1]($actual);
            $expected = $expected[0];
        }

        expect($actual)
            ->toBe($expected);
    };

    $expectToBe($cast->cast($before, $type), $php);
    $expectToBe($cast->castDb($before, $type), $db);
    $expectToBe($cast->castJson($before, $type), $json);

    expect($cast->cast(null, $type))
        ->toBeNull();
    expect($cast->castDb(null, $type))
        ->toBeNull();
    expect($cast->castJson(null, $type))
        ->toBeNull();
})->with($dataset);

it('can be formatted', function (
    string $separator,
    string $decrypt,
    string $key,
    string $cipher,
    string $before,
    mixed $php,
    mixed $db,
    mixed $json,
) {
    $type = 'encrypted';
    $format = implode($separator, array_filter([
        $decrypt,
        $key,
        $cipher,
    ], function (mixed $config) {
        return $config !== '';
    }));

    $cast = new Cast([
        'types' => [
            $type => EncryptedType::class,
        ],
    ]);

    $expectToBe = function (mixed $actual, mixed $expected) {
        if (is_array($expected) && is_string($actual)) {
            $actual = $expected[1]($actual);
            $expected = $expected[0];
        }

        expect($actual)
            ->toBe($expected);
    };

    $expectToBe($cast->cast($before, $type, $format), $php);
    $expectToBe($cast->castDb($before, $type, $format), $db);
    $expectToBe($cast->castJson($before, $type, $format), $json);

    expect($cast->cast(null, $type, $format))
        ->toBeNull();
    expect($cast->castDb(null, $type, $format))
        ->toBeNull();
    expect($cast->castJson(null, $type, $format))
        ->toBeNull();
})->with($datasetSeparator)
  ->with($dataset);
