[![Total Downloads](https://poser.pugx.org/jn-jairo/laravel-cast/downloads)](https://packagist.org/packages/jn-jairo/laravel-cast)
[![Latest Stable Version](https://poser.pugx.org/jn-jairo/laravel-cast/v/stable)](https://packagist.org/packages/jn-jairo/laravel-cast)
[![License](https://poser.pugx.org/jn-jairo/laravel-cast/license)](https://packagist.org/packages/jn-jairo/laravel-cast)

# Cast for Laravel

This package provide cast for Laravel.

## Version Compatibility

 Laravel  | Eloquent Cast
:---------|:----------
  5.8.x   | 1.x
  6.x     | 1.x
  7.x     | 1.x
  8.x     | 2.x
  9.x     | 2.x
 10.x     | 2.x
 11.x     | 2.x
 12.x     | 2.x

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
- `compressed`
- `base64`
- `pipe`
- `enum`

## Format parameter

- **decimal** - `precision:places,(up|down|ceiling|floor|half_up|half_down|half_even|half_odd|truncate)`. Example: `10:2,half_up`, `10:2`, `2`, `half_up`. Default: `28:2,half_up`.
The decimal type uses the https://php-decimal.io extension, to use this type run `composer require php-decimal/php-decimal:^1.1` and install the decimal extension.
- **date** - Example: `Y-m-d`. Default: `Y-m-d`.
- **datetime**, **timestamp** - Example: `Y-m-d H:i:s`. Default: `Y-m-d H:i:s`.
- **uuid** - `(uuid1|uuid4|ordered)`. Example: `uuid1`. Default: `uuid4`.
Empty string value will return a new UUID.
To use ordered UUID format run `composer require moontoast/math:^1.1`.
- **encrypted** - `(one|all),base64:key,cipher`. Empty key and cipher uses the laravel configuration `app.key` and `app.cipher`. Example: `base64:N38XBrdzg505959nDEqefo6fNpeTmGy0wTBHRSxrpcQ=,aes-256-cbc`. Default: `one`.
    - `one` - allow double encryption, decrypts only one time.
    - `all` - prevents double encryption, decrypts recursively all encryptions.
```php
$decrypted = Cast::cast($encrypted, 'encrypted');
$encrypted = Cast::castDb($decrypted, 'encrypted');
$decrypted = Cast::castJson($encrypted, 'encrypted');
```
- **compressed** - `(always|smaller),(one|all),level,(raw|deflate|gzip)`. Example: `smaller,all,9,gzip`. Default: `always,one,-1,raw`.
    - `always` - always uses the result of compression even if the result is bigger than the original value.
    - `smaller` - only uses the result of compression if the result is smaller than the original value.
    - `one` - allow double compression, decompresses only one time.
    - `all` - prevents double compression, decompresses recursively all compressions.
    - level - level of compression, from `0` (no compression) to `9` (maximum compression). If `-1` is used, the default compression of the zlib library is used.
    - `raw` - uses the encoding `ZLIB_ENCODING_RAW` with `gzdeflate` and `gzinflate`.
    - `deflate` - uses the encoding `ZLIB_ENCODING_DEFLATE` with `gzcompress` and `gzuncompress`.
    - `gzip` - uses the encoding `ZLIB_ENCODING_GZIP` with `gzencode` and `gzdecode`.
```php
$decompressed = Cast::cast($compressed, 'compressed');
$compressed = Cast::castDb($decompressed, 'compressed');
$decompressed = Cast::castJson($compressed, 'compressed');
```
- **base64** - `(one|all),prefix`. Example: `base64:`. Default: `one`.
    - `one` - allow double encoding, decodes only one time.
    - `all` - prevents double encoding, decodes recursively all encodings (requires prefix).
```php
$decoded = Cast::cast('base64:Rm9vQmFy', 'base64', 'base64:'); // FooBar
$encoded = Cast::castDb('FooBar', 'base64', 'base64:'); // base64:Rm9vQmFy
$decoded = Cast::castJson('base64:Rm9vQmFy', 'base64', 'base64:'); // FooBar
```
- **pipe** - `|type:format|type:format|...|direction`. Example: `|encrypted|compressed|array|`. Default: `||php:>,db:<,json:>`.
    - The direction format is `>|<|php:(>|<),db:(>|<),json:(>|<)`, `>` follows from left to right and `<` follows from right to left.
    - The first character is used as the type separator, for the other separators in case of conflict with the type separator the `|` separator is used.
    - Examples of valid parameters:<br/>
`|encrypted|array|`<br/>
`|encrypted|array|<`<br/>
`|encrypted|array|>,db:<`<br/>
`|encrypted|decimal:2|php:>,db:<,json:>`<br/>
`,encrypted,decimal:2,php:>|db:<|json:>`<br/>
`:encrypted:decimal|2:php|>,db|<,json|>`
```php
$array = Cast::cast('q1ZKy89XslJKSixSqgUA', 'pipe', '|base64|compressed|array|'); // ['foo' => 'bar']
$base64Compressed = Cast::castDb(['foo' => 'bar'], 'pipe', '|base64|compressed|array|'); // q1ZKy89XslJKSixSqgUA
$array = Cast::castJson('q1ZKy89XslJKSixSqgUA', 'pipe', '|base64|compressed|array|'); // ['foo' => 'bar']
```
- **enum** - Example: `MyEnum::class`. Default: ` `.
```php
enum MyEnum : int
{
    case foo = 1;
    case bar = 2;
}

Cast::cast(1, 'enum', MyEnum::class); // MyEnum::foo
Cast::castDb(MyEnum::foo, 'enum', MyEnum::class); // 1
Cast::castJson(MyEnum::foo, 'enum', MyEnum::class); // 1
```
It can be a instance of `\Illuminate\Contracts\Support\Arrayable` or `\Illuminate\Contracts\Support\Jsonable`.
```php
use Illuminate\Contracts\Support\Arrayable;

enum MyEnum : string implements Arrayable
{
    case foo = 1;
    case bar = 2;

    public function description() : string
    {
        return match ($this) {
            self::foo => 'foo description',
            self::bar => 'bar description',
        };
    }

    public function toArray()
    {
        return [
            'name' => $this->name,
            'value' => $this->value,
            'description' => $this->description(),
        ];
    }
}

Cast::cast(1, 'enum', MyEnum::class); // MyEnum::foo
Cast::castDb(MyEnum::foo, 'enum', MyEnum::class); // 1
Cast::castJson(MyEnum::foo, 'enum', MyEnum::class);
// [
//      'name' => 'foo',
//      'value' => 1,
//      'description' => 'foo description'
// ]
```

## Custom types

To create a custom type just implements the contract `\JnJairo\Laravel\Cast\Contracts\Type`.

```php
class CustomType implements \JnJairo\Laravel\Cast\Contracts\Type
{
    // ...
}
```

Or extends the `\JnJairo\Laravel\Cast\Types\Type` abstract class.

```php
class CustomType extends \JnJairo\Laravel\Cast\Types\Type
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
