<?php

namespace blakit\api\helpers\media;

use blakit\api\models\Image;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Class UploadHelper
 * @package blakit\api\helpers\media
 */
class UploadHelper
{
    /**
     * @param Model $model
     * @param string $attribute
     * @param string $filename_prefix
     * @return bool|string
     */
    public static function run(&$model, $attribute, $filename_prefix = '')
    {
        $result = false;
        /** @var UploadedFile $file */
        $file = $model->{$attribute} = UploadedFile::getInstanceByName($attribute);
        $model->validate();
        if ($file) {
            /** @var Image $image */
            $image = new Image();
            $image->name = $filename_prefix . Yii::$app->security->generateRandomString(32);
            $image->ext = $file->getExtension();
            if ($file->saveAs($image->prepareMkdir()->getSystemPath())) {
                $image->save();
                $result = $image->toJSON();
            } else {
                $result = [
                    'error' => true,
                    'message' => $file->error,
                ];
            }
        }
        return $result;
    }

    /**
     * @param Model $model
     * @param string $attribute
     * @param string $filename_prefix
     * @return bool|string
     */
    public static function runMultiple(&$model, $attribute, $filename_prefix = '')
    {
        $result = [];
        /** @var UploadedFile[] $files */
        $files = $model->{$attribute} = UploadedFile::getInstancesByName($attribute);
        $model->validate();
        /** @var UploadedFile $file */
        foreach ($files as $file) {
            /** @var Image $image */
            $image = new Image();
            $image->name = $filename_prefix . Yii::$app->security->generateRandomString(32);
            $image->ext = $file->getExtension();
            if ($file->saveAs($image->prepareMkdir()->getSystemPath())) {
                $image->save();
                $result[] = $image->toJSON();
            } else {
                $result[] = [
                    'error' => true,
                    'message' => $file->error,
                ];
            }
        }
        return $result;
    }
}
