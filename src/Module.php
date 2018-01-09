<?php

namespace blakit\api;

use blakit\api\constants\ErrorCode;
use blakit\api\errors\ErrorHandler;
use yii\i18n\I18N;

class Module extends \yii\base\Module
{
    public $enableErrorCodes = false;

    public $defaultErrorCode = ErrorCode::UNKNOWN_ERROR;

    public $enableLocalization = false;

    public $locales = ['ru', 'en'];

    public $defaultLocale = 'en';

    public function init()
    {
        $this->initI18n();
        $this->initErrorHandler();
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

    public function initI18n()
    {
        $translations = [
            '*' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'ru-RU',
                'on missingTranslation' => [
                    'blakit\api\errors\TranslationEventHandler',
                    'handleMissingTranslation'
                ]
            ]
        ];

        $isset_i18n = false;
        foreach ($this->components as $key => $value) {
            if (is_a($value, I18N::class)) {
                $isset_i18n = true;
                $this->components[$key]->translations = array_merge_recursive_ex($this->components[$key]->translations, $translations);
            }
            if ($isset_i18n) break;
        }

        if (!$isset_i18n) {
            $this->setComponents(['i18n' => [
                'class' => I18N::className(),
                'translations' => $translations,
            ]]);
        }
    }

    public function initErrorHandler()
    {
        $handler = new ErrorHandler();
        \Yii::$app->set('errorHandler', $handler);
        $handler->register();
    }
}