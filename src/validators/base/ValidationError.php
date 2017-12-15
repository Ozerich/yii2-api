<?php

namespace blakit\api\validators\base;

class ValidationError
{
    private $error_code;

    private $error_message;

    public function __construct($code, $message, $params = [])
    {
        $this->error_code = $code;
        $this->error_message = $message;
    }

    public function translateMessage($params = [])
    {
        $this->error_message = \Yii::$app->getI18n()->format($this->error_message, $params, \Yii::$app->language);
    }

    public function toJSON()
    {
        return [
            'code' => $this->error_code,
            'message' => $this->error_message
        ];
    }
}