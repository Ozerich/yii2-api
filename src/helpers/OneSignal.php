<?php

namespace blakit\api\helpers;


class OneSignal
{

    /**
     * Отправка Push уведомлений через OneSignal
     *
     * В конфигах необходимо указать App_ID и REST_API_key
     * \Yii::$app->params['oneSignal']['App_ID']
     * \Yii::$app->params['oneSignal']['REST_API_key']
     *
     * Мобильщику не забывать после подписки выставлять TAG userId с id пользователя:
            enum Key: String {
                case userId = “userId”
            }

            func sendTagOneSignal() {
                let id = UserDefaultsService().getUser().id
                OneSignal.sendTag(Key.userId.rawValue, value: “\(id)“)
            }

            func deleteOneSignalTag() {
                OneSignal.deleteTag(Key.userId.rawValue)
            }
     * Именно по этому тэгу OneSignal рассылает сообщения на все устройтсва пользователя
     *
     * @param integer $user_id
     * @param string $text
     *
     *
     * @return mixed
     */
    public static function sendPush($user_id, $text){

        $content = [
            "en" => $text
        ];

        $fields = array(
            'app_id' => \Yii::$app->params['oneSignal']['App_ID'],
            'filters' => [
                [
                    "field" => "tag",
                    "key" => "userId",
                    "relation" => "=",
                    "value" => "$user_id"
                ]
            ],
            'data' => array("foo" => "bar"),
            'contents' => $content
        );

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . \Yii::$app->params['oneSignal']['REST_API_key']));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

}
