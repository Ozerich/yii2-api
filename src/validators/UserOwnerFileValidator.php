<?php

namespace blakit\api\validators;

class UserOwnerFileValidator extends ModelFieldValueValidator
{
    public $ownerId = null;

    public $modelClass = 'blakit\filestorage\models\File';

    public $modelAttribute = 'user_id';

    public $errorCode = 'FIELD_FILE_ACCESS_DENIED';

    public $message;

    public function init()
    {
        parent::init();

        $this->compareValue = $this->ownerId ? $this->ownerId : \Yii::$app->user->id;

        $this->message = \Yii::t('validator', 'Access for file ID {id} denied');
    }
}