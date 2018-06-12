<?php

namespace blakit\api\request;


use blakit\api\validators\base\ValidationError;

class RequestError
{
    public $field;

    public $error;

    public $index = null;

    public function __construct($field, $error, $error_code = '', $error_index = null)
    {
        $this->field = $field;
        $this->error = $error instanceof ValidationError ? $error : new ValidationError($error_code, $error);
        $this->index = $error_index;
    }

    public function toJSON()
    {
        $result = $this->error->toJSON();

        if($this->index !== null){
            $result = array_merge($result, ['index' => $this->index]);
        }

        return $result;
    }

}