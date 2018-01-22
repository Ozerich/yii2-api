<?php

namespace blakit\api\request\media;

use blakit\api\request\RequestModel;
use yii\web\UploadedFile;

class ImageRequest extends RequestModel
{
    /** @var  UploadedFile */
    public $file;

    private $dynamicRules = [];

    public function rules()
    {
        return $this->dynamicRules;
    }

    public function setFileRule($maxSize)
    {
        $this->dynamicRules[] = [['file'], 'file', 'skipOnEmpty' => false, 'maxSize' => $maxSize, 'extensions' => 'png, jpg'];
    }
}