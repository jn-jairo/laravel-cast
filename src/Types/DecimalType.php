<?php

namespace JnJairo\Laravel\Cast\Types;

use Decimal\Decimal;
use JnJairo\Laravel\Cast\Types\Type;

class DecimalType extends Type
{
    /**
     * Default precision.
     *
     * @var int
     */
    protected $defaultPrecision = 28;

    /**
     * Default places.
     *
     * @var int
     */
    protected $defaultPlaces = 2;

    /**
     * Default round mode.
     *
     * @var int
     */
    protected $defaultRoundMode = Decimal::ROUND_HALF_UP;

    /**
     * Set configuration.
     *
     * @param array $config
     * @return void
     */
    public function setConfig(array $config) : void
    {
        parent::setConfig($config);

        if (isset($this->config['precision']) && $this->config['precision'] !== '') {
            $this->defaultPrecision = (int) $this->config['precision'];
        }

        if (isset($this->config['places']) && $this->config['places'] !== '') {
            $this->defaultPlaces = (int) $this->config['places'];
        }

        if (isset($this->config['round_mode']) && $this->config['round_mode'] !== '') {
            $this->defaultRoundMode = $this->parseRoundMode($this->config['round_mode']);
        }
    }

    /**
     * Cast to PHP type.
     *
     * @param mixed $value
     * @param strint $format
     * @return mixed
     */
    public function cast($value, string $format = '')
    {
        if (is_null($value)) {
            return $value;
        }

        $format = $this->parseFormat($format);

        if (is_float($value)) {
            $value = (string) $value;
        }

        $value = new Decimal($value, $format['precision']);
        $value = $value->round($format['places'], $format['round_mode']);

        return $value;
    }

    /**
     * Cast to database type.
     *
     * @param mixed $value
     * @param strint $format
     * @return mixed
     */
    public function castDb($value, string $format = '')
    {
        if (is_null($value)) {
            return $value;
        }

        $value = $this->cast($value, $format);
        $format = $this->parseFormat($format);
        $value = $value->toFixed($format['places'], false, $format['round_mode']);

        return $value;
    }

    /**
     * Cast to json type.
     *
     * @param mixed $value
     * @param strint $format
     * @return mixed
     */
    public function castJson($value, string $format = '')
    {
        return $this->castDb($value, $format);
    }

    /**
     * Parse the format.
     *
     * @param string $format
     * @return array ['precision', 'places', 'round_mode']
     */
    protected function parseFormat(string $format) : array
    {
        $formatParsed = [
            'precision' => $this->defaultPrecision,
            'places' => $this->defaultPlaces,
            'round_mode' => $this->defaultRoundMode,
        ];

        $formats = explode('|', $format);

        foreach ($formats as $format) {
            if ($format !== '') {
                if (strpos($format, ':') !== false) {
                    list($precision, $places) = explode(':', $format);

                    $formatParsed['precision'] = (int) $precision;
                    $formatParsed['places'] = (int) $places;
                } elseif (is_numeric($format)) {
                    $formatParsed['places'] = (int) $format;
                } else {
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
    protected function parseRoundMode(string $roundMode) : int
    {
        $rounds = [
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

        if (isset($rounds[$roundMode])) {
            return $rounds[$roundMode];
        }

        return $this->defaultRoundMode;
    }
}
