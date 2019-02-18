<?php

namespace ozerich\api\errors;

use ozerich\api\request\InvalidRequestException;
use ozerich\api\response\BaseHttpException;
use ozerich\api\utils\ApplicationVersion;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

class ErrorHandler extends \yii\web\ErrorHandler
{
    protected function renderException($exception)
    {
        $response = \Yii::$app->getResponse();

        $response->headers->add('Version', ApplicationVersion::get());

        if ($exception instanceof InvalidRequestException) {
            $response->setStatusCode(400);

            $errors = $exception->getErrors();

            $errors_by_field = [];
            foreach ($errors as $error) {
                if (!isset($errors_by_field[$error->field])) {
                    $errors_by_field[$error->field] = [];
                }
                $errors_by_field[$error->field][] = $error->toJSON();
            }

            $response->data = [
                'errors' => $errors_by_field
            ];

            $meta = $exception->getMetaParams();
            if (!empty($meta)) {
                $response->data['meta'] = $meta;
            }

            $response->send();
        } else if ($exception instanceof UnauthorizedHttpException) {
            $response->setStatusCode(401);
            $response->data = [
                'error' => empty($exception->getMessage()) ? \Yii::t('api_errors', 'Unauthorized') : $exception->getMessage()
            ];
            $response->send();

        } else if ($exception instanceof ForbiddenHttpException) {
            $response->setStatusCode(403);
            $response->data = [
                'error' => empty($exception->getMessage()) ? \Yii::t('api_errors', 'Forbidden') : $exception->getMessage()
            ];
            $response->send();

        } else if ($exception instanceof NotFoundHttpException) {
            $response->setStatusCode(404);
            $response->data = [
                'error' => $exception->getMessage()
            ];
            $response->send();
        } else if ($exception instanceof MethodNotAllowedHttpException) {
            $response->setStatusCode(405);
            $response->data = [
                'error' => \Yii::t('api_errors', 'Method not allowed'),
                'allowed' => json_decode($exception->getMessage())
            ];
            $response->send();
        } else if ($exception instanceof BaseHttpException) {
            $response->setStatusCode($exception->statusCode);
            $response->data = [
                'error' => $exception->getMessage(),
                'data' => $exception->getFields()
            ];
            $response->send();
        } else if ($exception instanceof HttpException) {
            $response->setStatusCode($exception->statusCode);
            $response->data = [
                'error' => $exception->getMessage()
            ];
            $response->send();
        } else if ($exception instanceof ErrorException or $exception instanceof Exception or $exception instanceof \Error) {
            $response->setStatusCode(500);
            $response->data = [
                'error' => \Yii::t('api_errors', 'Internal Server Error')
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
            parent::renderException($exception);
        }
    }
}