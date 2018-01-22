<?php

namespace blakit\api\actions;

use blakit\api\helpers\media\UploadHelper;
use blakit\api\request\media\MultipleImageRequest;
use yii\base\Action;

class MultipleImageAction extends Action {
    public $maxSize;

    public $maxFiles;

    public function run()
    {
        $this->maxSize = $this->maxSize ?? 5 * 1024 * 1024;
        $this->maxFiles = $this->maxFiles ?? 10;

        $request = new MultipleImageRequest();
        $request->setMaxSizeFile($this->maxSize);
        $request->setMaxFiles($this->maxFiles);

        return UploadHelper::runMultiple($request, 'files');
    }
}