<?php

namespace ozerich\api\base;

class Model extends \yii\base\Model
{
    private $_errors = [];

    private $_error_pathes = [];

    public function addError($attribute, $error = '', $error_path_index = null)
    {
        $this->_errors[$attribute][] = $error;
        $this->_error_pathes[$attribute][] = $error_path_index;
    }

    public function getErrors($attribute = null)
    {
        if ($attribute === null) {
            return $this->_errors === null ? [] : $this->_errors;
        }

        return isset($this->_errors[$attribute]) ? $this->_errors[$attribute] : [];
    }

    public function hasErrors($attribute = null)
    {
        return $attribute === null ? !empty($this->_errors) : isset($this->_errors[$attribute]);
    }

    public function getErrorPathes()
    {
        return $this->_error_pathes;
    }
}