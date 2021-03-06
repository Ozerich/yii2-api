<?php

namespace ozerich\api\filters;

use yii\base\Action;
use yii\base\Component;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\Request;
use yii\web\UnauthorizedHttpException;
use yii\web\User;

class AccessControlRule extends Component
{
    public $action;

    public $verbs;

    public $roles;

    public $allowGuest = false;

    public function init()
    {
        $this->verbs = $this->verbs ? (is_array($this->verbs) ? $this->verbs : [$this->verbs]) : [];
        $this->roles = $this->roles ? (is_array($this->roles) ? $this->roles : [$this->roles]) : [];

        $this->verbs = array_map(function ($method) {
            return strtoupper($method);
        }, $this->verbs);

        $this->roles = array_map(function ($method) {
            return strtoupper($method);
        }, $this->roles);
    }

    public function allows($action, $user, Request $request)
    {
        if ($this->matchAction($action)) {
            if (!$this->matchVerb($request->getMethod())) {
                throw new MethodNotAllowedHttpException(json_encode($this->verbs));
            }
            if (!$this->matchRole($user)) {
                throw new ForbiddenHttpException();
            }

            return true;
        } else {
            return null;
        }
    }


    /**
     * @param $user
     * @return bool
     * @throws UnauthorizedHttpException
     */
    protected function matchRole($user)
    {
        if (empty($this->roles)) {
            return true;
        }

        if ($user->isGuest) {
            if (!$this->allowGuest) {
                throw new UnauthorizedHttpException('Your request was made with invalid credentials.');
            }

            return true;
        }

        return in_array(strtoupper($user->getIdentity()->role), $this->roles);
    }

    /**
     * @param Action $action the action
     * @return bool whether the rule applies to the action
     */
    protected function matchAction($action)
    {
        return $this->action == $action->id;
    }

    /**
     * @param string $verb the request method.
     * @return bool whether the rule applies to the request
     */
    protected function matchVerb($verb)
    {
        return empty($this->verbs) || in_array(strtoupper($verb), array_map('strtoupper', $this->verbs), true);
    }
}