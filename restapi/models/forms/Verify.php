<?php
namespace restapi\models\forms;

use restapi\models\Users;
use yii;
use yii\base\Model;

/**
 * Signup form
 */
class Verify extends Model
{
    public $_code;
    public $_phone;
    private $_user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_phone'], 'filter', 'filter' => 'trim'],
            [['_phone'], 'required'],
            ['_phone', 'match', 'pattern' => '/^[\d]{11}$/', 'message' => 'Phone must be an 11 digits'],
            [['_code'], 'filter', 'filter' => 'trim'],
            [['_code'], 'required'],
            [['_code'], 'string', 'max' => 4, 'tooLong'  => 'Verification code must contain 4 characters'],
            [['_code'], 'verify'],
        ];
    }

    public function __construct($phone, $config = [])
    {
        if (!$this->_user = Users::findByUnverifyPhone($phone)) {
            throw new yii\web\BadRequestHttpException('User is not found.');
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_code' => 'Проверочный код',
        ];
    }

    public function verify($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if ($this->_user->code_verify != $this->_code) {
                $this->addError($attribute, 'Your verification code is incorrect.');
            }
        }
    }
}
