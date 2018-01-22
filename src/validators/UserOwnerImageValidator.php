<?php

namespace blakit\api\validators;

use blakit\api\models\Image;
use yii\validators\Validator;

class UserOwnerImageValidator extends Validator
{
    public function validateValue($value)
    {
        if (is_array($value)) {
            foreach ($value as $item) {
                /** @var Image $image */
                $image = Image::findOne($item);
                if (!$image || $image->user_id != \Yii::$app->user->id) {
                    return [\Yii::t('errors', 'Доступ к картинке ID '.$item.' запрещен'), []];
                }
            }
        } else {
            /** @var Image $image */
            $image = Image::findOne($value);
            if (!$image || $image->user_id != \Yii::$app->user->id) {
                return [\Yii::t('errors', 'Доступ к картинке ID '.$value.' запрещен'), []];
            }
        }

        return null;
    }
}