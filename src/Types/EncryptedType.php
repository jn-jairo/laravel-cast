<?php

namespace JnJairo\Laravel\Cast\Types;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\StringEncrypter;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Str;
use JnJairo\Laravel\Cast\Facades\Cast;

class EncryptedType extends Type
{
    protected const DECRYPT = [
        'one',
        'all',
    ];

    protected const CIPHER = [
        'aes-128-cbc',
        'aes-256-cbc',
        'aes-128-gcm',
        'aes-256-gcm',
    ];

    /**
     * Default decrypt.
     *
     * @var string
     */
    protected string $defaultDecrypt = 'one';

    /**
     * Default key.
     *
     * @var string
     */
    protected string $defaultKey = '';

    /**
     * Default cipher.
     *
     * @var string
     */
    protected string $defaultCipher = '';

    /**
     * Encrypter instances.
     *
     * @var \Illuminate\Contracts\Encryption\StringEncrypter[]
     */
    protected array $encrypterInstances = [];

    public function __construct()
    {
        /**
         * @var \Illuminate\Contracts\Config\Repository $config
         */
        $config = app('config');

        $key = $config->get('app.key');

        if (is_string($key)) {
            $this->defaultKey = $key;
        }

        if (Str::startsWith($key = $this->defaultKey, $prefix = 'base64:')) {
            $this->defaultKey = base64_decode(Str::after($key, $prefix));
        }

        $cipher = $config->get('app.cipher');

        if (is_string($cipher)) {
            $this->defaultCipher = $cipher;
        }
    }

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
            isset($this->config['decrypt'])
            && is_string($this->config['decrypt'])
            && $this->config['decrypt'] !== ''
        ) {
            $this->defaultDecrypt = $this->parseDecrypt($this->config['decrypt']);
        }

        if (
            isset($this->config['key'])
            && is_string($this->config['key'])
            && $this->config['key'] !== ''
        ) {
            $this->defaultKey = $this->parseKey($this->config['key']);
        }

        if (
            isset($this->config['cipher'])
            && is_string($this->config['cipher'])
            && $this->config['cipher'] !== ''
        ) {
            $this->defaultCipher = $this->parseCipher($this->config['cipher']);
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

        return $this->decrypt($value, $this->parseFormat($format));
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

        return $this->encrypt($value, $this->parseFormat($format));
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

        return $this->decrypt($value, $this->parseFormat($format));
    }

    /**
     * Encrypt the value.
     *
     * @param string $value
     * @param array<string, mixed> $config
     * @return string
     */
    protected function encrypt(string $value, array $config): string
    {
        if (is_string($value)) {
            if ($config['decrypt'] === 'all') {
                $value = $this->decrypt($value, $config);
            }

            $encrypter = $this->getEncrypterInstance($config);

            $value = $encrypter->encryptString($value);
        }

        return $value;
    }

    /**
     * Decrypt the value.
     *
     * @param string $value
     * @param array<string, mixed> $config
     * @return string
     */
    protected function decrypt(string $value, array $config): string
    {
        $encrypter = $this->getEncrypterInstance($config);

        if ($config['decrypt'] === 'one') {
            try {
                $value = $encrypter->decryptString($value);
            } catch (DecryptException $e) {
            }
        } elseif ($config['decrypt'] === 'all') {
            $decrypted = ! is_string($value);

            while (! $decrypted) {
                try {
                    $value = $encrypter->decryptString($value);
                    $decrypted = ! is_string($value);
                } catch (DecryptException $e) {
                    $decrypted = true;
                }
            }
        }

        return $value;
    }

    /**
     * Get the encrypter instance.
     *
     * @param array<string, mixed> $config
     * @return \Illuminate\Contracts\Encryption\StringEncrypter
     */
    protected function getEncrypterInstance(array $config): StringEncrypter
    {
        /**
         * @var string $key
         */
        $key = $config['key'];

        /**
         * @var string $cipher
         */
        $cipher = $config['cipher'];

        $index = base64_encode($key . ':' . $cipher);

        if (! isset($this->encrypterInstances[$index])) {
            $this->encrypterInstances[$index] = new Encrypter($key, strtoupper($cipher));
        }

        return $this->encrypterInstances[$index];
    }

    /**
     * Parse the format.
     *
     * @param string $format
     * @return array<string, mixed> ['decrypt', 'key', 'cipher']
     */
    protected function parseFormat(string $format): array
    {
        $formatParsed = [
            'decrypt' => $this->defaultDecrypt,
            'key' => $this->defaultKey,
            'cipher' => $this->defaultCipher,
        ];

        if (strpos($format, '|') !== false) {
            $formats = explode('|', $format);
        } else {
            $formats = explode(',', $format);
        }

        foreach ($formats as $format) {
            if ($format !== '') {
                if (in_array($format, self::DECRYPT)) {
                    $formatParsed['decrypt'] = $this->parseDecrypt($format);
                } elseif (in_array(strtolower($format), self::CIPHER)) {
                    $formatParsed['cipher'] = $this->parseCipher($format);
                } elseif (Str::startsWith($format, 'base64:')) {
                    $formatParsed['key'] = $this->parseKey($format);
                }
            }
        }

        return $formatParsed;
    }

    /**
     * Parse the decrypt.
     *
     * @param string $decrypt
     * @return string
     */
    protected function parseDecrypt(string $decrypt): string
    {
        if (in_array($decrypt, self::DECRYPT)) {
            return $decrypt;
        }

        return $this->defaultDecrypt;
    }

    /**
     * Parse the key.
     *
     * @param string $key
     * @return string
     */
    protected function parseKey(string $key): string
    {
        if (Str::startsWith($key, $prefix = 'base64:')) {
            return base64_decode(Str::after($key, $prefix));
        }

        return $this->defaultKey;
    }

    /**
     * Parse the cipher.
     *
     * @param string $cipher
     * @return string
     */
    protected function parseCipher(string $cipher): string
    {
        if (in_array($cipher = strtolower($cipher), self::CIPHER)) {
            return $cipher;
        }

        return $this->defaultCipher;
    }
}
