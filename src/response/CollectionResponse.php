<?php

namespace blakit\api\response;

use yii\data\BaseDataProvider;

class CollectionResponse extends BaseResponse
{
    private $dataProvider;

    private $dtoClass;

    public function __construct(BaseDataProvider $dataProvider, $dtoClass)
    {
        $this->dataProvider = $dataProvider;
        $this->dtoClass = $dtoClass;

        parent::__construct();
    }

    public function toJSON()
    {
        return [
            'collection' => array_map(function ($model) {
                $model = \Yii::createObject($this->dtoClass, [$model]);
                return $model->toJSON();
            }, $this->dataProvider->getModels()),
            'meta' => [
                'pageNumber' => $this->dataProvider->getPagination()->getPage() + 1,
                'pageSize' => $this->dataProvider->getPagination()->getPageSize(),
                'pagesCount' => $this->dataProvider->getPagination()->getPageCount(),
                'itemsCount' => $this->dataProvider->getTotalCount()
            ],
        ];
    }
}