<?php

namespace blakit\api\request;

use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\validators\Validator;

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

    protected function ignoreFieldsIfNotSet()
    {
        return [];
    }

    private $data = [];

    /**
     * @param $attribute
     * @return bool
     */
    public function issetAttribute($attribute)
    {
        return is_array($this->data) && isset($this->data[$attribute]);
    }

    public function load($data = null, $formName = null, $post = true)
    {
        $data = $post ? \Yii::$app->request->post() : \Yii::$app->request->get();

        $this->data = $data;

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

    private function needCheckAttribute($attribute)
    {
        return !in_array($attribute, $this->ignoreFieldsIfNotSet()) || isset($this->data[$attribute]);
    }

    public function activeAttributes()
    {
        return array_filter(parent::activeAttributes(), function ($attribute) {
            return $this->needCheckAttribute($attribute);
        });
    }

    public function validate($attributeNames = null, $clearErrors = true)
    {
        $result = parent::validate($attributeNames, $clearErrors);

        foreach ($this->modelFields() as $model_field => $model_fields) {
            /** @var Model $model */
            $model = $this->{$model_field};

            $model_fields = array_filter($model_fields, function ($field) {
                return $this->needCheckAttribute($field);
            });

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
                    if (is_string($error)) {
                        $error = new RequestError($field, $error);
                    } else {
                        $error = new RequestError($field, $error['message'], $error['code']);
                    }

                    $ex->addError($error);
                }
            }

            throw $ex;
        }
    }

    /**
     * Creates validator objects based on the validation rules specified in [[rules()]].
     * Unlike [[getValidators()]], each time this method is called, a new list of validators will be returned.
     * @return \ArrayObject validators
     * @throws InvalidConfigException if any validation rule configuration is invalid
     */
    public function createValidators()
    {
        $validators = new \ArrayObject();
        foreach ($this->rules() as $rule) {
            if ($rule instanceof Validator) {
                $validators->append($rule);
            } elseif (is_array($rule) && isset($rule[0], $rule[1])) { // attributes, validator type
                $validator = \blakit\api\validators\base\Validator::createValidator($rule[1], $this, (array)$rule[0], array_slice($rule, 2));
                $validators->append($validator);
            } else {
                throw new InvalidConfigException('Invalid validation rule: a rule must specify both attribute names and validator type.');
            }
        }

        return $validators;
    }
}