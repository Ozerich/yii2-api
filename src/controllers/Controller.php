<?php

namespace ozerich\api\controllers;

use ozerich\api\Module;
use yii\filters\Cors;
use yii\web\HeaderCollection;
use yii\web\IdentityInterface;

class Controller extends \yii\web\Controller
{
    public $enableCsrfValidation = false;

    protected function getAllowedOrigins()
    {
        $module = \Yii::$app->controller->module;

        if ($module instanceof Module) {
            return $module->allowedOrigins;
        }
    }

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
                } else {
                    $language = $module->defaultLocale;
                }
            }

            \Yii::$app->language = mb_strtolower($language);
        }

        return parent::beforeAction($action);
    }

    public function behaviors()
    {
        $origins = $this->getAllowedOrigins();

        if (empty($origins)) {
            return parent::behaviors();
        }

        return array_merge(parent::behaviors(), [
            [
                'class' => Cors::class,
                'cors' => [
                    'Origin' => is_array($origins) ? $origins : [$origins],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Allow-Credentials' => null,
                    'Access-Control-Max-Age' => 86400,
                    'Access-Control-Expose-Headers' => [],
                ]
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