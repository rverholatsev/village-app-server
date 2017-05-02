<?php

namespace restapi\models\validation\users;

use restapi\models\Users;

use yii;
use yii\base\Model;

class Edit extends Model
{
    public $name;
    public $phone;
    public $company_name;
    public $email;
    private $_user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'company_name'], 'string'],
            [['name', 'phone', 'company_name', 'email'], 'filter', 'filter' => 'trim'],
            ['phone', 'integer', 'max' => 9999999999, 'min' => 1000000000, 'integerOnly' => true],
            ['email', 'email'],
            ['email', 'validateEmail'],
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

            if ($user && $user !== Yii::$app->user->identity) {
                $this->addError($attribute, 'Email already exists.');
            }
        }
    }

    /**
     * @return null|Users
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = Yii::$app->user->identity;
        }
        return $this->_user;
    }

    public function edit()
    {
        $user = $this->getUser();
        $prevEmail = $user->email;

        $user->edit($this->name, $this->phone, $this->email, $this->company_name);

        if($prevEmail !== $user->email){
            Yii::$app->mail->compose()
                ->setFrom(Yii::$app->params['adminEmail'])
                ->setTo($this->email)
                ->setSubject('Village app. Confirm email.')
                ->setTextBody('To confirm email, follow the link: ' . Yii::$app->params['confirmEmailUrl'] . '?password_reset_token=' . $user->password_reset_token)
                ->send();

            $user->logout();
        }

        return new \stdClass();
    }
}
