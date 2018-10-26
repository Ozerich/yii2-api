<?php

namespace blakit\api\request;

use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\ActiveRecord;
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

    protected function structures()
    {
        return [];
    }

    protected function ignoreFieldsIfNotSet()
    {
        return [];
    }

    public function init()
    {
        $models = $this->models();

        foreach ($models as $attr => $className) {
            $this->{$attr} = \Yii::createObject($className);
        }

        parent::init();
    }

    private $data = [];

    public function setModelId(ActiveRecord $model, $id)
    {
        $model->id = $id;
        $model->isNewRecord = false;
    }

    private $_error_pathes = [];

    public function addError($attribute, $error = '', $path = null)
    {
        parent::addError($attribute, $error);
        if (!isset($this->_error_pathes[$attribute])) {
            $this->_error_pathes[$attribute] = [];
        }
        $this->_error_pathes[$attribute][] = $path;
    }

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
        if ($data === null) {
            $data = $post ? \Yii::$app->request->post() : \Yii::$app->request->get();
        }

        $this->data = $data;

        foreach ($this->structures() as $model_field => $params) {
            if (isset($data[$model_field])) {
                $value = $data[$model_field];
                $is_array = isset($params['is_array']) && $params['is_array'];

                $items = $is_array ? $value : [$value];

                foreach ($items as &$item) {
                    if (!is_array($item)) {
                        continue;
                    }

                    /** @var Model $struct */
                    $struct = \Yii::createObject($params['model_class'], isset($params['config']) ? [$params['config']] : []);
                    foreach ($item as $param => $value) {
                        if ($struct->hasProperty($param)) {
                            $struct->{$param} = $value;
                        }
                    }
                    $item = $struct;
                }

                $this->{$model_field} = $is_array ? $items : array_shift($items);
                unset($data[$model_field]);
            }
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

        foreach ($this->structures() as $model_field => $params) {
            $value = $this->{$model_field};

            $items = is_array($value) ? $value : [$value];
            $is_array = isset($params['is_array']) && $params['is_array'];

            foreach ($items as $ind => $item) {
                if (!is_object($item)) {
                    $this->addError($model_field, \Yii::t('api_errors', 'Not valid structure (not object / associative array)'));
                    $result = false;
                    continue;
                }
                if ($item && !$item->validate()) {

                    $pathes = $item instanceof \blakit\api\base\Model ? $item->getErrorPathes() : [];

                    foreach ($item->getErrors() as $error_field => $field_errors) {
                        foreach ($field_errors as $_ind => $error) {
                            $path = isset($pathes[$error_field][$_ind]) ? $pathes[$error_field][$_ind] : null;
                            $error_path = ($is_array ? $ind . (empty($path) ? '' : '.') : '') . $path;
                            $this->addError($model_field, $error, empty($path) ? null : $error_path);
                        }
                    }

                    $result = false;
                }
            }
        }

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
                foreach ($field_errors as $ind => $error) {
                    $error_path = $this->_error_pathes[$field][$ind];
                    if (is_string($error)) {
                        $error = new RequestError($field, $error, null, $error_path);
                    } else {
                        $error = new RequestError($field, $error['message'], $error['code'], $error_path);
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