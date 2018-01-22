<?php

namespace blakit\api\request\media;

use blakit\api\request\RequestModel;
use yii\web\UploadedFile;

class ImageRequest extends RequestModel
{
    /** @var  UploadedFile */
    public $file;

    public $maxSize = 5 * 1024 * 1024; // 5Mb

    public $extensions = 'png, jpg, gif, bmp';

    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false, 'maxSize' => $this->maxSize, 'extensions' => $this->extensions]
        ];
    }
}