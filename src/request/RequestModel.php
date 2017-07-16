<?php

namespace blakit\api\request;

use yii\base\Model;

class RequestModel extends Model
{
    protected function models()
    {
        return [];
    }

    protected function modelFields()
    {
        return [];
    }

    public function load($data = null, $formName = null)
    {
        $data = \Yii::$app->request->post();

        $models = $this->models();

        foreach ($models as $model_field => $model_class) {
            $this->{$model_field} = \Yii::createObject($model_class);
        }


        foreach ($this->modelFields() as $model_field => $model_fields) {
            foreach ($model_fields as $field) {
                $this->{$model_field}->{$field} = isset($data[$field]) ? $data[$field] : '';
            }
        }

        $result = parent::load($data, '');

        $this->validate();

        return $result;
    }


    public function validate($attributeNames = null, $clearErrors = true)
    {
        $result = parent::validate($attributeNames, $clearErrors);

        foreach ($this->modelFields() as $model_field => $model_fields) {

            /** @var Model $model */
            $model = $this->{$model_field};

            if (!$model->validate($model_fields)) {

                foreach ($model->getErrors() as $error_field => $field_errors) {
                    foreach ($field_errors as $error) {
                        $this->addError($error_field, $error);
                    }
                }

                $result = false;
            }
        }
        if (!$result) {
            $errors = $this->getErrors();

            $ex = new InvalidRequestException();

            foreach ($errors as $field => $field_errors) {
                foreach ($field_errors as $error) {
                    $ex->addError(new RequestError($field, $error));
                }
            }

            throw $ex;
        }
    }
}