<?php

namespace blakit\api\validators;

use blakit\api\validators\base\ValidationError;
use blakit\api\validators\base\Validator;
use libphonenumber\NumberParseException;

class PhoneValidator extends Validator
{
    public $message;

    public $errorCode = 'FIELD_INVALID_PHONE';

    public function init()
    {
        $this->message = \Yii::t('validator', 'Invalid phone');
    }

    public static function check($value)
    {
        if (empty($value)) {
            return true;
        }

        if ($value[0] != '+') {
            $value = '+' . $value;
        }

        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();

        try {
            $phone = $phoneUtil->parse($value);
        } catch (NumberParseException $ex) {
            return false;
        }

        if ($phoneUtil->isValidNumber($phone) == false) {
            return false;
        }

        return true;
    }

    public function validateValue($value)
    {
        if ($value[0] != '+') {
            $value = '+' . $value;
        }

        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();

        try {
            $phone = $phoneUtil->parse($value);
        } catch (NumberParseException $ex) {
            return [new ValidationError($this->errorCode, $this->message), []];
        }

        if ($phoneUtil->isValidNumber($phone) == false) {
            return [new ValidationError($this->errorCode, $this->message), []];
        }

        return [];
    }
}