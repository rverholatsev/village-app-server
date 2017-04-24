<?php
namespace restapi\models\forms;

use restapi\models\Users;
use yii;
use yii\base\Model;

/**
 * Confirm form
 */
class Confirm extends Model
{
    public $token;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['token', 'required'],
            ['token', 'string'],
            ['token', 'validateToken'],
        ];
    }

    public function validateToken($attribute, $params)
    {
        $user = Users::findOne(['sign_up_token' => $this->token, 'status' => Users::STATUS_UNVERIFIED]);

        if(empty($user)){
            $this->addError($attribute, 'Wrong token.');
        }
    }

    public function send()
    {
//        /** @var Users $user */
//        $user = Yii::$app->user->identity;
//        if (!$user) {
//            $user = new Users();
//
//            $user->generateCodeVerify();
//            $user->status = Users::STATUS_UNVERIFY;
//            if ($user->save()) {
//                return 'unverify';
//            } else {
//                throw new yii\web\ServerErrorHttpException('Could not create user for unknown reason.');
//            }
//        } else {
//            $user->generateCodeVerify();
//            $user->status = Users::STATUS_UNVERIFY;
//        }
//
//        if ($user->save()) {
//            Yii::$app->twilio->sendSms('89831304926', '89831304926', $user->code_verify);
//        } else {
//            throw new yii\web\ServerErrorHttpException('Could not update user for unknown reason.');
//        }
//
//        return true;
    }
}
