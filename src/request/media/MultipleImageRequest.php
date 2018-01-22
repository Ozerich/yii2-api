<?php

namespace blakit\api\request\media;

use blakit\api\request\RequestModel;
use yii\web\UploadedFile;

class MultipleImageRequest extends RequestModel
{
    /** @var  UploadedFile[] */
    public $files;

    private $dynamicRules = [];

    public function rules()
    {
        return $this->dynamicRules;
    }

    public function setFileRule($maxSize, $maxFiles)
    {
        $this->dynamicRules[] = [['files'], 'file', 'skipOnEmpty' => false, 'maxSize' => $maxSize, 'extensions' => 'png, jpg', 'maxFiles' => $maxFiles];
    }
}