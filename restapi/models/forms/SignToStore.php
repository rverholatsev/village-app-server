<?php
namespace restapi\models\forms;

use common\models\extended\UsersStores;
use yii\base\Model;

class SignToStore extends Model
{
    public $googleId;
    public $name;
    public $address;
    public $role;
    public $date_from;


    public function rules()
    {
        return [
            [['googleId', 'name', 'address', 'role', 'date_from'], 'filter', 'filter' => 'trim'],
            [['googleId', 'name', 'role'], 'required'],
            [['googleId', 'name', 'address'], 'safe'],
            [['googleId'], 'string', 'max' => 31, 'tooLong'  => 'GoogleId must contain 31 characters or less'],
            [['name'], 'string', 'max' => 255, 'tooLong'  => 'Name must contain 255 characters or less'],
            ['role', 'validateRole']
        ];
    }

    public function attributeLabels()
    {
        return [
            'googleId' => 'Google ID',
            'name' => 'Store name',
            'address' => 'Store address',
            'role' => 'Role in store',
            'date_from' => 'Date from',
        ];
    }

    public function validateRole($attribute, $params)
    {
        if (!in_array($this->$attribute, UsersStores::ROLES)) {
            $this->addError($attribute, 'Role must be one of: ' . implode(', ', UsersStores::ROLES));
        }
    }
}
