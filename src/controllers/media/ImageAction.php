<?php

namespace blakit\api\controllers\media;

use blakit\api\helpers\media\UploadHelper;
use blakit\api\request\media\ImageRequest;
use yii\base\Action;

class ImageAction extends Action {
    public function run()
    {
        $request = new ImageRequest();
        return UploadHelper::run($request, 'file');
    }
}