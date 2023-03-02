<?php

namespace JnJairo\Laravel\Cast\Types;

use Ramsey\Uuid\Codec\TimestampFirstCombCodec;
use Ramsey\Uuid\Generator\CombGenerator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidInterface;

use function Safe\hex2bin;

class UuidType extends Type
{
    /**
     * Default format.
     *
     * @var string
     */
    protected string $defaultFormat = 'uuid4';

    /**
     * Set configuration.
     *
     * @param array<string, mixed> $config
     * @return void
     */
    public function setConfig(array $config): void
    {
        parent::setConfig($config);

        if (
            isset($this->config['format'])
            && is_string($this->config['format'])
            && $this->config['format'] !== ''
        ) {
            $this->defaultFormat = $this->config['format'];
        }
    }

    /**
     * Cast to PHP type.
     *
     * @param mixed $value
     * @param string $format
     * @return mixed
     */
    public function cast(mixed $value, string $format = ''): mixed
    {
        if (is_null($value)) {
            return $value;
        }

        if ($format === '') {
            $format = $this->defaultFormat;
        }

        if (is_string($value) && strlen($value) == 16) {
            $value = Uuid::fromBytes($value);
        } elseif (is_string($value) && strlen($value) == 32) {
            $value = Uuid::fromBytes(hex2bin($value));
        } elseif (is_string($value) && strlen($value) == 36) {
            $value = Uuid::fromString($value);
        } elseif (! $value instanceof UuidInterface) {
            $value = $this->createUuid($format);
        }

        return $value;
    }

    /**
     * Cast to database type.
     *
     * @param mixed $value
     * @param string $format
     * @return mixed
     */
    public function castDb(mixed $value, string $format = ''): mixed
    {
        if (is_null($value)) {
            return $value;
        }

        /**
         * @var \Ramsey\Uuid\UuidInterface $value
         */
        $value = $this->cast($value, $format);
        $value = $value->getBytes();

        return $value;
    }

    /**
     * Cast to json type.
     *
     * @param mixed $value
     * @param string $format
     * @return mixed
     */
    public function castJson(mixed $value, string $format = ''): mixed
    {
        if (is_null($value)) {
            return $value;
        }

        /**
         * @var \Ramsey\Uuid\UuidInterface $value
         */
        $value = $this->cast($value, $format);
        $value = $value->toString();

        return $value;
    }

    /**
     * Create a UUID.
     *
     * @param string $format
     * @return \Ramsey\Uuid\UuidInterface
     */
    protected function createUuid(string $format): UuidInterface
    {
        $uuid = null;

        switch ($format) {
            case 'ordered':
                $factory = new UuidFactory();
                $factory->setRandomGenerator(new CombGenerator(
                    $factory->getRandomGenerator(),
                    $factory->getNumberConverter(),
                ));
                $factory->setCodec(new TimestampFirstCombCodec(
                    $factory->getUuidBuilder(),
                ));
                $uuid = $factory->uuid4();
                break;
            case 'uuid1':
                $uuid = Uuid::uuid1();
                break;
            case 'uuid4':
            default:
                $uuid = Uuid::uuid4();
                break;
        }

        return $uuid;
    }
}
