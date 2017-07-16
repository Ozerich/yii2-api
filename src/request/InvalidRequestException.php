<?php

namespace blakit\api\request;

class InvalidRequestException extends \Exception
{
    /** @var RequestError[] */
    private $errors = [];

    public function __construct($error = null)
    {
        if ($error) {
            if ($error instanceof RequestError) {
                $this->addError($error);
            } else {
                $this->addError(new RequestError('*', $error));
            }
        }
    }

    public function addError($error)
    {
        $this->errors[] = $error;
    }

    /**
     * @return RequestError[]
     */
    public function getErrors()
    {
        return $this->errors;
    }
}