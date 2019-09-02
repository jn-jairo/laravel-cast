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
                // Example using format parameter:
                // Cast::cast($value, 'decimal', '10:2|half_up');
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
                // Example using format parameter:
                // Cast::cast($value, 'datetime', 'Y-m-d H:i:s');
                'format' => 'Y-m-d H:i:s',
            ],
        ],
         */
        'date' => \JnJairo\Laravel\Cast\Types\DateType::class,
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
                // Example using format parameter:
                // Cast::cast($value, 'uuid', 'uuid1');
                'format' => 'uuid4', // uuid1|uuid4|ordered
            ],
        ],
         */
    ],
];
