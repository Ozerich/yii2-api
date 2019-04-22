<?php

namespace ozerich\api\response;

class ArrayResponse extends BaseResponse
{
    private $models;

    private $dtoClass;

    public function __construct($models, $dtoClass)
    {
        $this->models = $models;
        $this->dtoClass = $dtoClass;

        parent::__construct();
    }

    public function toJSON()
    {
        return array_map(function ($model) {
            $model = \Yii::createObject($this->dtoClass, [$model]);
            return $model->toJSON();
        }, $this->models);
    }
}