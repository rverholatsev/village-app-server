<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tokens".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $type
 * @property string $value
 *
 * @property Users $user
 */
class Tokens extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tokens';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'value'], 'required'],
            [['user_id', 'type'], 'integer'],
            [['value'], 'string', 'max' => 63],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'type' => 'Type',
            'value' => 'Value',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id'])->inverseOf('tokens');
    }
}
