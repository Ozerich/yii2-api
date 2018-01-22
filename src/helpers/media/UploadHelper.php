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
            self::correctImageOrientation($fullpath, $image->ext);
            $imageInfo = self::getImageInfo($fullpath);
            $image->setAttributes(array_merge($imageInfo, ['size' => $file->size]));
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
    public static function rotate($filename, $extension, $angle)
    {
        if ($extension == 'png') {
            $image = imagecreatefrompng($filename);
            $rotate = imagerotate($image, $angle, 0);
            imagepng($rotate, $filename);
        } else {
            $image = imagecreatefromjpeg($filename);
            $rotate = imagerotate($image, $angle, 0);
            imagejpeg($rotate, $filename);
        }
    }

    /**
     * Correct image orientation
     * @param $filename
     * @param $extenstion
     */
    public static function correctImageOrientation($filename, $extenstion)
    {
        if (function_exists('exif_read_data')) {
            $exif = exif_read_data($filename);
            if($exif && isset($exif['Orientation'])) {
                $orientation = $exif['Orientation'];
                if($orientation != 1){
                    $deg = 0;
                    switch ($orientation) {
                        case 3: $deg = 180; break;
                        case 6: $deg = 270; break;
                        case 8: $deg = 90;  break;
                    }
                    if ($deg) {
                        self::rotate($filename, $extenstion, $deg);
                    }
                }
            }
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
