<?php

namespace blakit\api;

if (!function_exists('array_merge_recursive_ex')) {
    function array_merge_recursive_ex(array & $array1, array & $array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => & $value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = array_merge_recursive_ex($merged[$key], $value);
            } else if (is_numeric($key)) {
                if (!in_array($value, $merged))
                    $merged[] = $value;
            } else
                $merged[$key] = $value;
        }

        return $merged;
    }
}

use blakit\api\constants\ErrorCode;
use blakit\api\errors\ErrorHandler;
use yii\i18n\I18N;
use yii\i18n\PhpMessageSource;
use yii\web\Response;

class Module extends \yii\base\Module
{
    public $allowedOrigins = [];

    public $enableErrorCodes = false;

    public $defaultErrorCode = 'UNKNOWN_ERROR';

    public $enableLocalization = false;

    public $locales = ['ru', 'en'];

    public $defaultLocale = 'en';

    public function init()
    {
        parent::init();

        $this->initRequestAndResponse();
        $this->initI18n();
        $this->initErrorHandler();
    }

    private function initRequestAndResponse()
    {
        \Yii::$app->request->enableCookieValidation = false;
        \Yii::$app->response->format = Response::FORMAT_JSON;
        \Yii::$app->request->parsers['application/json'] = 'yii\web\JsonParser';
    }

    public function initI18n()
    {
        $new_i18n_config = [
            'class' => I18N::class,
            'translations' => [
                'api_errors' => [
                    'class' => PhpMessageSource::class,
                    'sourceLanguage' => 'en',
                    'basePath' => __DIR__ . '/messages'
                ]
            ]
        ];

        if ($this->enableLocalization) {
            $new_i18n_config['translations']['*'] = [
                'class' => PhpMessageSource::class,
                'on missingTranslation' => [
                    'blakit\api\errors\TranslationEventHandler',
                    'handleMissingTranslation'
                ]
            ];
        }

        foreach (\Yii::$app->components as $key => $value) {
            $check = is_array($value) ? (isset($value['class']) && $value['class'] == I18N::class) : $value instanceof I18N;

            if ($check) {
                $i18n_original_config = \Yii::$app->components[$key];
                $new_i18n_config = array_merge_recursive_ex($i18n_original_config, $new_i18n_config);

                return \Yii::$app->set($key, $new_i18n_config);
            }
        }

        return \Yii::$app->set('i18n', $new_i18n_config);
    }

    public function initErrorHandler()
    {
        $handler = new ErrorHandler();

        \Yii::$app->set('errorHandler', $handler);

        $handler->register();
    }
}