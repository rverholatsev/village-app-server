<?php

namespace restapi\models\validation\users;

use restapi\models\Users;
use yii;
use yii\base\Model;

class SetPassword extends Model
{
    public $password_reset_token;
    public $password;

    private $_user;

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
        if (!$this->hasErrors()) {
            if (!$this->getUser()) {
                $this->addError($attribute, 'Wrong password reset token.');
            }
        }
    }

    public function getUser()
    {
        if (!$this->_user) {
            $this->_user = Users::findByResetPasswordToken($this->password_reset_token);
        }
        return $this->_user;
    }

    public function setPassword()
    {
        $user = $this->getUser();

        $user->setPassword($this->password);

        return new \stdClass();
    }
}
