<?php

namespace blakit\api\errors;

use blakit\api\Module;
use yii\i18n\MissingTranslationEvent;

class TranslationEventHandler
{
    public static function handleMissingTranslation(MissingTranslationEvent $event)
    {
        /** @var Module $module */
        $module = \Yii::$app->controller->module;

        if ($event->language != $module->defaultLocale) {
            \Yii::warning(
                'Missed localization "' . $event->message . '" for ' . $event->language . ' [' . $event->category . ']',
                'localization'
            );
        }
    }
}