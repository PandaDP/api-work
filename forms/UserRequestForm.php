<?php

namespace app\forms;


use app\extensions\validators\PhoneValidator;
use yii\base\Model;
use yii\validators\EmailValidator;

class UserRequestForm extends Model
{
    public const TYPE_EMAIL = 'email';
    public const TYPE_PHONE = 'phone';

    public static $types = [
        self::TYPE_EMAIL,
        self::TYPE_PHONE
    ];

    public $key1;
    public $key2;
    public $value;

    public function rules()
    {
        return [
            [
                [
                    'key1',
                    'key2',
                    'value'
                ],
                'trim'
            ],
            [
                [
                    'key1',
                    'key2',
                    'value'
                ],
                'required'
            ],
            [
                'key1',
                'string',
                'length' => [1, 20]
            ],
            [
                'key2',
                'string',
                'length' => [1, 32]
            ],
            [
                'value',
                'string'
            ],
            [
                'key1',
                'validateTypes'
            ],
            [
                'key2',
                'validateContact',
            ],
            [
                'value',
                'validateValue'
            ]
        ];
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validateTypes($attribute, $params)
    {
        if (!self::fastCheck($this->key1, $this->key2)) {
            $this->addError('key1', 'Unknown key1 or not enough data');
        }
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validateContact($attribute, $params)
    {
        if (self::TYPE_EMAIL == $this->key1) {
            $validator = new EmailValidator();
            if (!$validator->validate($this->key2)) {
                $this->addError('key2', 'Incorrect email');
            }
        } elseif (self::TYPE_PHONE == $this->key1) {
            $validator = new PhoneValidator();
            if (!$validator->validate($this->key2)) {
                $this->addError(
                    'key2',
                    "Format +[country code][area code][phone number]. Must be between " . PhoneValidator::COUNT_DIGITS_MIN . "-" . PhoneValidator::COUNT_DIGITS_MAX . " digits"
                );
            }
        }
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validateValue($attribute, $params)
    {
        $data = json_decode($this->value, true);
        if (json_last_error() != JSON_ERROR_NONE) {
            $this->addError('value', json_last_error_msg());
        } elseif (!is_array($data)) {
            $this->addError('value', 'Not valid JSON');
        }
    }

    /**
     * @param string|null $type
     * @param string|null $typeValue
     * @return bool
     */
    public static function fastCheck(?string $type = '', ?string $typeValue = ''): bool
    {
        if (!empty($type) && !empty($typeValue) && in_array($type, self::$types)) {
            return true;
        } else {
            return false;
        }
    }
}