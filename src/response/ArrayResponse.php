<?php

namespace ozerich\api\response;

class ArrayResponse extends BaseResponse
{
    private $models;

    private $dtoClass;

    private $dtoClassParams;

    public function __construct($models, $dtoClass, $dtoClassParams = [])
    {
        $this->models = $models;
        $this->dtoClass = $dtoClass;
        $this->dtoClassParams = $dtoClassParams;

        parent::__construct();
    }

    public function toJSON()
    {
        return array_map(function ($model) {
            $model = \Yii::createObject($this->dtoClass, [$model]);

            if (is_array($this->dtoClassParams)) {
                foreach ($this->dtoClassParams as $param => $value) {
                    if (isset($model->{$param})) {
                        $model->{$param} = $value;
                    }
                }
            }

            return $model->toJSON();
        }, $this->models);
    }
}