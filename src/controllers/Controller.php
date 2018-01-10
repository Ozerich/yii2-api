<?php

namespace blakit\api\controllers;

use blakit\api\Module;
use yii\filters\Cors;
use yii\web\HeaderCollection;
use yii\web\IdentityInterface;

class Controller extends \yii\web\Controller
{
    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        /** @var Module $module */
        $module = \Yii::$app->controller->module;

        if ($module->enableLocalization) {
            /** @var HeaderCollection $headers */
            $headers = \Yii::$app->request->headers;

            $language = mb_strtolower($headers->get('Accept-Language'));

            if (!in_array($language, $module->locales)) {
                if (strpos($language, '-') !== false) {
                    $language = substr($language, 0, strpos($language, '-'));
                    if (!in_array($language, $module->locales)) {
                        $language = $module->defaultLocale;
                    }
                }
                else{
                    $language = $module->defaultLocale;
                }
            }

            \Yii::$app->language = mb_strtolower($language);
        }

        return parent::beforeAction($action);
    }

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