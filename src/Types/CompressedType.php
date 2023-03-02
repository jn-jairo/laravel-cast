<?php

namespace JnJairo\Laravel\Cast\Types;

use JnJairo\Laravel\Cast\Facades\Cast;

class CompressedType extends Type
{
    protected const COMPRESS = [
        'always',
        'smaller',
    ];

    protected const DECOMPRESS = [
        'one',
        'all',
    ];

    protected const ENCODING = [
        'raw' => ZLIB_ENCODING_RAW,
        'deflate' => ZLIB_ENCODING_DEFLATE,
        'gzip' => ZLIB_ENCODING_GZIP,
    ];

    /**
     * Default compress.
     *
     * @var string
     */
    protected string $defaultCompress = 'always';

    /**
     * Default decompress.
     *
     * @var string
     */
    protected string $defaultDecompress = 'one';

    /**
     * Default level.
     *
     * @var int
     */
    protected int $defaultLevel = -1;

    /**
     * Default encoding.
     *
     * @var int
     */
    protected int $defaultEncoding = ZLIB_ENCODING_RAW;

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
            isset($this->config['compress'])
            && is_string($this->config['compress'])
            && $this->config['compress'] !== ''
        ) {
            $this->defaultCompress = $this->parseCompress($this->config['compress']);
        }

        if (
            isset($this->config['decompress'])
            && is_string($this->config['decompress'])
            && $this->config['decompress'] !== ''
        ) {
            $this->defaultDecompress = $this->parseDecompress($this->config['decompress']);
        }

        if (
            isset($this->config['level'])
            && (is_string($this->config['level']) || is_int($this->config['level']))
            && $this->config['level'] !== ''
        ) {
            $this->defaultLevel = $this->parseLevel($this->config['level']);
        }

        if (
            isset($this->config['encoding'])
            && is_string($this->config['encoding'])
            && $this->config['encoding'] !== ''
        ) {
            $this->defaultEncoding = $this->parseEncoding($this->config['encoding']);
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

        return $this->decompress($value, $this->parseFormat($format));
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

        return $this->compress($value, $this->parseFormat($format));
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

        return $this->decompress($value, $this->parseFormat($format));
    }

    /**
     * Compress the value.
     *
     * @param string $value
     * @param array<string, mixed> $config
     * @return string
     */
    protected function compress(string $value, array $config): string
    {
        /**
         * @var int $encoding
         */
        $encoding = $config['encoding'];

        /**
         * @var int $level
         */
        $level = $config['level'];

        /**
         * @var string $compress
         */
        $compress = $config['compress'];

        /**
         * @var string $decompress
         */
        $decompress = $config['decompress'];

        $method = [
            ZLIB_ENCODING_RAW => 'gzdeflate',
            ZLIB_ENCODING_DEFLATE => 'gzcompress',
            ZLIB_ENCODING_GZIP => 'gzencode',
        ][$encoding];

        if (is_string($value)) {
            if ($decompress === 'all') {
                $value = $this->decompress($value, $config);
            }

            $compressedValue = @$method($value, $level, $encoding);

            if (
                is_string($compressedValue)
                && ($compress === 'always'
                || $compress === 'smaller' && strlen($compressedValue) < strlen($value))
            ) {
                $value = $compressedValue;
            }
        }

        return $value;
    }

    /**
     * Decompress the value.
     *
     * @param string $value
     * @param array<string, mixed> $config
     * @return string
     */
    protected function decompress(string $value, array $config): string
    {
        $method = [
            ZLIB_ENCODING_RAW => 'gzinflate',
            ZLIB_ENCODING_DEFLATE => 'gzuncompress',
            ZLIB_ENCODING_GZIP => 'gzdecode',
        ][$config['encoding']];

        if ($config['decompress'] === 'one') {
            if (is_string($value)) {
                $decompressedValue = @$method($value);
                if (is_string($decompressedValue)) {
                    $value = $decompressedValue;
                }
            }
        } elseif ($config['decompress'] === 'all') {
            $decompressed = ! is_string($value);

            while (! $decompressed) {
                $decompressedValue = @$method($value);
                if (is_string($decompressedValue)) {
                    $value = $decompressedValue;
                } else {
                    $decompressed = true;
                }
            }
        }

        return $value;
    }

    /**
     * Parse the format.
     *
     * @param string $format
     * @return array<string, mixed> ['compress', 'decompress', 'level', 'encoding']
     */
    protected function parseFormat(string $format): array
    {
        $formatParsed = [
            'compress' => $this->defaultCompress,
            'decompress' => $this->defaultDecompress,
            'level' => $this->defaultLevel,
            'encoding' => $this->defaultEncoding,
        ];

        if (strpos($format, '|') !== false) {
            $formats = explode('|', $format);
        } else {
            $formats = explode(',', $format);
        }

        foreach ($formats as $format) {
            if ($format !== '') {
                if (is_numeric($format)) {
                    $formatParsed['level'] = $this->parseLevel($format);
                } elseif (in_array($format, self::COMPRESS)) {
                    $formatParsed['compress'] = $this->parseCompress($format);
                } elseif (in_array($format, self::DECOMPRESS)) {
                    $formatParsed['decompress'] = $this->parseDecompress($format);
                } elseif (isset(self::ENCODING[$format])) {
                    $formatParsed['encoding'] = $this->parseEncoding($format);
                }
            }
        }

        return $formatParsed;
    }

    /**
     * Parse the compress.
     *
     * @param string $compress
     * @return string
     */
    protected function parseCompress(string $compress): string
    {
        if (in_array($compress, self::COMPRESS)) {
            return $compress;
        }

        return $this->defaultCompress;
    }

    /**
     * Parse the decompress.
     *
     * @param string $decompress
     * @return string
     */
    protected function parseDecompress(string $decompress): string
    {
        if (in_array($decompress, self::DECOMPRESS)) {
            return $decompress;
        }

        return $this->defaultDecompress;
    }

    /**
     * Parse the level.
     *
     * @param int|string $level
     * @return int
     */
    protected function parseLevel(int|string $level): int
    {
        if (is_numeric($level) && $level >= -1 && $level <= 9) {
            return (int) $level;
        }

        return $this->defaultLevel;
    }

    /**
     * Parse the encoding.
     *
     * @param string $encoding
     * @return int
     */
    protected function parseEncoding(string $encoding): int
    {
        if (isset(self::ENCODING[$encoding])) {
            return self::ENCODING[$encoding];
        }

        return $this->defaultEncoding;
    }
}
