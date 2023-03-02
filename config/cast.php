<?php

return [
    'types' => [
        'string' => \JnJairo\Laravel\Cast\Types\StringType::class,
        'text' => \JnJairo\Laravel\Cast\Types\StringType::class,

        'int' => \JnJairo\Laravel\Cast\Types\IntegerType::class,
        'integer' => \JnJairo\Laravel\Cast\Types\IntegerType::class,

        'float' => \JnJairo\Laravel\Cast\Types\FloatType::class,
        'real' => \JnJairo\Laravel\Cast\Types\FloatType::class,
        'double' => \JnJairo\Laravel\Cast\Types\FloatType::class,

        // The decimal type uses the https://php-decimal.io extension,
        // to use this type run `composer require php-decimal/php-decimal:^1.1`
        // and install the decimal extension.
        'decimal' => \JnJairo\Laravel\Cast\Types\DecimalType::class,
        /*
        'decimal' => [
            'class' => \JnJairo\Laravel\Cast\Types\DecimalType::class,
            'config' => [
                'precision' => 28,
                'places' => 2,
                'round_mode' => 'half_up', // up|down|ceiling|floor|half_up|half_down|half_even|half_odd|truncate
            ],
        ],
         */

        'bool' => \JnJairo\Laravel\Cast\Types\BooleanType::class,
        'boolean' => \JnJairo\Laravel\Cast\Types\BooleanType::class,

        // datetime, date and timestamp accept the same configuration parameters
        'datetime' => \JnJairo\Laravel\Cast\Types\DateTimeType::class,
        /*
        'datetime' => [
            'class' => \JnJairo\Laravel\Cast\Types\DateTimeType::class,
            'config' => [
                'format' => 'Y-m-d H:i:s',
            ],
        ],
         */
        'date' => \JnJairo\Laravel\Cast\Types\DateType::class,
        /*
        'date' => [
            'class' => \JnJairo\Laravel\Cast\Types\DateType::class,
            'config' => [
                'format' => 'Y-m-d',
            ],
        ],
         */
        'timestamp' => \JnJairo\Laravel\Cast\Types\TimestampType::class,

        'json' => \JnJairo\Laravel\Cast\Types\JsonType::class,
        'array' => \JnJairo\Laravel\Cast\Types\ArrayType::class,
        'object' => \JnJairo\Laravel\Cast\Types\ObjectType::class,
        'collection' => \JnJairo\Laravel\Cast\Types\CollectionType::class,

        // To use ordered UUID type run `composer require moontoast/math:^1.1`
        'uuid' => \JnJairo\Laravel\Cast\Types\UuidType::class,
        /*
        'uuid' => [
            'class' => \JnJairo\Laravel\Cast\Types\UuidType::class,
            'config' => [
                'format' => 'uuid4', // uuid1|uuid4|ordered
            ],
        ],
         */

        'enum' => \JnJairo\Laravel\Cast\Types\EnumType::class,

        'pipe' => \JnJairo\Laravel\Cast\Types\PipeType::class,
        /*
        'pipe' => [
            'class' => \JnJairo\Laravel\Cast\Types\PipeType::class,
            'config' => [
                'php_direction' => '>', // >|<
                'db_direction' => '<', // >|<
                'json_direction' => '>', // >|<
            ],
        ],
         */

        'encrypted' => \JnJairo\Laravel\Cast\Types\EncryptedType::class,
        /*
        'encrypted' => [
            'class' => \JnJairo\Laravel\Cast\Types\CompressedType::class,
            'config' => [
                'decrypt' => 'one', // one|all
                'key' => '', // base64:<key in base 64>
                'cipher' => '', // aes-128-cbc|aes-256-cbc|aes-128-gcm|aes-256-gcm
            ],
        ],
         */

        'compressed' => \JnJairo\Laravel\Cast\Types\CompressedType::class,
        /*
        'compressed' => [
            'class' => \JnJairo\Laravel\Cast\Types\CompressedType::class,
            'config' => [
                'compress' => 'always', // always|smaller
                'decompress' => 'one', // one|all
                'level' => -1, // -1 >= level >= 9
                'encoding' => 'raw', // raw|deflate|gzip
            ],
        ],
         */

        'base64' => \JnJairo\Laravel\Cast\Types\Base64Type::class,
        /*
        'base64' => [
            'class' => \JnJairo\Laravel\Cast\Types\Base64Type::class,
            'config' => [
                'decode' => 'one', // one|all
                'prefix' => '',
            ],
        ],
         */
    ],
];
