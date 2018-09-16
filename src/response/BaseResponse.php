<?php

namespace blakit\api\response;

use yii\web\Response;

abstract class BaseResponse extends Response
{
    abstract protected function toJSON();

    public $format = Response::FORMAT_JSON;

    protected function prepare()
    {
        $this->data = $this->toJSON();

        parent::prepare();
    }
}