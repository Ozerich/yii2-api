<?php

namespace blakit\api\controllers\media;

use blakit\api\helpers\media\UploadHelper;
use blakit\api\request\media\MultipleImageRequest;
use yii\base\Action;

class MultipleImageAction extends Action {
    public function run()
    {
        $request = new MultipleImageRequest();
        return UploadHelper::runMultiple($request, 'files');
    }
}