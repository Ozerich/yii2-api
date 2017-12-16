<?php

namespace blakit\api;

use blakit\api\constants\ErrorCode;
use blakit\api\errors\ErrorHandler;

class Module extends \yii\base\Module
{
    public $enableErrorCodes = false;

    public $defaultErrorCode = ErrorCode::UNKNOWN_ERROR;

    public function init()
    {
        $handler = new ErrorHandler();
        \Yii::$app->set('errorHandler', $handler);
        $handler->register();

        parent::init();
    }

    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
            ],
        ];
    }
}