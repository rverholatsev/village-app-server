<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "requests_logs".
 *
 * @property integer $id
 * @property string $controller
 * @property string $action
 * @property string $request
 * @property string $response
 * @property string $error
 * @property string $device
 * @property integer $user_id
 * @property string $timestamp
 * @property string $method
 */
class RequestsLogs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'requests_logs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['controller', 'action'], 'required'],
            [['user_id'], 'integer'],
            [['controller', 'action', 'request', 'response', 'error', 'device', 'timestamp'], 'string', 'max' => 255],
            [['method'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'controller' => 'Controller',
            'action' => 'Action',
            'request' => 'Request',
            'response' => 'Response',
            'error' => 'Error',
            'device' => 'Device',
            'user_id' => 'User ID',
            'timestamp' => 'Timestamp',
            'method' => 'Method',
        ];
    }
}
