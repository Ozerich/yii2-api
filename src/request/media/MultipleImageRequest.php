<?php

namespace blakit\api\request\media;

use blakit\api\request\RequestModel;
use yii\web\UploadedFile;

class MultipleImageRequest extends RequestModel
{
    /** @var  UploadedFile[] */
    public $files;

    public $maxFiles = 10;

    public $maxSize = 5 * 1024 * 1024; // 5Mb

    public $extensions = 'png, jpg, gif, bmp';

    public function rules()
    {
        return [
            [['files'], 'file', 'skipOnEmpty' => false, 'maxSize' => $this->maxSize, 'extensions' => $this->extensions, 'maxFiles' => $this->maxFiles]
        ];
    }
}