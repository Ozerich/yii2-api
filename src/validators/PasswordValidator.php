<?php

namespace blakit\api\validators;

use blakit\api\constants\ErrorCode;
use blakit\api\validators\base\ValidationError;
use blakit\api\validators\base\Validator;

class PasswordValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $value = $model->{$attribute};

        if (strlen($value) < 6) {
            $this->addError($model, $attribute, new ValidationError(ErrorCode::FIELD_SIMPLE_PASSWORD, \Yii::t('errors', 'Пароль должен состоять из минимум 6 символов')));
        }

        return true;
    }
}