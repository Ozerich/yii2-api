<?php

namespace blakit\api\validators;

use blakit\api\validators\base\ValidationError;
use blakit\api\validators\base\Validator;

class ModelFieldValueValidator extends Validator
{
    public $modelAttribute;

    public $modelClass;

    public $compareValue;

    public $errorOnNull = false;

    public $errorCode = 'FIELD_FORBIDDEN_VALUE';

    public function init()
    {
        parent::init();

        $this->message = \Yii::t('validator', 'Access denied');
    }

    private function getModel($id)
    {
        $className = $this->modelClass;

        $model = $className::findOne($id);
        return $model;
    }

    private function error($value)
    {
        return [
            new ValidationError(
                $this->errorCode,
                $this->message
            ), ['id' => $value]
        ];
    }


    private function check($value)
    {
        $model = $this->getModel($value);

        if (!$model) {
            return $this->error($value);
        }

        $value = $model->{$this->modelAttribute};

        if ($value === null) {
            if ($this->errorOnNull) {
                return $this->error($value);
            }
        } else if ($value != $this->compareValue) {
            return $this->error($value);
        }

        return true;
    }

    public function validateValue($value)
    {
        if (is_array($value)) {
            foreach ($value as $item) {
                $check = $this->check($item);
                if ($check !== true) {
                    return $check;
                }
            }
        } else {
            $check = $this->check($value);
            if ($check !== true) {
                return $check;
            }
        }

        return null;
    }
}