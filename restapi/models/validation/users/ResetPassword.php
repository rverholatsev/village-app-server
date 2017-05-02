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
                $this->addError($attribute, 'User with current email is not exist or email not verified.');
            }
        }
    }

    public function getUser()
    {
        if ($this->_user === null) {
            $this->_user = Users::findByVerifiedEmail($this->email);
        }
        return $this->_user;
    }

    public function resetPassword()
    {
        $user = $this->getUser();
        $user->resetPassword();

        Yii::$app->mail->compose()
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($this->email)
            ->setSubject('Village app. Reset password.')
            ->setTextBody('To reset password, follow the link: ' . Yii::$app->params['confirmPasswordUrl'] . '?password_reset_token=' . $user->password_reset_token)
            ->send();

        return new \stdClass();
    }
}
