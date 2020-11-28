<?php

namespace JnJairo\Laravel\Cast\Types;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use JnJairo\Laravel\Cast\Facades\Cast;
use JnJairo\Laravel\Cast\Types\Type;

class EncryptedType extends Type
{
    /**
     * Cast to PHP type.
     *
     * @param mixed $value
     * @param string $format
     * @return mixed
     */
    public function cast($value, string $format = '')
    {
        if (is_null($value)) {
            return $value;
        }

        $value = $this->decrypt($value);

        $formatParts = explode(':', $format, 2);

        $type = $formatParts[0] ?? '';
        $format = $formatParts[1] ?? '';

        if ($type !== '') {
            $value = Cast::cast($value, $type, $format);
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
    public function castDb($value, string $format = '')
    {
        if (is_null($value)) {
            return $value;
        }

        $value = $this->decrypt($value);

        $formatParts = explode(':', $format, 2);

        $type = $formatParts[0] ?? '';
        $format = $formatParts[1] ?? '';

        if ($type !== '') {
            $value = Cast::castDb($value, $type, $format);
        }

        return Crypt::encrypt($value, false);
    }

    /**
     * Cast to json type.
     *
     * @param mixed $value
     * @param string $format
     * @return mixed
     */
    public function castJson($value, string $format = '')
    {
        if (is_null($value)) {
            return $value;
        }

        $value = $this->decrypt($value);

        $formatParts = explode(':', $format, 2);

        $type = $formatParts[0] ?? '';
        $format = $formatParts[1] ?? '';

        if ($type !== '') {
            $value = Cast::castJson($value, $type, $format);
        }

        return $value;
    }

    /**
     * Decrypt the value.
     *
     * @param mixed $value
     * @return mixed
     */
    protected function decrypt($value)
    {
        $decrypted = ! is_string($value);

        while (! $decrypted) {
            try {
                $value = Crypt::decrypt($value, false);
                $decrypted = ! is_string($value);
            } catch (DecryptException $e) {
                $decrypted = true;
            }
        }

        return $value;
    }
}
