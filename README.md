[![Build Status](https://circleci.com/gh/jn-jairo/laravel-cast.svg?style=shield)](https://circleci.com/gh/jn-jairo/laravel-cast)
[![Total Downloads](https://poser.pugx.org/jn-jairo/laravel-cast/downloads)](https://packagist.org/packages/jn-jairo/laravel-cast)
[![Latest Stable Version](https://poser.pugx.org/jn-jairo/laravel-cast/v/stable)](https://packagist.org/packages/jn-jairo/laravel-cast)
[![License](https://poser.pugx.org/jn-jairo/laravel-cast/license)](https://packagist.org/packages/jn-jairo/laravel-cast)

# Cast for Laravel

This package provide cast for Laravel.

## Requirements

- Laravel Framework >= 5.8

## Installation

You can install the package via composer:

```bash
composer require jn-jairo/laravel-cast
```

The `CastServiceProvider` will be automatically registered for you.

## Usage

Both [contract](https://laravel.com/docs/contracts) `\JnJairo\Laravel\Cast\Contracts\Cast` and [facade](https://laravel.com/docs/facades) `\JnJairo\Laravel\Cast\Facades\Cast` are available.

There are three methods `Cast::cast()`, `Cast::castDb()` and `Cast::castJson()`.

- `Cast::cast()` casts to `PHP` types
- `Cast::castDb()` casts to `database` types
- `Cast::castJson()` casts to `json` types

All methods accept three parameters `mixed $value`, `string $type` and optionally `string $format`.

## Examples

```php
print_r(Cast::cast('{"foo":"bar"}', 'array'));
/*
Array
(
    [foo] => bar
)
*/

print_r(Cast::castDb(1234.555, 'decimal', '10:2'));
// 1234.56

print_r(Cast::castDb(['foo' => 'bar'], 'json'));
// {"foo":"bar"}

print_r(Cast::castJson(new DateTime('01 jan 2000'), 'date'));
// 2000-01-01
```

## Types available

- `int`, `integer`
- `float`, `real`, `double`
- `decimal`
- `bool`, `boolean`
- `date`
- `datetime`
- `timestamp`
- `json`
- `array`
- `object`
- `collection`
- `string`, `text`
- `uuid`
- `encrypted`

## Format parameter

- **decimal** - `precision:places|(up|down|ceiling|floor|half_up|half_down|half_even|half_odd|truncate)`. Example: `10:2|half_up`, `10:2`, `2`, `half_up`. Default: `28:2|half_up`.
The decimal type uses the https://php-decimal.io extension, to use this type run `composer require php-decimal/php-decimal:^1.1` and install the decimal extension.
- **date** - Example: `Y-m-d`. Default: `Y-m-d`.
- **datetime**, **timestamp** - Example: `Y-m-d H:i:s`. Default: `Y-m-d H:i:s`.
- **uuid** - `(uuid1|uuid4|ordered)`. Example: `uuid1`. Default: `uuid4`.
Empty string value will return a new UUID.
To use ordered UUID format run `composer require moontoast/math:^1.1`.
- **encrypted** - `type:format`. Example: `date:Y-m-d`. Default: ` `.
```php
$decrypted = Cast::cast($value, 'encrypted');
$encrypted = Cast::castDb($value, 'encrypted');
$decrypted = Cast::castJson($value, 'encrypted');
```

## Custom types

To create a custom type just implements the contract `\JnJairo\Laravel\Cast\Contracts\Type`.

```php
class CustomType implements \JnJairo\Laravel\Cast\Contracts\Type
{
    // ...
}
```

Publish the configuration to `config/cast.php`.

```bash
php artisan vendor:publish --provider=JnJairo\\Laravel\\Cast\\CastServiceProvider
```

Set the new type in the configuration.

```php
// config/cast.php

return [
    'types' => [
        'custom_type' => CustomType::class,
    ],
];
```

And the custom type will be available.

```php
Cast::cast('foo', 'custom_type');
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
