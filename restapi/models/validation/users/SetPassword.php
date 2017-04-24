<?php

namespace restapi\models\validation\users;

use restapi\models\Users;
use yii;
use yii\base\Model;

class SetPassword extends Model
{
    public $password_reset_token;
    public $password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['password_reset_token', 'password', 'required'],
            ['password_reset_token', 'string', 'min' => 1],
            ['password_reset_token', 'validateToken'],
            ['password', 'string', 'length' => [3, 250]],
        ];
    }

    public function validateToken($attribute, $params)
    {
        $user = Users::findByResetPasswordToken($this->password_reset_token);

        if (empty($user)) {
            $this->addError($attribute, 'Wrong email password reset token.');
        } else if ($user->status !== Users::STATUS_PASSWORD_RESETED) {
            $this->addError($attribute,'User with this token can\'t have any statuses except password reseted.');
        }
    }
}
