<?php

namespace blakit\api\actions;

use blakit\api\helpers\media\UploadHelper;
use blakit\api\request\media\ImageRequest;
use yii\base\Action;

class ImageAction extends Action {
    public $maxSize;

    public $extensions;

    public function run()
    {
        $request = new ImageRequest();

        if ($this->maxSize) $request->maxSize = $this->maxSize;
        if ($this->extensions) $request->extensions = $this->extensions;
        return [
            'image' => UploadHelper::run($request, 'file')
        ];
    }
}