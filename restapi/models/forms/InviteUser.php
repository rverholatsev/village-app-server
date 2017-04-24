<?php
namespace restapi\models\forms;

use restapi\models\Users;
use yii\base\Model;

class InviteUser extends Model
{
    public $_phone, $name;

    public function rules()
    {
        return [
            [['_phone'], 'filter', 'filter' => 'trim'],
            [['_phone'], 'required'],
            ['_phone', 'match', 'pattern' => '/^[\d]{11}$/', 'message' => 'Phone must be an 11 digits'],
            ['name', 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            '_phone' => 'Phone',
            'name' => 'Name',
        ];
    }
}
