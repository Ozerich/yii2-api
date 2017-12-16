<?php

namespace blakit\api\request;


use blakit\api\Module;
use blakit\api\validators\base\ValidationError;

class RequestError
{
    public $field;

    public $error;

    public function __construct($field, $error, $error_code = '')
    {
        $this->field = $field;
        $this->error = $error instanceof ValidationError ? $error : new ValidationError($error_code, $error);
    }

    public function toJSON()
    {
        return $this->error->toJSON();
    }

}