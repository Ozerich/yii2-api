<?php

namespace blakit\api\actions;

use blakit\api\helpers\media\UploadHelper;
use blakit\api\request\media\MultipleImageRequest;
use yii\base\Action;

class MultipleImageAction extends Action {
    public $maxSize;

    public $maxFiles;

    public $extensions;

    public function run()
    {
        $request = new MultipleImageRequest();

        if ($this->maxSize) $request->maxSize = $this->maxSize;
        if ($this->extensions) $request->extensions = $this->extensions;
        if ($this->maxFiles) $request->maxFiles = $this->maxFiles;

        return [
            'images' => UploadHelper::runMultiple($request, 'files')
        ];
    }
}