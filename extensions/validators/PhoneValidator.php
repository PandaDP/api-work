<?php

namespace app\extensions\validators;

use yii\validators\Validator;

class PhoneValidator extends Validator
{
    public const COUNT_DIGITS_MIN = 10;
    public const COUNT_DIGITS_MAX = 15;

    /**
     * @param mixed $value
     * @return array|null
     */
    public function validateValue($value) : ?array
    {
        if (!preg_match('|^\+\d{' . self::COUNT_DIGITS_MIN . ',' . self::COUNT_DIGITS_MAX . '}$|', $value)) {
            return [$this->message ?: self::getErrorMessage(), []];
        }

        return null;
    }

    /**
     * @return string
     */
    public static function getErrorMessage() :string
    {
        return "Format +[country code][area code][phone number]. Must be between " . self::COUNT_DIGITS_MIN . "-" . self::COUNT_DIGITS_MAX . " digits";
    }
}