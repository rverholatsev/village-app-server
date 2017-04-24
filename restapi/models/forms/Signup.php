<?php
namespace restapi\models\forms;

use restapi\models\Users;

use yii;
use yii\base\Model;

/**
 * Signup form
 */
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
            [['name', 'company_name', 'password'], 'string'],
            [['name', 'phone', 'company_name', 'email', 'password'], 'filter', 'filter' => 'trim'],
            ['phone', 'integer', 'max' => 99999999999, 'min' => 10000000000, 'integerOnly' => true],
            ['email', 'email'],
            ['email', 'validateLogin'],
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

    private function validateEmail($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if ($user) {
                $this->addError($attribute, 'Email already exists.');
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
}
