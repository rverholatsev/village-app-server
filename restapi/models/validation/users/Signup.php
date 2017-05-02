<?php

namespace restapi\models\validation\users;

use restapi\models\Users;

use yii;
use yii\base\Model;

class Signup extends Model
{
    public $name;
    public $phone;
    public $company_name;
    public $email;
    public $password;
    private $_user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'phone', 'email', 'password'], 'required'],
            [['name', 'company_name'], 'string'],
            ['password', 'string', 'length' => [3, 250]],
            [['name', 'phone', 'company_name', 'email', 'password'], 'filter', 'filter' => 'trim'],
            ['phone', 'integer', 'max' => 9999999999, 'min' => 1000000000, 'integerOnly' => true],
            ['email', 'email'],
            ['email', 'validateEmail'],
            ['phone', 'validatePhone'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'phone' => 'Phone',
            'company_name' => 'Company name',
            'email' => 'Email',
            'password' => 'Password',
        ];
    }

    public function validateEmail($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if ($user) {
                $this->addError($attribute, 'Email already exists.');
            }
        }
    }

    public function validatePhone($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = Users::findByPhone($this->phone);
            if ($user) {
                $this->addError($attribute, 'Phone already exists.');
            }
        }
    }

    /**
     * Finds user by [[phone]]
     *
     * @return Users|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = Users::findByEmail($this->email);
        }
        return $this->_user;
    }

    public function signUp()
    {
        /** @var Users $user */
        $user = Users::signUp($this->name, $this->phone, $this->company_name, $this->email, $this->password);

        Yii::$app->mail->compose()
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($this->email)
            ->setSubject('Village app. Verify email.')
            ->setTextBody('To confirm the mail, follow the link: ' . Yii::$app->params['confirmEmailUrl'] . '?email_verify_token=' . $user->email_verify_token)
            ->send();

        return new \stdClass();
    }

}
