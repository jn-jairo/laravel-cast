<?php

namespace JnJairo\Laravel\Cast\Tests;

use DateTime;
use Decimal\Decimal;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use JnJairo\Laravel\Cast\Cast;
use JnJairo\Laravel\Cast\Contracts\Type as TypeContract;
use JnJairo\Laravel\Cast\Exceptions\InvalidTypeException;
use JnJairo\Laravel\Cast\Tests\Fixtures\DummyArrayable;
use JnJairo\Laravel\Cast\Tests\Fixtures\DummyJsonable;
use JnJairo\Laravel\Cast\Tests\Fixtures\Types\DummyType;
use JnJairo\Laravel\Cast\Tests\OrchestraTestCase as TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

/**
 * @testdox Cast
 */
class CastTest extends TestCase
{
    public function test_invalid_type_not_found() : void
    {
        $cast = new Cast();

        $this->expectException(InvalidTypeException::class);
        $this->expectExceptionMessage('[bar]: Type not found in the configuration.');
        $cast->cast('foo', 'bar');
    }

    public function test_invalid_type_missing_class() : void
    {
        $cast = new Cast([
            'types' => [
                'bar' => [],
            ],
        ]);

        $this->expectException(InvalidTypeException::class);
        $this->expectExceptionMessage('[bar]: Missing class in the configuration.');
        $cast->cast('foo', 'bar');
    }

    public function test_invalid_type_class_not_exists() : void
    {
        $cast = new Cast([
            'types' => [
                'bar' => 'Bar',
            ],
        ]);

        $this->expectException(InvalidTypeException::class);
        $this->expectExceptionMessage('[bar]: Class "Bar" does not exists.');
        $cast->cast('foo', 'bar');
    }

    public function test_invalid_type_class_not_implements_interface() : void
    {
        $cast = new Cast([
            'types' => [
                'bar' => stdClass::class,
            ],
        ]);

        $this->expectException(InvalidTypeException::class);
        $this->expectExceptionMessage('[bar]: Class "' . stdClass::class . '" does not implements the interface "'
            . TypeContract::class . '".');
        $cast->cast('foo', 'bar');
    }

    public function test_abstract_type() : void
    {
        $dummyType = new DummyType;

        $this->assertSame('123', $dummyType->cast('123'), 'PHP');
        $this->assertSame('123', $dummyType->castDb('123'), 'Database');
        $this->assertSame('123', $dummyType->castJson('123'), 'Json');

        $this->assertNull($dummyType->cast(null), 'PHP null');
        $this->assertNull($dummyType->castDb(null), 'Database null');
        $this->assertNull($dummyType->castJson(null), 'Json null');
    }

    public function test_config_types_class() : void
    {
        $type = 'dummy';

        $cast = new Cast([
            'types' => [
                $type => DummyType::class,
            ],
        ]);

        $this->assertSame('123', $cast->cast('123', $type), 'PHP');
        $this->assertSame('123', $cast->castDb('123', $type), 'Database');
        $this->assertSame('123', $cast->castJson('123', $type), 'Json');

        $this->assertNull($cast->cast(null, $type), 'PHP null');
        $this->assertNull($cast->castDb(null, $type), 'Database null');
        $this->assertNull($cast->castJson(null, $type), 'Json null');
    }

    public function test_config_types_config() : void
    {
        $type = 'dummy';

        $dummy = new DummyType;

        app()->instance(DummyType::class, $dummy);

        $cast = new Cast([
            'types' => [
                $type => [
                    'class' => DummyType::class,
                    'config' => [
                        'foo' => 'bar',
                    ],
                ],
            ],
        ]);

        $this->assertSame('123', $cast->cast('123', $type), 'PHP');
        $this->assertSame('123', $cast->castDb('123', $type), 'Database');
        $this->assertSame('123', $cast->castJson('123', $type), 'Json');

        $this->assertNull($cast->cast(null, $type), 'PHP null');
        $this->assertNull($cast->castDb(null, $type), 'Database null');
        $this->assertNull($cast->castJson(null, $type), 'Json null');

        $this->assertSame(['foo' => 'bar'], $dummy->getConfig(), 'Configuration');
    }

    public function test_integer() : void
    {
        $type = 'integer';

        $cast = new Cast([
            'types' => [
                $type => \JnJairo\Laravel\Cast\Types\IntegerType::class,
            ],
        ]);

        $this->assertSame(123, $cast->cast('123', $type), 'PHP');
        $this->assertSame(123, $cast->castDb('123', $type), 'Database');
        $this->assertSame(123, $cast->castJson('123', $type), 'Json');

        $this->assertNull($cast->cast(null, $type), 'PHP null');
        $this->assertNull($cast->castDb(null, $type), 'Database null');
        $this->assertNull($cast->castJson(null, $type), 'Json null');
    }

    public function test_float() : void
    {
        $type = 'float';

        $cast = new Cast([
            'types' => [
                $type => \JnJairo\Laravel\Cast\Types\FloatType::class,
            ],
        ]);

        $this->assertSame(1.23, $cast->cast('1.23', $type), 'PHP');
        $this->assertSame(1.23, $cast->castDb('1.23', $type), 'Database');
        $this->assertSame(1.23, $cast->castJson('1.23', $type), 'Json');

        $this->assertNull($cast->cast(null, $type), 'PHP null');
        $this->assertNull($cast->castDb(null, $type), 'Database null');
        $this->assertNull($cast->castJson(null, $type), 'Json null');
    }

    public function test_boolean() : void
    {
        $type = 'bool';

        $cast = new Cast([
            'types' => [
                $type => \JnJairo\Laravel\Cast\Types\BooleanType::class,
            ],
        ]);

        $this->assertSame(true, $cast->cast(1, $type), 'PHP');
        $this->assertSame(false, $cast->castDb('0', $type), 'Database');
        $this->assertSame(true, $cast->castJson('1', $type), 'Json');

        $this->assertNull($cast->cast(null, $type), 'PHP null');
        $this->assertNull($cast->castDb(null, $type), 'Database null');
        $this->assertNull($cast->castJson(null, $type), 'Json null');
    }

    public function test_string() : void
    {
        $type = 'string';

        $cast = new Cast([
            'types' => [
                $type => \JnJairo\Laravel\Cast\Types\StringType::class,
            ],
        ]);

        $this->assertSame('1.23', $cast->cast(1.23, $type), 'PHP');
        $this->assertSame('1.23', $cast->castDb(1.23, $type), 'Database');
        $this->assertSame('1.23', $cast->castJson(1.23, $type), 'Json');

        $this->assertNull($cast->cast(null, $type), 'PHP null');
        $this->assertNull($cast->castDb(null, $type), 'Database null');
        $this->assertNull($cast->castJson(null, $type), 'Json null');
    }

    public function test_datetime() : void
    {
        $type = 'datetime';

        $cast = new Cast([
            'types' => [
                $type => \JnJairo\Laravel\Cast\Types\DateTimeType::class,
            ],
        ]);

        $now = Carbon::now();
        $date = $now->format('Y-m-d H:i:s');
        $carbon = Carbon::createFromFormat('Y-m-d H:i:s', $date);
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $date);
        $timestamp = $carbon->getTimestamp();

        $this->assertInstanceOf(Carbon::class, $cast->cast($now, $type), 'Carbon instance from PHP');
        $this->assertInstanceOf(Carbon::class, $cast->cast($date, $type), 'Carbon instance from string');
        $this->assertInstanceOf(Carbon::class, $cast->cast($carbon, $type), 'Carbon instance from Carbon');
        $this->assertInstanceOf(Carbon::class, $cast->cast($dateTime, $type), 'Carbon instance from DateTime');
        $this->assertInstanceOf(Carbon::class, $cast->cast($timestamp, $type), 'Carbon instance from timestamp');

        $this->assertEquals($carbon, $cast->cast($now, $type), 'PHP from PHP');
        $this->assertEquals($carbon, $cast->cast($date, $type), 'PHP from string');
        $this->assertEquals($carbon, $cast->cast($carbon, $type), 'PHP from Carbon');
        $this->assertEquals($carbon, $cast->cast($dateTime, $type), 'PHP from DateTime');
        $this->assertEquals($carbon, $cast->cast($timestamp, $type), 'PHP from timestamp');

        $this->assertSame($date, $cast->castDb($date, $type), 'Database from string');
        $this->assertSame($date, $cast->castDb($carbon, $type), 'Database from Carbon');
        $this->assertSame($date, $cast->castDb($dateTime, $type), 'Database from DateTime');
        $this->assertSame($date, $cast->castDb($timestamp, $type), 'Database from timestamp');

        $this->assertSame($date, $cast->castJson($date, $type), 'Json from string');
        $this->assertSame($date, $cast->castJson($carbon, $type), 'Json from Carbon');
        $this->assertSame($date, $cast->castJson($dateTime, $type), 'Json from DateTime');
        $this->assertSame($date, $cast->castJson($timestamp, $type), 'Json from timestamp');

        $this->assertNull($cast->cast(null, $type), 'PHP null');
        $this->assertNull($cast->castDb(null, $type), 'Database null');
        $this->assertNull($cast->castJson(null, $type), 'Json null');
    }

    public function test_datetime_config() : void
    {
        $type = 'datetime';

        $cast = new Cast([
            'types' => [
                $type => [
                    'class' => \JnJairo\Laravel\Cast\Types\DateTimeType::class,
                    'config' => [
                        'format' => 'Y-m-d'
                    ],
                ],
            ],
        ]);

        $now = Carbon::now();
        $date = $now->format('Y-m-d');
        $carbon = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
        $dateTime = DateTime::createFromFormat('Y-m-d', $date);
        $timestamp = $carbon->getTimestamp();

        $this->assertInstanceOf(Carbon::class, $cast->cast($now, $type), 'Carbon instance from PHP');
        $this->assertInstanceOf(Carbon::class, $cast->cast($date, $type), 'Carbon instance from string');
        $this->assertInstanceOf(Carbon::class, $cast->cast($carbon, $type), 'Carbon instance from Carbon');
        $this->assertInstanceOf(Carbon::class, $cast->cast($dateTime, $type), 'Carbon instance from DateTime');
        $this->assertInstanceOf(Carbon::class, $cast->cast($timestamp, $type), 'Carbon instance from timestamp');

        $this->assertEquals($carbon, $cast->cast($now, $type), 'PHP from PHP');
        $this->assertEquals($carbon, $cast->cast($date, $type), 'PHP from string');
        $this->assertEquals($carbon, $cast->cast($carbon, $type), 'PHP from Carbon');
        $this->assertEquals($carbon, $cast->cast($dateTime, $type), 'PHP from DateTime');
        $this->assertEquals($carbon, $cast->cast($timestamp, $type), 'PHP from timestamp');

        $this->assertSame($date, $cast->castDb($date, $type), 'Database from string');
        $this->assertSame($date, $cast->castDb($carbon, $type), 'Database from Carbon');
        $this->assertSame($date, $cast->castDb($dateTime, $type), 'Database from DateTime');
        $this->assertSame($date, $cast->castDb($timestamp, $type), 'Database from timestamp');

        $this->assertSame($date, $cast->castJson($date, $type), 'Json from string');
        $this->assertSame($date, $cast->castJson($carbon, $type), 'Json from Carbon');
        $this->assertSame($date, $cast->castJson($dateTime, $type), 'Json from DateTime');
        $this->assertSame($date, $cast->castJson($timestamp, $type), 'Json from timestamp');

        $this->assertNull($cast->cast(null, $type), 'PHP null');
        $this->assertNull($cast->castDb(null, $type), 'Database null');
        $this->assertNull($cast->castJson(null, $type), 'Json null');
    }

    public function test_date() : void
    {
        $type = 'date';

        $cast = new Cast([
            'types' => [
                $type => \JnJairo\Laravel\Cast\Types\DateType::class,
            ],
        ]);

        $now = Carbon::now();
        $date = $now->format('Y-m-d');
        $carbon = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
        $dateTime = DateTime::createFromFormat('Y-m-d', $date);
        $timestamp = $carbon->getTimestamp();

        $this->assertInstanceOf(Carbon::class, $cast->cast($now, $type), 'Carbon instance from PHP');
        $this->assertInstanceOf(Carbon::class, $cast->cast($date, $type), 'Carbon instance from string');
        $this->assertInstanceOf(Carbon::class, $cast->cast($carbon, $type), 'Carbon instance from Carbon');
        $this->assertInstanceOf(Carbon::class, $cast->cast($dateTime, $type), 'Carbon instance from DateTime');
        $this->assertInstanceOf(Carbon::class, $cast->cast($timestamp, $type), 'Carbon instance from timestamp');

        $this->assertEquals($carbon, $cast->cast($now, $type), 'PHP from PHP');
        $this->assertEquals($carbon, $cast->cast($date, $type), 'PHP from string');
        $this->assertEquals($carbon, $cast->cast($carbon, $type), 'PHP from Carbon');
        $this->assertEquals($carbon, $cast->cast($dateTime, $type), 'PHP from DateTime');
        $this->assertEquals($carbon, $cast->cast($timestamp, $type), 'PHP from timestamp');

        $this->assertSame($date, $cast->castDb($date, $type), 'Database from string');
        $this->assertSame($date, $cast->castDb($carbon, $type), 'Database from Carbon');
        $this->assertSame($date, $cast->castDb($dateTime, $type), 'Database from DateTime');
        $this->assertSame($date, $cast->castDb($timestamp, $type), 'Database from timestamp');

        $this->assertSame($date, $cast->castJson($date, $type), 'Json from string');
        $this->assertSame($date, $cast->castJson($carbon, $type), 'Json from Carbon');
        $this->assertSame($date, $cast->castJson($dateTime, $type), 'Json from DateTime');
        $this->assertSame($date, $cast->castJson($timestamp, $type), 'Json from timestamp');

        $this->assertNull($cast->cast(null, $type), 'PHP null');
        $this->assertNull($cast->castDb(null, $type), 'Database null');
        $this->assertNull($cast->castJson(null, $type), 'Json null');
    }

    public function test_timestamp() : void
    {
        $type = 'timestamp';

        $cast = new Cast([
            'types' => [
                $type => \JnJairo\Laravel\Cast\Types\TimestampType::class,
            ],
        ]);

        $now = Carbon::now();
        $date = $now->format('Y-m-d H:i:s');
        $carbon = Carbon::createFromFormat('Y-m-d H:i:s', $date);
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $date);
        $timestamp = $carbon->getTimestamp();

        $this->assertIsInt($cast->cast($now, $type), 'Timestamp from PHP');
        $this->assertIsInt($cast->cast($date, $type), 'Timestamp from string');
        $this->assertIsInt($cast->cast($carbon, $type), 'Timestamp from Carbon');
        $this->assertIsInt($cast->cast($dateTime, $type), 'Timestamp from DateTime');
        $this->assertIsInt($cast->cast($timestamp, $type), 'Timestamp from timestamp');

        $this->assertSame($timestamp, $cast->cast($now, $type), 'PHP from PHP');
        $this->assertSame($timestamp, $cast->cast($date, $type), 'PHP from string');
        $this->assertSame($timestamp, $cast->cast($carbon, $type), 'PHP from Carbon');
        $this->assertSame($timestamp, $cast->cast($dateTime, $type), 'PHP from DateTime');
        $this->assertSame($timestamp, $cast->cast($timestamp, $type), 'PHP from timestamp');

        $this->assertSame($timestamp, $cast->castDb($date, $type), 'Database from string');
        $this->assertSame($timestamp, $cast->castDb($carbon, $type), 'Database from Carbon');
        $this->assertSame($timestamp, $cast->castDb($dateTime, $type), 'Database from DateTime');
        $this->assertSame($timestamp, $cast->castDb($timestamp, $type), 'Database from timestamp');

        $this->assertSame($timestamp, $cast->castJson($date, $type), 'Json from string');
        $this->assertSame($timestamp, $cast->castJson($carbon, $type), 'Json from Carbon');
        $this->assertSame($timestamp, $cast->castJson($dateTime, $type), 'Json from DateTime');
        $this->assertSame($timestamp, $cast->castJson($timestamp, $type), 'Json from timestamp');

        $this->assertNull($cast->cast(null, $type), 'PHP null');
        $this->assertNull($cast->castDb(null, $type), 'Database null');
        $this->assertNull($cast->castJson(null, $type), 'Json null');
    }

    public function test_json() : void
    {
        $type = 'json';

        $cast = new Cast([
            'types' => [
                $type => \JnJairo\Laravel\Cast\Types\JsonType::class,
            ],
        ]);

        $json = '{"foo":"bar"}';
        $array = ['foo' => 'bar'];
        $object = (object) $array;
        $collection = new Collection($array);

        $arrayable = new DummyArrayable();
        $jsonable = new DummyJsonable();

        $this->assertIsString($cast->cast($json, $type, 'json'), 'Format json from json');
        $this->assertIsArray($cast->cast($json, $type, 'array'), 'Format array from json');
        $this->assertIsObject($cast->cast($json, $type, 'object'), 'Format object from json');
        $this->assertInstanceOf(
            Collection::class,
            $cast->cast($json, $type, 'collection'),
            'Format collection from json'
        );

        $this->assertIsString($cast->cast($array, $type, 'json'), 'Format json from array');
        $this->assertIsArray($cast->cast($array, $type, 'array'), 'Format array from array');
        $this->assertIsObject($cast->cast($array, $type, 'object'), 'Format object from array');
        $this->assertInstanceOf(
            Collection::class,
            $cast->cast($array, $type, 'collection'),
            'Format collection from array'
        );

        $this->assertIsString($cast->cast($object, $type, 'json'), 'Format json from object');
        $this->assertIsArray($cast->cast($object, $type, 'array'), 'Format array from object');
        $this->assertIsObject($cast->cast($object, $type, 'object'), 'Format object from object');
        $this->assertInstanceOf(
            Collection::class,
            $cast->cast($object, $type, 'collection'),
            'Format collection from object'
        );

        $this->assertIsString($cast->cast($collection, $type, 'json'), 'Format json from collection');
        $this->assertIsArray($cast->cast($collection, $type, 'array'), 'Format array from collection');
        $this->assertIsObject($cast->cast($collection, $type, 'object'), 'Format object from collection');
        $this->assertInstanceOf(
            Collection::class,
            $cast->cast($collection, $type, 'collection'),
            'Format collection from collection'
        );

        $this->assertSame($array, $cast->cast($jsonable, $type, 'array'), 'Format array from jsonable');
        $this->assertEquals($object, $cast->cast($jsonable, $type, 'object'), 'Format object from jsonable');
        $this->assertSame($json, $cast->cast($arrayable, $type, 'json'), 'Format json from arrayable');

        $this->assertSame($json, $cast->cast($json, $type), 'PHP default json from json');
        $this->assertSame($json, $cast->cast($array, $type), 'PHP default json from array');
        $this->assertSame($json, $cast->cast($object, $type), 'PHP default json from object');
        $this->assertSame($json, $cast->cast($collection, $type), 'PHP default json from collection');

        $this->assertSame($json, $cast->cast($json, $type, 'json'), 'PHP json from json');
        $this->assertSame($json, $cast->cast($array, $type, 'json'), 'PHP json from array');
        $this->assertSame($json, $cast->cast($object, $type, 'json'), 'PHP json from object');
        $this->assertSame($json, $cast->cast($collection, $type, 'json'), 'PHP json from collection');

        $this->assertSame($array, $cast->cast($json, $type, 'array'), 'PHP array from json');
        $this->assertSame($array, $cast->cast($array, $type, 'array'), 'PHP array from array');
        $this->assertSame($array, $cast->cast($object, $type, 'array'), 'PHP array from object');
        $this->assertSame($array, $cast->cast($collection, $type, 'array'), 'PHP array from collection');

        $this->assertEquals($object, $cast->cast($json, $type, 'object'), 'PHP object from json');
        $this->assertEquals($object, $cast->cast($array, $type, 'object'), 'PHP object from array');
        $this->assertEquals($object, $cast->cast($object, $type, 'object'), 'PHP object from object');
        $this->assertEquals($object, $cast->cast($collection, $type, 'object'), 'PHP object from collection');

        $this->assertEquals($collection, $cast->cast($json, $type, 'collection'), 'PHP collection from json');
        $this->assertEquals($collection, $cast->cast($array, $type, 'collection'), 'PHP collection from array');
        $this->assertEquals($collection, $cast->cast($object, $type, 'collection'), 'PHP collection from object');
        $this->assertEquals(
            $collection,
            $cast->cast($collection, $type, 'collection'),
            'PHP collection from collection'
        );

        $this->assertSame($json, $cast->castDb($json, $type), 'Database from json');
        $this->assertSame($json, $cast->castDb($array, $type), 'Database from array');
        $this->assertSame($json, $cast->castDb($object, $type), 'Database from object');
        $this->assertSame($json, $cast->castDb($collection, $type), 'Database from collection');

        $this->assertSame($array, $cast->castJson($json, $type), 'Json from json');
        $this->assertSame($array, $cast->castJson($array, $type), 'Json from array');
        $this->assertSame($array, $cast->castJson($object, $type), 'Json from object');
        $this->assertSame($array, $cast->castJson($collection, $type), 'Json from collection');

        $this->assertNull($cast->cast(null, $type), 'PHP null');
        $this->assertNull($cast->castDb(null, $type), 'Database null');
        $this->assertNull($cast->castJson(null, $type), 'Json null');
    }

    public function test_json_config() : void
    {
        $type = 'json';

        $cast = new Cast([
            'types' => [
                $type => [
                    'class' => \JnJairo\Laravel\Cast\Types\JsonType::class,
                    'config' => [
                        'format' => 'object',
                    ],
                ],
            ],
        ]);

        $json = '{"foo":"bar"}';
        $array = ['foo' => 'bar'];
        $object = (object) $array;
        $collection = new Collection($array);

        $arrayable = new DummyArrayable();
        $jsonable = new DummyJsonable();

        $this->assertEquals($object, $cast->cast($json, $type), 'PHP from json');
        $this->assertEquals($object, $cast->cast($array, $type), 'PHP from array');
        $this->assertEquals($object, $cast->cast($object, $type), 'PHP from object');
        $this->assertEquals($object, $cast->cast($collection, $type), 'PHP from collection');

        $this->assertSame($json, $cast->castDb($json, $type), 'Database from json');
        $this->assertSame($json, $cast->castDb($array, $type), 'Database from array');
        $this->assertSame($json, $cast->castDb($object, $type), 'Database from object');
        $this->assertSame($json, $cast->castDb($collection, $type), 'Database from collection');

        $this->assertSame($array, $cast->castJson($json, $type), 'Json from json');
        $this->assertSame($array, $cast->castJson($array, $type), 'Json from array');
        $this->assertSame($array, $cast->castJson($object, $type), 'Json from object');
        $this->assertSame($array, $cast->castJson($collection, $type), 'Json from collection');

        $this->assertNull($cast->cast(null, $type), 'PHP null');
        $this->assertNull($cast->castDb(null, $type), 'Database null');
        $this->assertNull($cast->castJson(null, $type), 'Json null');
    }

    public function test_array() : void
    {
        $type = 'array';

        $cast = new Cast([
            'types' => [
                $type => \JnJairo\Laravel\Cast\Types\ArrayType::class,
            ],
        ]);

        $json = '{"foo":"bar"}';
        $array = ['foo' => 'bar'];
        $object = (object) $array;
        $collection = new Collection($array);

        $this->assertSame($array, $cast->cast($json, $type), 'PHP from json');
        $this->assertSame($array, $cast->cast($array, $type), 'PHP from array');
        $this->assertSame($array, $cast->cast($object, $type), 'PHP from object');
        $this->assertSame($array, $cast->cast($collection, $type), 'PHP from collection');

        $this->assertSame($json, $cast->castDb($json, $type), 'Database from json');
        $this->assertSame($json, $cast->castDb($array, $type), 'Database from array');
        $this->assertSame($json, $cast->castDb($object, $type), 'Database from object');
        $this->assertSame($json, $cast->castDb($collection, $type), 'Database from collection');

        $this->assertSame($array, $cast->castJson($json, $type), 'Json from json');
        $this->assertSame($array, $cast->castJson($array, $type), 'Json from array');
        $this->assertSame($array, $cast->castJson($object, $type), 'Json from object');
        $this->assertSame($array, $cast->castJson($collection, $type), 'Json from collection');

        $this->assertNull($cast->cast(null, $type), 'PHP null');
        $this->assertNull($cast->castDb(null, $type), 'Database null');
        $this->assertNull($cast->castJson(null, $type), 'Json null');
    }

    public function test_object() : void
    {
        $type = 'object';

        $cast = new Cast([
            'types' => [
                $type => \JnJairo\Laravel\Cast\Types\ObjectType::class,
            ],
        ]);

        $json = '{"foo":"bar"}';
        $array = ['foo' => 'bar'];
        $object = (object) $array;
        $collection = new Collection($array);

        $this->assertEquals($object, $cast->cast($json, $type), 'PHP from json');
        $this->assertEquals($object, $cast->cast($array, $type), 'PHP from array');
        $this->assertEquals($object, $cast->cast($object, $type), 'PHP from object');
        $this->assertEquals($object, $cast->cast($collection, $type), 'PHP from collection');

        $this->assertSame($json, $cast->castDb($json, $type), 'Database from json');
        $this->assertSame($json, $cast->castDb($array, $type), 'Database from array');
        $this->assertSame($json, $cast->castDb($object, $type), 'Database from object');
        $this->assertSame($json, $cast->castDb($collection, $type), 'Database from collection');

        $this->assertSame($array, $cast->castJson($json, $type), 'Json from json');
        $this->assertSame($array, $cast->castJson($array, $type), 'Json from array');
        $this->assertSame($array, $cast->castJson($object, $type), 'Json from object');
        $this->assertSame($array, $cast->castJson($collection, $type), 'Json from collection');

        $this->assertNull($cast->cast(null, $type), 'PHP null');
        $this->assertNull($cast->castDb(null, $type), 'Database null');
        $this->assertNull($cast->castJson(null, $type), 'Json null');
    }

    public function test_collection() : void
    {
        $type = 'collection';

        $cast = new Cast([
            'types' => [
                $type => \JnJairo\Laravel\Cast\Types\CollectionType::class,
            ],
        ]);

        $json = '{"foo":"bar"}';
        $array = ['foo' => 'bar'];
        $object = (object) $array;
        $collection = new Collection($array);

        $this->assertEquals($collection, $cast->cast($json, $type), 'PHP from json');
        $this->assertEquals($collection, $cast->cast($array, $type), 'PHP from array');
        $this->assertEquals($collection, $cast->cast($object, $type), 'PHP from object');
        $this->assertEquals($collection, $cast->cast($collection, $type), 'PHP from collection');

        $this->assertSame($json, $cast->castDb($json, $type), 'Database from json');
        $this->assertSame($json, $cast->castDb($array, $type), 'Database from array');
        $this->assertSame($json, $cast->castDb($object, $type), 'Database from object');
        $this->assertSame($json, $cast->castDb($collection, $type), 'Database from collection');

        $this->assertSame($array, $cast->castJson($json, $type), 'Json from json');
        $this->assertSame($array, $cast->castJson($array, $type), 'Json from array');
        $this->assertSame($array, $cast->castJson($object, $type), 'Json from object');
        $this->assertSame($array, $cast->castJson($collection, $type), 'Json from collection');

        $this->assertNull($cast->cast(null, $type), 'PHP null');
        $this->assertNull($cast->castDb(null, $type), 'Database null');
        $this->assertNull($cast->castJson(null, $type), 'Json null');
    }

    public function test_decimal() : void
    {
        $type = 'decimal';

        $cast = new Cast([
            'types' => [
                $type => \JnJairo\Laravel\Cast\Types\DecimalType::class,
            ],
        ]);

        $decimal = new Decimal('1234.56', 28);
        $float = 1234.56;
        $string = '1234.56';

        $this->assertEquals($decimal, $cast->cast($decimal, $type), 'PHP from decimal');
        $this->assertEquals($decimal, $cast->cast($float, $type), 'PHP from float');
        $this->assertEquals($decimal, $cast->cast($string, $type), 'PHP from string');

        $this->assertSame($string, $cast->castDb($decimal, $type), 'Database from decimal');
        $this->assertSame($string, $cast->castDb($float, $type), 'Database from float');
        $this->assertSame($string, $cast->castDb($string, $type), 'Database from string');

        $this->assertSame($string, $cast->castJson($decimal, $type), 'Json from decimal');
        $this->assertSame($string, $cast->castJson($float, $type), 'Json from float');
        $this->assertSame($string, $cast->castJson($string, $type), 'Json from string');

        $this->assertNull($cast->cast(null, $type), 'PHP null');
        $this->assertNull($cast->castDb(null, $type), 'Database null');
        $this->assertNull($cast->castJson(null, $type), 'Json null');
    }

    public function decimalConfigProvider() : array
    {
        return [
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
    }

    /**
     * @dataProvider decimalConfigProvider
     */
    public function test_decimal_config(
        ?int $precision,
        ?int $places,
        ?string $roundMode,
        string $string,
        string $stringRounded
    ) : void {
        $type = 'decimal';

        $cast = new Cast([
            'types' => [
                $type => [
                    'class' => \JnJairo\Laravel\Cast\Types\DecimalType::class,
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

        $this->assertEquals($decimalRounded, $cast->cast($decimal, $type), 'PHP from decimal');
        $this->assertEquals($decimalRounded, $cast->cast($float, $type), 'PHP from float');
        $this->assertEquals($decimalRounded, $cast->cast($string, $type), 'PHP from string');

        $this->assertSame($stringRounded, $cast->castDb($decimal, $type), 'Database from decimal');
        $this->assertSame($stringRounded, $cast->castDb($float, $type), 'Database from float');
        $this->assertSame($stringRounded, $cast->castDb($string, $type), 'Database from string');

        $this->assertSame($stringRounded, $cast->castJson($decimal, $type), 'Json from decimal');
        $this->assertSame($stringRounded, $cast->castJson($float, $type), 'Json from float');
        $this->assertSame($stringRounded, $cast->castJson($string, $type), 'Json from string');

        $this->assertNull($cast->cast(null, $type), 'PHP null');
        $this->assertNull($cast->castDb(null, $type), 'Database null');
        $this->assertNull($cast->castJson(null, $type), 'Json null');
    }

    /**
     * @dataProvider decimalConfigProvider
     */
    public function test_decimal_format(
        ?int $precision,
        ?int $places,
        ?string $roundMode,
        string $string,
        string $stringRounded
    ) : void {
        $type = 'decimal';

        $cast = new Cast([
            'types' => [
                $type => \JnJairo\Laravel\Cast\Types\DecimalType::class,
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

        $format = implode('|', $format);

        $this->assertEquals($decimalRounded, $cast->cast($decimal, $type, $format), 'PHP from decimal');
        $this->assertEquals($decimalRounded, $cast->cast($float, $type, $format), 'PHP from float');
        $this->assertEquals($decimalRounded, $cast->cast($string, $type, $format), 'PHP from string');

        $this->assertSame($stringRounded, $cast->castDb($decimal, $type, $format), 'Database from decimal');
        $this->assertSame($stringRounded, $cast->castDb($float, $type, $format), 'Database from float');
        $this->assertSame($stringRounded, $cast->castDb($string, $type, $format), 'Database from string');

        $this->assertSame($stringRounded, $cast->castJson($decimal, $type, $format), 'Json from decimal');
        $this->assertSame($stringRounded, $cast->castJson($float, $type, $format), 'Json from float');
        $this->assertSame($stringRounded, $cast->castJson($string, $type, $format), 'Json from string');

        $this->assertNull($cast->cast(null, $type), 'PHP null');
        $this->assertNull($cast->castDb(null, $type), 'Database null');
        $this->assertNull($cast->castJson(null, $type), 'Json null');
    }

    public function test_uuid() : void
    {
        $type = 'uuid';

        $cast = new Cast([
            'types' => [
                $type => \JnJairo\Laravel\Cast\Types\UuidType::class,
            ],
        ]);

        $uuid = Uuid::uuid4();
        $string = $uuid->toString();
        $binary = $uuid->getBytes();
        $hex = bin2hex($binary);

        $this->assertSame($uuid, $cast->cast($uuid, $type), 'PHP from uuid');
        $this->assertEquals($uuid, $cast->cast($string, $type), 'PHP from string');
        $this->assertEquals($uuid, $cast->cast($binary, $type), 'PHP from binary');
        $this->assertEquals($uuid, $cast->cast($hex, $type), 'PHP from hex');

        $this->assertSame($binary, $cast->castDb($uuid, $type), 'Database from uuid');
        $this->assertSame($binary, $cast->castDb($string, $type), 'Database from string');
        $this->assertSame($binary, $cast->castDb($binary, $type), 'Database from binary');
        $this->assertSame($binary, $cast->castDb($hex, $type), 'Database from hex');

        $this->assertSame($string, $cast->castJson($uuid, $type), 'Json from uuid');
        $this->assertSame($string, $cast->castJson($string, $type), 'Json from string');
        $this->assertSame($string, $cast->castJson($binary, $type), 'Json from binary');
        $this->assertSame($string, $cast->castJson($hex, $type), 'Json from hex');

        $this->assertNull($cast->cast(null, $type), 'PHP null');
        $this->assertNull($cast->castDb(null, $type), 'Database null');
        $this->assertNull($cast->castJson(null, $type), 'Json null');
    }

    public function test_uuid_new() : void
    {
        $type = 'uuid';

        $cast = new Cast([
            'types' => [
                $type => \JnJairo\Laravel\Cast\Types\UuidType::class,
            ],
        ]);

        $value = '';

        $uuid = $cast->cast($value, $type);
        $this->assertInstanceOf(Uuid::class, $uuid, 'PHP');
        $this->assertSame(4, $uuid->getVersion(), 'PHP uuid version');

        $uuid = $cast->castDb($value, $type);
        $this->assertIsString($uuid, 'Database');
        $this->assertSame(16, strlen($uuid), 'Database uuid length');

        $uuid = $cast->castJson($value, $type);
        $this->assertIsString($uuid, 'Json');
        $this->assertSame(36, strlen($uuid), 'Json uuid length');
    }

    public function uuidConfigProvider() : array
    {
        return [
            'uuid1' => [1, 'uuid1'],
            'uuid4' => [4, 'uuid4'],
            'ordered' => [4, 'ordered'],
        ];
    }

    /**
     * @dataProvider uuidConfigProvider
     */
    public function test_uuid_config(int $version, string $format) : void
    {
        $type = 'uuid';

        $cast = new Cast([
            'types' => [
                $type => [
                    'class' => \JnJairo\Laravel\Cast\Types\UuidType::class,
                    'config' => [
                        'format' => $format,
                    ],
                ],
            ],
        ]);

        $value = '';

        $uuid = $cast->cast($value, $type);
        $this->assertInstanceOf(Uuid::class, $uuid, 'PHP');
        $this->assertSame($version, $uuid->getVersion(), 'PHP uuid version');

        $uuid = $cast->castDb($value, $type);
        $this->assertIsString($uuid, 'Database');
        $this->assertSame(16, strlen($uuid), 'Database uuid length');

        $uuid = $cast->castJson($value, $type);
        $this->assertIsString($uuid, 'Json');
        $this->assertSame(36, strlen($uuid), 'Json uuid length');
    }

    /**
     * @dataProvider uuidConfigProvider
     */
    public function test_uuid_format(int $version, string $format) : void
    {
        $type = 'uuid';

        $cast = new Cast([
            'types' => [
                $type => \JnJairo\Laravel\Cast\Types\UuidType::class,
            ],
        ]);

        $value = '';

        $uuid = $cast->cast($value, $type, $format);
        $this->assertInstanceOf(Uuid::class, $uuid, 'PHP');
        $this->assertSame($version, $uuid->getVersion(), 'PHP uuid version');

        $uuid = $cast->castDb($value, $type, $format);
        $this->assertIsString($uuid, 'Database');
        $this->assertSame(16, strlen($uuid), 'Database uuid length');

        $uuid = $cast->castJson($value, $type, $format);
        $this->assertIsString($uuid, 'Json');
        $this->assertSame(36, strlen($uuid), 'Json uuid length');
    }
}
