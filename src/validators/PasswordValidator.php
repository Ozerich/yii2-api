<?php

namespace ozerich\api\validators;

use ozerich\api\validators\base\ValidationError;
use ozerich\api\validators\base\Validator;

class PasswordValidator extends Validator
{
    public $message;

    public $errorCode = 'FIELD_SIMPLE_PASSWORD';

    public function init()
    {
        $this->message = \Yii::t('validator', 'Too weak password');
    }

    public function validateAttribute($model, $attribute)
    {
        $value = $model->{$attribute};

        if (strlen($value) < 6) {
            $this->addError($model, $attribute, new ValidationError($this->errorCode, $this->formatMessage($this->message, ['attribute' => $attribute])));
        }

        return true;
    }
}