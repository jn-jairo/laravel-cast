<?php

namespace JnJairo\Laravel\Cast\Types;

use Decimal\Decimal;

class DecimalType extends Type
{
    protected const ROUND_MODE = [
        'up' => Decimal::ROUND_UP,
        'down' => Decimal::ROUND_DOWN,
        'ceiling' => Decimal::ROUND_CEILING,
        'floor' => Decimal::ROUND_FLOOR,
        'half_up' => Decimal::ROUND_HALF_UP,
        'half_down' => Decimal::ROUND_HALF_DOWN,
        'half_even' => Decimal::ROUND_HALF_EVEN,
        'half_odd' => Decimal::ROUND_HALF_ODD,
        'truncate' => Decimal::ROUND_TRUNCATE,
    ];

    /**
     * Default precision.
     *
     * @var int
     */
    protected int $defaultPrecision = 28;

    /**
     * Default places.
     *
     * @var int
     */
    protected int $defaultPlaces = 2;

    /**
     * Default round mode.
     *
     * @var int
     */
    protected int $defaultRoundMode = Decimal::ROUND_HALF_UP;

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
            isset($this->config['precision'])
            && (is_string($this->config['precision']) || is_int($this->config['precision']))
            && $this->config['precision'] !== ''
        ) {
            $this->defaultPrecision = (int) $this->config['precision'];
        }

        if (
            isset($this->config['places'])
            && (is_string($this->config['places']) || is_int($this->config['places']))
            && $this->config['places'] !== ''
        ) {
            $this->defaultPlaces = (int) $this->config['places'];
        }

        if (
            isset($this->config['round_mode'])
            && is_string($this->config['round_mode'])
            && $this->config['round_mode'] !== ''
        ) {
            $this->defaultRoundMode = $this->parseRoundMode($this->config['round_mode']);
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

        return $this->asDecimal($value, $this->parseFormat($format));
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

        $config = $this->parseFormat($format);

        $value = $this->asDecimal($value, $config);

        if (is_null($value)) {
            return $value;
        }

        return $this->serializeDecimal($value, $config);
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
        return $this->castDb($value, $format);
    }

    /**
     * Cast to Decimal object.
     *
     * @param mixed $value
     * @param array<string, mixed> $config
     * @return \Decimal\Decimal|null
     */
    protected function asDecimal(mixed $value, array $config): ?Decimal
    {
        /**
         * @var int $precision
         */
        $precision = $config['precision'];

        /**
         * @var int $places
         */
        $places = $config['places'];

        /**
         * @var int $roundMode
         */
        $roundMode = $config['round_mode'];

        if (is_float($value)) {
            $value = (string) $value;
        }

        if (
            $value instanceof Decimal
            || is_string($value)
            || is_int($value)
        ) {
            $value = new Decimal($value, $precision);
            $value = $value->round($places, $roundMode);
            return $value;
        }

        return null;
    }

    /**
     * Serialize the Decimal.
     *
     * @param \Decimal\Decimal $decimal
     * @param array<string, mixed> $config
     * @return string
     */
    protected function serializeDecimal(Decimal $decimal, array $config): string
    {
        /**
         * @var int $places
         */
        $places = $config['places'];

        /**
         * @var int $roundMode
         */
        $roundMode = $config['round_mode'];

        return $decimal->toFixed($places, false, $roundMode);
    }

    /**
     * Parse the format.
     *
     * @param string $format
     * @return array<string, mixed> ['precision', 'places', 'round_mode']
     */
    protected function parseFormat(string $format): array
    {
        $formatParsed = [
            'precision' => $this->defaultPrecision,
            'places' => $this->defaultPlaces,
            'round_mode' => $this->defaultRoundMode,
        ];

        if (strpos($format, '|') !== false) {
            $formats = explode('|', $format);
        } else {
            $formats = explode(',', $format);
        }

        foreach ($formats as $format) {
            if ($format !== '') {
                if (strpos($format, ':') !== false) {
                    list($precision, $places) = explode(':', $format);

                    $formatParsed['precision'] = (int) $precision;
                    $formatParsed['places'] = (int) $places;
                } elseif (is_numeric($format)) {
                    $formatParsed['places'] = (int) $format;
                } elseif (isset(self::ROUND_MODE[$format])) {
                    $formatParsed['round_mode'] = $this->parseRoundMode($format);
                }
            }
        }

        return $formatParsed;
    }

    /**
     * Parse the round mode.
     *
     * @param string $roundMode
     * @return int
     */
    protected function parseRoundMode(string $roundMode): int
    {
        if (isset(self::ROUND_MODE[$roundMode])) {
            return self::ROUND_MODE[$roundMode];
        }

        return $this->defaultRoundMode;
    }
}
