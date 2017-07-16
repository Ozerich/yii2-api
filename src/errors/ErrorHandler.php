<?php

namespace blakit\api\errors;

use blakit\api\request\InvalidRequestException;
use yii\base\ErrorException;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

class ErrorHandler extends \yii\web\ErrorHandler
{
    protected function renderException($exception)
    {
        if ($exception instanceof InvalidRequestException) {
            $response = \Yii::$app->getResponse();
            $response->setStatusCode(400);

            $errors = $exception->getErrors();

            $errors_by_field = [];
            foreach ($errors as $error) {
                if (!isset($errors_by_field[$error->field])) {
                    $errors_by_field[$error->field] = [];
                }
                $errors_by_field[$error->field][] = $error->error;
            }

            $response->data = [
                'errors' => $errors_by_field
            ];

            $response->send();
        } else if ($exception instanceof UnauthorizedHttpException) {
            $response = \Yii::$app->getResponse();
            $response->setStatusCode(401);
            $response->data = [
                'error' => 'Not Unauthorized'
            ];
            $response->send();

        } else if ($exception instanceof ForbiddenHttpException) {
            $response = \Yii::$app->getResponse();
            $response->setStatusCode(403);
            $response->data = [
                'error' => 'Forbidden'
            ];
            $response->send();

        } else if ($exception instanceof NotFoundHttpException) {
            $response = \Yii::$app->getResponse();
            $response->setStatusCode(404);
            $response->data = [
                'error' => $exception->getMessage()
            ];
            $response->send();
        } else if ($exception instanceof MethodNotAllowedHttpException) {
            $response = \Yii::$app->getResponse();
            $response->setStatusCode(405);
            $response->data = [
                'error' => 'Method not allowed',
                'allowed' => json_decode($exception->getMessage())
            ];
            $response->send();
        } else if ($exception instanceof ErrorException) {
            $response = \Yii::$app->getResponse();
            $response->setStatusCode(500);
            $response->data = [
                'error' => 'Internal Server Error'
            ];

            if (YII_DEBUG) {

                $debug = [
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile() . ';  Line ' . $exception->getLine(),
                    'stack-trace' => explode("\n", $exception->getTraceAsString())
                ];

                if ($exception instanceof \yii\db\Exception) {
                    $debug['error-info'] = $exception->errorInfo;
                }

                $response->data = array_merge($response->data, [
                    'debug' => $debug
                ]);
            }

            $response->send();
        } else {
            return parent::renderException($exception);
        }
    }
}