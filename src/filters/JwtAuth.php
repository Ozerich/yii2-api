<?php

namespace blakit\api\filters;

use yii\filters\auth\AuthMethod;

class JwtAuth extends AuthMethod
{
    public $allowGuestActions = [];

    public function authenticate($user, $request, $response)
    {
        $action_id = $this->getActionId(\Yii::$app->requestedAction);
        $allowGuest = in_array($action_id, $this->allowGuestActions);
        $authHeader = $request->getHeaders()->get('Authorization');

        if ($authHeader !== null && preg_match('/^JWT\s+(.*?)$/', $authHeader, $matches)) {
            $identity = $user->loginByAccessToken($matches[1], get_class($this));
            if ($identity === null) {
                if(!$allowGuest){
                    $this->handleFailure($response);
                }
            }
            return $identity;
        }

        return true;
    }
}