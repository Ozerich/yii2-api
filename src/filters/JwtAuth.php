<?php

namespace blakit\api\filters;

use yii\filters\auth\AuthMethod;

class JwtAuth extends AuthMethod
{
    public $allowGuestActions = [];

    public function authenticate($user, $request, $response)
    {
        $authHeader = $request->getHeaders()->get('Authorization');

        if ($authHeader !== null && preg_match('/^JWT\s+(.*?)$/', $authHeader, $matches)) {
            $identity = $user->loginByAccessToken($matches[1], get_class($this));
            if ($identity === null) {
                $action_id = $this->getActionId(\Yii::$app->requestedAction);
                if (in_array($action_id, $this->allowGuestActions)) {
                } else {
                    $this->handleFailure($response);
                }
            }
            return $identity;
        }

        return null;
    }
}