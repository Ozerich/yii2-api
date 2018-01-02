<?php

namespace blakit\api\errors;

use yii\i18n\MissingTranslationEvent;

class TranslationEventHandler
{
    public static function handleMissingTranslation(MissingTranslationEvent $event)
    {
        \Yii::warning('Missed localization "' . $event->message . '" for ' . $event->language . ' [' . $event->category . ']', 'localization');
    }
}