<?php

namespace JnJairo\Laravel\Cast\Types;

class PipeType extends Type
{
    /**
     * Default php cast direction.
     *
     * @var string
     */
    protected string $defaultPhpDirection = '>';

    /**
     * Default db cast direction.
     *
     * @var string
     */
    protected string $defaultDbDirection = '<';

    /**
     * Default json cast direction.
     *
     * @var string
     */
    protected string $defaultJsonDirection = '>';

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
            isset($this->config['php_direction'])
            && is_string($this->config['php_direction'])
            && $this->config['php_direction'] !== ''
            && in_array($this->config['php_direction'], ['>', '<'])
        ) {
            $this->defaultPhpDirection = $this->config['php_direction'];
        }

        if (
            isset($this->config['db_direction'])
            && is_string($this->config['db_direction'])
            && $this->config['db_direction'] !== ''
            && in_array($this->config['db_direction'], ['>', '<'])
        ) {
            $this->defaultDbDirection = $this->config['db_direction'];
        }

        if (
            isset($this->config['json_direction'])
            && is_string($this->config['json_direction'])
            && $this->config['json_direction'] !== ''
            && in_array($this->config['json_direction'], ['>', '<'])
        ) {
            $this->defaultJsonDirection = $this->config['json_direction'];
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

        $format = $this->parseFormat($format, 'php');

        foreach ($format as $next) {
            /**
             * @var string $type
             */
            $type = $next['type'];

            /**
             * @var string $format
             */
            $format = $next['format'];

            $value = $this->getCast()->cast($value, $type, $format);
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

        $format = $this->parseFormat($format, 'db');

        foreach ($format as $next) {
            /**
             * @var string $type
             */
            $type = $next['type'];

            /**
             * @var string $format
             */
            $format = $next['format'];

            $value = $this->getCast()->castDb($value, $type, $format);
        }

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

        $format = $this->parseFormat($format, 'json');

        foreach ($format as $next) {
            /**
             * @var string $type
             */
            $type = $next['type'];

            /**
             * @var string $format
             */
            $format = $next['format'];

            $value = $this->getCast()->castJson($value, $type, $format);
        }

        return $value;
    }

    /**
     * Parse the format.
     *
     * Examples:
     *
     * |encrypted|array|
     * |encrypted|array|<
     * |encrypted|array|>,db:<
     * |encrypted|decimal:2|php:>,db:<,json:>
     * ,encrypted,decimal:2,php:>|db:<|json:>
     * :encrypted:decimal|2:php|>,db|<,json|>
     *
     * @param string $format
     * @param string $type php|db|json (default: php)
     * @return array<int, array<string, mixed>> [['type', 'format'], ...]
     */
    protected function parseFormat(string $format, string $type = 'php'): array
    {
        $list = [];

        $listDirection = [
            'php' => $this->defaultPhpDirection,
            'db' => $this->defaultDbDirection,
            'json' => $this->defaultJsonDirection,
        ][$type];

        if ($format !== '') {
            $separator = substr($format, 0, 1);

            $formats = explode($separator, $format);

            $directions = explode($separator === ',' ? '|' : ',', array_pop($formats));

            $formats = array_filter($formats);

            foreach ($directions as $direction) {
                if (in_array($direction, ['>', '<'])) {
                    $listDirection = $direction;
                } else {
                    $direction = explode($separator === ':' ? '|' : ':', $direction);

                    $directionKey = $direction[0] ?? '';
                    $directionValue = $direction[1] ?? '';

                    if ($directionKey === $type && in_array($directionValue, ['>', '<'])) {
                        $listDirection = $directionValue;
                    }
                }
            }

            foreach ($formats as $format) {
                $formatParts = explode($separator === ':' ? '|' : ':', $format, 2);

                $type = $formatParts[0] ?? '';
                $format = $formatParts[1] ?? '';

                if ($type !== '') {
                    $list[] = [
                        'type' => $type,
                        'format' => $format,
                    ];
                }
            }

            if ($listDirection === '<') {
                $list = array_reverse($list);
            }
        }

        return $list;
    }
}
