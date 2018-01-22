<?php

namespace blakit\api\request\media;

use blakit\api\request\RequestModel;
use yii\web\UploadedFile;

class MultipleImageRequest extends RequestModel
{
    /** @var  UploadedFile[] */
    public $files;

    private $dynamicRules = [];

    private $maxFiles;

    private $maxSize;

    public function rules()
    {
        return $this->dynamicRules;
    }

    protected function setFileRule()
    {
        $this->dynamicRules[] = [['files'], 'file', 'skipOnEmpty' => false, 'maxSize' => $this->maxSize, 'extensions' => 'png, jpg', 'maxFiles' => $this->maxFiles];
    }

    public function setMaxSizeFile($maxSize)
    {
        $this->maxSize = $maxSize;
        if ($this->maxSize && $this->maxFiles) {
            $this->setFileRule();
        }
    }

    public function setMaxFiles($maxFiles)
    {
        $this->maxFiles = $maxFiles;
        if ($this->maxSize && $this->maxFiles) {
            $this->setFileRule();
        }
    }
}