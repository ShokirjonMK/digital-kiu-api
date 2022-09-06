<?php

namespace api\controllers;

use common\models\model\Profile;
use Yii;
use yii\db\Expression;
use yii\rest\ActiveController;

class TelegramController extends ActiveController
{
    use ApiOpen;

    public $modelClass = '';

    public function actions()
    {
        return [];
    }

    // public $enableCsrfValidation = false;

    public function actionIndex()
    {

        /*   Yii::$app->telegram->sendPhoto([
            'chat_id' => 813225336,
            'photo' => 'https://digital.tsul.uz/static/media/loginImg.e19938fd.png',
            'caption' => 'this is test'
        ]); */

        /* ************** */
        // $telegram = Yii::$app->telegram;
        // $telegram->sendMessage([
        //     'chat_id' => 813225336,
        //     'text' =>  "aaaasd !!!"

        // ]);

        // return 0;

        /* ************** */
        $telegram = Yii::$app->telegram;
        // return $telegram;
        if ($telegram) {
            $telegram->sendMessage([
                'chat_id' => 813225336,
                'text' => "asd asd"

            ]);

            $text = $telegram->input->message->text;
            $username = $telegram->input->message->chat->username;
            $telegram_id = $telegram->input->message->chat->id;

            if (
                $text == "/start"
            ) {

                $keyboards = json_encode([
                    'keyboard' => [
                        [
                            ['text' => "☎️Telefon raqamni jo`natish☎️", 'callback_data' => "/start"]
                        ]
                    ], 'resize_keyboard' => true
                ]);

                $telegram->sendMessage([
                    'chat_id' => $telegram_id,
                    'text' => "Assalomu alaykum @" . $username . " \n DIGITAL TSUL rasmiy botiga xush kelibsiz!!!",
                    'reply_markup' => $keyboards
                ]);
            }

            if ($text == "☎️Telefon raqamni jo`natish☎️") {
                $replyMarkup3 = [
                    'keyboard' => [[[
                        'text' => 'Telefon raqamni jo`nating...',
                        'request_contact' => true,
                    ]]],
                    'resize_keyboard' => true,
                    'request_contact' => true,
                ];
                $encodedMarkup = json_encode($replyMarkup3);
                $telegram->sendMessage([
                    'chat_id' => $telegram_id,
                    'text' => "Telefon raqamni jo`nating...",
                    'reply_markup' => $encodedMarkup
                ]);
                die;
            }

            if (json_encode($telegram->input->message->contact) != "null") {
                $test = json_encode($telegram->input->message->contact);
                $new_test = json_decode($test);
                $phone = (int) $new_test->phone_number;

                $new_phone = "(" . mb_substr($phone, 3, 2) . ")-" . mb_substr($phone, 5, 3) . "-" . mb_substr($phone, 8, 4);

                $new_phone = preg_replace('/[^0-9]/', '', $new_phone);

                $userTelegram = Profile::find()
                    ->select([
                        new Expression("replace(replace(phone, '-', ''), ' ', '') as phone"),
                        new Expression("replace(replace(phone_secondary, '-', ''), ' ', '') as phone_secondary"),
                        'telegram_chat_id',
                        'last_name',
                        'first_name',
                        'middle_name',
                        'id',
                    ])
                    ->orWhere(['phone' => $new_phone])
                    ->orWhere(['phone_secondary' => $new_phone])
                    ->one();


                if ($userTelegram) {
                    if ($userTelegram->telegram_chat_id) {
                        $arr = explode("-", $userTelegram->telegram_chat_id);
                        if (!in_array($telegram_id, $arr)) {
                            $userTelegram->telegram_chat_id = $userTelegram->telegram_chat_id . "-" . $telegram_id;
                        }
                    } else {
                        $userTelegram->telegram_chat_id = json_encode($telegram_id);
                    }
                    $userTelegram->save(false);

                    $telegram->sendMessage([
                        'chat_id' => $telegram_id,
                        'text' =>  $userTelegram->full_name . "-" . $new_phone
                    ]);
                } else {
                    $telegram->sendMessage([
                        'chat_id' => $telegram_id,
                        'text' =>  "+998" . $new_phone . " raqamdan ro`yxatdan o`tgan o`quvchi topilmadi!!!"

                    ]);
                }
            }
        }
    }
}
