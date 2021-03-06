<?php

namespace ozerich\api\response;

use ozerich\api\interfaces\DTO;
use ozerich\api\Module;
use ozerich\api\utils\ApplicationVersion;
use yii\web\Response;

abstract class BaseResponse extends Response
{
    abstract protected function toJSON();

    public $format = Response::FORMAT_JSON;

    public function init()
    {
        parent::init();

        /** @var Module $module */
        $module = \Yii::$app->controller->module;

        if ($module instanceof Module) {
            if (!empty($module->responseEvents)) {
                foreach ($module->responseEvents as $event => $handlers) {
                    $handlers = is_array($handlers) ? $handlers : [$handlers];
                    foreach ($handlers as $handler) {
                        $this->on($event, $handler);
                    }
                }
            }
        }
    }

    private function rec($array)
    {
        $result = [];

        foreach ($array as $ind => $item) {
            if (is_array($item)) {
                $result[$ind] = $this->rec($item);
            } else if ($item instanceof DTO) {
                $result[$ind] = $item->toJSON();
                foreach ($result[$ind] as &$child) {
                    if (is_array($child)) {
                        $child = $this->rec($child);
                    }
                }
            } else {
                $result[$ind] = $item;
            }
        }

        return $result;
    }

    protected function prepare()
    {
        foreach (\Yii::$app->response->headers as $header => $value) {
            $this->headers->set($header, $value);
        }

        $this->headers->add('Version', ApplicationVersion::get());

        $this->data = $this->toJSON();

        if ($this->data instanceof BaseResponse) {
            $this->data = $this->data->toJSON();
        } else if ($this->data instanceof DTO) {
            $this->data = $this->data->toJSON();
        } else if (is_array($this->data)) {
            $this->data = $this->rec($this->data);
        }

        parent::prepare();
    }
}