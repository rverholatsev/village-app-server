<?php

namespace restapi\models\validation\users;

use restapi\models\Users;
use yii;
use yii\base\Model;

/**
 * Confirm form
 */
class Confirm extends Model
{
    public $email_verify_token;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email_verify_token', 'required'],
            ['email_verify_token', 'string'],
            ['email_verify_token', 'validateToken'],
        ];
    }

    public function validateToken($attribute, $params)
    {
        $user = Users::findByEmailVerifyToken($this->email_verify_token);
        if (empty($user)) {
            $this->addError($attribute, 'Wrong sign up token.');
        } else if ($user->status !== Users::STATUS_EMAIL_UNVERIFIED) {
            $this->addError($attribute, 'User with this token can\'t have any status expect "STATUS_EMAIL_UNVERIFIED".');
        }
    }
}
