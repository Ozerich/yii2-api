<?php

namespace blakit\api\controllers;

use blakit\api\filters\JwtAuth;

class SecuredController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['authenticator']);

        $behaviors['authenticator'] = [
            'class' => JwtAuth::className(),
            'except' => ['options'],
        ];

        return $behaviors;
    }
}