<?php

namespace JnJairo\Laravel\Cast\Types;

use Illuminate\Support\Str;
use JnJairo\Laravel\Cast\Facades\Cast;

class Base64Type extends Type
{
    protected const DECODE = [
        'one',
        'all',
    ];

    /**
     * Default decode.
     *
     * @var string
     */
    protected string $defaultDecode = 'one';

    /**
     * Default prefix.
     *
     * @var string
     */
    protected string $defaultPrefix = '';

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
            isset($this->config['decode'])
            && is_string($this->config['decode'])
            && $this->config['decode'] !== ''
        ) {
            $this->defaultDecode = $this->parseDecode($this->config['decode']);
        }

        if (
            isset($this->config['prefix'])
            && is_string($this->config['prefix'])
            && $this->config['prefix'] !== ''
        ) {
            $this->defaultPrefix = $this->config['prefix'];
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
        if (is_null($value) || ! is_string($value)) {
            return $value;
        }

        return $this->decode($value, $this->parseFormat($format));
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
        if (is_null($value) || ! is_string($value)) {
            return $value;
        }

        return $this->encode($value, $this->parseFormat($format));
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
        if (is_null($value) || ! is_string($value)) {
            return $value;
        }

        return $this->decode($value, $this->parseFormat($format));
    }

    /**
     * Encode the value.
     *
     * @param string $value
     * @param array<string, mixed> $config
     * @return string
     */
    protected function encode(string $value, array $config): string
    {
        if ($config['decode'] === 'all') {
            $value = $this->decode($value, $config);
        }

        return $this->addPrefix(base64_encode($value), $config);
    }

    /**
     * Decode the value.
     *
     * @param string $value
     * @param array<string, mixed> $config
     * @return string
     */
    protected function decode(string $value, array $config): string
    {
        /**
         * @var string $prefix
         */
        $prefix = $config['prefix'];

        /**
         * @var string $decode
         */
        $decode = $config['decode'];

        if ($decode === 'one') {
            if ($prefix === '' || Str::startsWith($value, $prefix)) {
                $decodedValue = @base64_decode($this->removePrefix($value, $config), true);
                if (is_string($decodedValue)) {
                    $value = $decodedValue;
                }
            }
        } elseif ($decode === 'all') {
            $decoded = ! is_string($value);

            while (! $decoded) {
                if ($prefix === '' || Str::startsWith($value, $prefix)) {
                    $decodedValue = @base64_decode($this->removePrefix($value, $config), true);
                    if (is_string($decodedValue)) {
                        $value = $decodedValue;
                    } else {
                        $decoded = true;
                    }
                } else {
                    $decoded = true;
                }
            }
        }

        return $value;
    }

    /**
     * Add the prefix.
     *
     * @param string $value
     * @param array<string, mixed> $config
     * @return string
     */
    protected function addPrefix(string $value, array $config): string
    {
        return $config['prefix'] . $value;
    }

    /**
     * Remove the prefix.
     *
     * @param string $value
     * @param array<string, mixed> $config
     * @return string
     */
    protected function removePrefix(string $value, array $config): string
    {
        /**
         * @var string $prefix
         */
        $prefix = $config['prefix'];

        if ($prefix !== '' && Str::startsWith($value, $prefix)) {
            $value = Str::after($value, $prefix);
        }

        return $value;
    }

    /**
     * Parse the format.
     *
     * @param string $format
     * @return array<string, mixed> ['decode', 'prefix']
     */
    protected function parseFormat(string $format): array
    {
        $formatParsed = [
            'decode' => $this->defaultDecode,
            'prefix' => $this->defaultPrefix,
        ];

        if (strpos($format, '|') !== false) {
            $formats = explode('|', $format);
        } else {
            $formats = explode(',', $format);
        }

        foreach ($formats as $format) {
            if ($format !== '') {
                if (in_array($format, self::DECODE)) {
                    $formatParsed['decode'] = $this->parseDecode($format);
                } else {
                    $formatParsed['prefix'] = $format;
                }
            }
        }

        if ($formatParsed['prefix'] === '') {
            $formatParsed['decode'] = 'one';
        }

        return $formatParsed;
    }

    /**
     * Parse the decode.
     *
     * @param string $decode
     * @return string
     */
    protected function parseDecode(string $decode): string
    {
        if (in_array($decode, self::DECODE)) {
            return $decode;
        }

        return $this->defaultDecode;
    }
}
