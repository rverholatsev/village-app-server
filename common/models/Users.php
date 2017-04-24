<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $name
 * @property integer $phone
 * @property string $email
 * @property string $company_name
 * @property string $password_hash
 * @property integer $status
 * @property integer $role
 * @property integer $is_push_available
 * @property integer $ar_number
 *
 * @property Tokens[] $tokens
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'phone', 'email', 'role'], 'required'],
            [['phone', 'status', 'role', 'is_push_available', 'ar_number'], 'integer'],
            [['name', 'email', 'company_name'], 'string', 'max' => 31],
            [['password_hash'], 'string', 'max' => 255],
            [['phone'], 'unique'],
            [['email'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'phone' => 'Phone',
            'email' => 'Email',
            'company_name' => 'Company Name',
            'password_hash' => 'Password Hash',
            'status' => 'Status',
            'role' => 'Role',
            'is_push_available' => 'Is Push Available',
            'ar_number' => 'Ar Number',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTokens()
    {
        return $this->hasMany(Tokens::className(), ['user_id' => 'id'])->inverseOf('user');
    }
}
