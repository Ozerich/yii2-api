<?php

namespace ozerich\api\response;

use ozerich\api\interfaces\DTO;
use yii\base\InvalidArgumentException;
use yii\base\Model;

class ModelResponse extends BaseResponse
{
    private $model;

    private $dtoClass;

    public function __construct(?Model $model, $dtoClass)
    {
        $this->model = $model;
        $this->dtoClass = $dtoClass;

        parent::__construct();
    }

    public function toJSON()
    {
        if ($this->model === null) {
            return ['model' => null];
        }

        /** @var DTO $model */
        $model = \Yii::createObject($this->dtoClass, [$this->model]);

        if (!$model instanceof DTO) {
            throw new InvalidArgumentException('dtoClass does not implements DTO interface');
        }

        return [
            'model' => $model->toJSON()
        ];
    }
}