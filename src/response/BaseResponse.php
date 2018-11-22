<?php

namespace blakit\api\response;

use blakit\api\interfaces\DTO;
use yii\web\Response;

abstract class BaseResponse extends Response
{
    abstract protected function toJSON();

    public $format = Response::FORMAT_JSON;

    private function rec($array)
    {
        $result = [];

        foreach ($array as $ind => $item) {
            if (is_array($item)) {
                $result[$ind] = $this->rec($item);
            } else if ($item instanceof DTO) {
                $result[$ind] = $item->toJSON();
            } else {
                $result[$ind] = $item;
            }
        }

        return $result;
    }

    protected function prepare()
    {
        $this->data = $this->toJSON();

        if ($this->data instanceof BaseResponse) {
            $this->data = $this->data->toJSON();
        } else if ($this->data instanceof DTO) {
            $this->data = $this->data->toJSON();
        } else if (is_array($this->data)) {
            $this->data = $this->rec($this->data);
        }

        parent::prepare();
    }
}