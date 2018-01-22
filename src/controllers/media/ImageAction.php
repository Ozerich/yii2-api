<?php

namespace blakit\api\controllers\media;

use blakit\api\helpers\media\UploadHelper;
use blakit\api\request\media\ImageRequest;
use yii\base\Action;

class ImageAction extends Action {
    public $maxSize;

    public function run()
    {
        $this->maxSize = $this->maxSize ?? 5 * 1024 * 1024;

        $request = new ImageRequest();
        $request->setFileRule($this->maxSize);

        return UploadHelper::run($request, 'file');
    }
}