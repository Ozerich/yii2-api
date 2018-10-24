<?php

namespace blakit\api\response;

use yii\web\HttpException;

class BaseHttpException extends HttpException
{
    private $fields = [];

    public function addField($field, $value)
    {
        $this->fields[$field] = $value;
    }

    public function getFields()
    {
        return $this->fields;
    }
}