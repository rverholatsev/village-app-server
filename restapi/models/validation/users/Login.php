<?php

namespace restapi\models\validation\users;

use restapi\models\Users;

use yii;
use yii\base\Model;

class Login extends Model
{
    public $email;
    public $password;
    private $_user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            ['email', 'email'],
            ['email', 'validateEmail'],
            ['password', 'string', 'length' => [3, 250]],
            ['password', 'validatePassword'],
            [['email', 'password'], 'filter', 'filter' => 'trim'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => 'Email',
            'password' => 'Password',
        ];
    }

    public function validateEmail($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user) {
                $this->addError($attribute, 'User with current email is not exist.');
            } else if ($user->status !== Users::STATUS_ACTIVE) {
                $this->addError($attribute, 'User is not active.');
            }
        }
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!Yii::$app->security->validatePassword($this->password, $user->password_hash)) {
                $this->addError($attribute, 'Wrong password.');
            }
        }
    }

    public function getUser()
    {
        if ($this->_user === null) {
            $this->_user = Users::findByEmail($this->email);
        }
        return $this->_user;
    }
}
