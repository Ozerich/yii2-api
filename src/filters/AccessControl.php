<?php

namespace blakit\api\filters;

use yii\base\ActionFilter;
use yii\di\Instance;
use yii\web\ForbiddenHttpException;
use yii\web\User;

class AccessControl extends ActionFilter
{
    /**
     * @var User|array|string the user object representing the authentication status or the ID of the user application component.
     * Starting from version 2.0.2, this can also be a configuration array for creating the object.
     */
    private $user = 'user';

    public $rules = [];

    /**
     * Initializes the [[rules]] array by instantiating rule objects from configurations.
     */
    public function init()
    {
        parent::init();

        $this->user = Instance::ensure($this->user, User::className());

        foreach ($this->rules as $i => $rule) {
            if (is_array($rule)) {
                $this->rules[$i] = \Yii::createObject(array_merge([
                    'class' => AccessControlRule::className()
                ], $rule));
            }
        }
    }

    /**
     * @param \yii\base\Action $action
     * @return bool
     */
    public function beforeAction($action)
    {
        $user = $this->user;
        $request = \Yii::$app->getRequest();


        /* @var $rule AccessControlRule */
        foreach ($this->rules as $rule) {
            if ($allow = $rule->allows($action, $user, $request)) {
                return true;
            } elseif ($allow === false) {
                if (isset($rule->denyCallback)) {
                    call_user_func($rule->denyCallback, $rule, $action);
                } elseif ($this->denyCallback !== null) {
                    call_user_func($this->denyCallback, $rule, $action);
                } else {
                    $this->denyAccess($user);
                }
                return false;
            }
        }

        $this->denyAccess($user);

        return false;
    }

    /**
     * Denies the access of the user.
     * The default implementation will redirect the user to the login page if he is a guest;
     * if the user is already logged, a 403 HTTP exception will be thrown.
     * @param User $user the current user
     * @throws ForbiddenHttpException if the user is already logged in.
     */
    protected function denyAccess($user)
    {
        if ($user->getIsGuest()) {
            $user->loginRequired();
        } else {
            throw new ForbiddenHttpException(\Yii::t('yii', 'You are not allowed to perform this action.'));
        }
    }
}