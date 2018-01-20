<?php

namespace blakit\api\request\media;

use blakit\api\request\RequestModel;
use yii\web\UploadedFile;

class MultipleImageRequest extends RequestModel
{
    /** @var  UploadedFile[] */
    public $files;

    public function rules()
    {
        return [
            [['files'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg', 'maxFiles' => 10],
        ];
    }
}