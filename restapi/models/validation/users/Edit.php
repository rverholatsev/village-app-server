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

    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = Users::findByEmail($this->email);
        }
        return $this->_user;
    }
}
