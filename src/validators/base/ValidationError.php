<?php

namespace blakit\api\validators\base;

use blakit\api\Module;

class ValidationError
{
    private $error_code;

    private $error_message;

    /**
     * @return Module
     */
    private function getModule()
    {
        return \Yii::$app->controller->module;
    }

    public function __construct($code, $message)
    {
        $this->error_code = $code;
        $this->error_message = $message;
    }

    public function translateMessage($params = [])
    {
        $this->error_message = \Yii::$app->getI18n()->format($this->error_message, $params, \Yii::$app->language);
    }

    public function getMessage()
    {
        return $this->error_message;
    }

    public function getErrorCode()
    {
        return $this->error_code ? $this->error_code : $this->getModule()->defaultErrorCode;
    }

    public function toJSON()
    {
        return $this->getModule()->enableErrorCodes ? [
            'code' => $this->getErrorCode(),
            'message' => $this->getMessage()
        ] : $this->getMessage();
    }
}