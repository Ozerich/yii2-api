<?php

namespace blakit\api\validators;

use blakit\api\models\Image;
use yii\console\Application;
use yii\validators\Validator;

class UserOwnerImageValidator extends Validator
{
    private function checkImage($value)
    {
        $is_console = \Yii::$app instanceof Application;

        /** @var Image $image */
        $image = Image::findOne($value);

        if (!$image || ($is_console == false &&  $image->user_id != \Yii::$app->user->id)) {
            return [\Yii::t('errors', 'Доступ к картинке ID {id} запрещен', ['id' => $value]), []];
        }

        return true;
    }

    public function validateValue($value)
    {
        if (is_array($value)) {
            foreach ($value as $item) {
                $check = $this->checkImage($item);
                if($check !== true){
                    return $check;
                }
            }
        } else {
            $check = $this->checkImage($value);
            if($check !== true){
                return $check;
            }
        }

        return null;
    }
}