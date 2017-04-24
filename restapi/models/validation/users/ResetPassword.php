<?php

namespace restapi\models\validation\users;

use restapi\models\Users;
use yii;
use yii\base\Model;

class ResetPassword extends Model
{
    public $email;
    private $_user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'validateEmail'],
            ['email', 'filter', 'filter' => 'trim'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => 'Email',
        ];
    }

    public function validateEmail($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user) {
                $this->addError($attribute, 'User with current email is not exist.');
            } else if ($user->status === Users::STATUS_EMAIL_UNVERIFIED) {
                $this->addError($attribute, 'Email is not verified.');
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
