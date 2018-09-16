<?php

namespace blakit\api\controllers;

use blakit\api\filters\JwtAuth;

class SecuredController extends Controller
{
    protected $allowGuestActions = [];

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['authenticator']);

        $behaviors['authenticator'] = [
            'class' => JwtAuth::class,
            'except' => ['options'],
            'allowGuestActions' => $this->allowGuestActions
        ];

        return $behaviors;
    }
}