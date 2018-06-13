<?php

namespace blakit\api\request;


use blakit\api\validators\base\ValidationError;

class RequestError
{
    public $field;

    public $error;

    public $path = null;

    public function __construct($field, $error, $error_code = '', $error_path = null)
    {
        $this->field = $field;
        $this->error = $error instanceof ValidationError ? $error : new ValidationError($error_code, $error);
        $this->path = $error_path;
    }

    public function toJSON()
    {
        $result = $this->error->toJSON();

        if($this->path !== null){
            $result = array_merge($result, ['path' => $this->path]);
        }

        return $result;
    }

}