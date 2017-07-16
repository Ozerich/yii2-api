<?php

namespace blakit\api\controllers;

use yii\filters\Cors;
use yii\web\IdentityInterface;

class Controller extends \yii\web\Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => Cors::className(),
            ],
        ]);
    }

    /**
     * @return IdentityInterface|null
     */
    protected function getUser()
    {
        return \Yii::$app->user->getIdentity();
    }
}