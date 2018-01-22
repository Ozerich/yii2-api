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
            $result = self::proccess($file, $filename_prefix);
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
            $result[] = self::proccess($file, $filename_prefix);
        }
        return $result;
    }

    /**
     * @param UploadedFile $file
     * @param string $filename_prefix
     * @return array
     * @throws \yii\base\Exception
     */
    public static function proccess($file, $filename_prefix = '')
    {
        $result = [];
        /** @var Image $image */
        $image = new Image();
        $image->name = $filename_prefix . Yii::$app->security->generateRandomString(32);
        $image->ext = $file->getExtension();
        $fullpath = $image->prepareMkdir()->getSystemPath();
        if ($file->saveAs($fullpath)) {
            $imageInfo = self::getImageInfo($fullpath);
            $needRotate = $imageInfo['width'] > $imageInfo['height'];
            if ($needRotate) {
                self::rotate($fullpath, $image->ext);
            }
            $image->setAttributes([
                'width' => $needRotate ? $imageInfo['height'] : $imageInfo['width'],
                'height' => $needRotate ? $imageInfo['width'] : $imageInfo['height'],
                'mime' => $imageInfo['mime'],
                'size' => $file->size,
            ]);
            $image->save();
            $result = $image->toJSON();
        } else {
            $result = [
                'error' => true,
                'message' => $file->error,
            ];
        }
        return $result;
    }

    /**
     * Rotate image
     * @param string $filename
     * @param string $extension
     */
    public static function rotate($filename, $extension)
    {
        if ($extension == 'png') {
            $image = imagecreatefrompng($filename);
            $rotate = imagerotate($image, 90, 0);
            imagepng($rotate, $filename);
        } else {
            $image = imagecreatefromjpeg($filename);
            $rotate = imagerotate($image, 90, 0);
            imagejpeg($rotate, $filename);
        }
    }

    /**
     * @param string $filename
     * @return array
     */
    public static function getImageInfo($filename)
    {
        $imageinfo = getimagesize($filename);
        return [
            'width' => $imageinfo[0],
            'height' => $imageinfo[1],
            'mime' => $imageinfo['mime']
        ];
    }
}
