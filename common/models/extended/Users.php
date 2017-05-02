<?php

namespace common\models\extended;

use Yii;
use yii\web\ServerErrorHttpException;

/**
 * This is the model class for table "users".
 *
 * @property string $email_verify_token
 * @property string $auth_key
 * @property string $bearer_token
 * @property string $password_reset_token
 * @property string $fcm_token
 *
 */

class Users extends \common\models\Users
{
    const STATUS_EMAIL_UNVERIFIED = 0;
    const STATUS_PASSWORD_RESETED = 1;
    const STATUS_ACTIVE = 10;

    const ROLE_USER = 0;
    const ROLE_ADMIN = 1;
    const ROLE_SUPER_USER = 2;

    public static function findByEmail($email)
    {
        return self::findOne(['email' => $email]);
    }

    public function getEmail_verify_token()
    {
        $token = Tokens::findOne(['user_id' => $this->id, 'type' => Tokens::TYPE_EMAIL_VERIFY_TOKEN]);
        return $token ? $token->value : null;
    }

    public function setEmail_verify_token($value)
    {
        Tokens::updateOrCreateOrDelete($this->id, Tokens::TYPE_EMAIL_VERIFY_TOKEN, $value);
    }

    public function getAuth_key()
    {
        $token = Tokens::findOne(['user_id' => $this->id, 'type' => Tokens::TYPE_AUTH_KEY]);
        return $token ? $token->value : null;
    }

    public function setAuth_key($value)
    {
        Tokens::updateOrCreateOrDelete($this->id, Tokens::TYPE_AUTH_KEY, $value);
    }

    public function getBearer_token()
    {
        $token = Tokens::findOne(['user_id' => $this->id, 'type' => Tokens::TYPE_BEARER_TOKEN]);
        return $token ? $token->value : null;
    }

    public function setBearer_token($value)
    {
        Tokens::updateOrCreateOrDelete($this->id, Tokens::TYPE_BEARER_TOKEN, $value);
    }

    public function getPassword_reset_token()
    {
        $token = Tokens::findOne(['user_id' => $this->id, 'type' => Tokens::TYPE_PASSWORD_RESET_TOKEN]);
        return $token ? $token->value : null;
    }

    public function setPassword_reset_token($value)
    {
        Tokens::updateOrCreateOrDelete($this->id, Tokens::TYPE_PASSWORD_RESET_TOKEN, $value);
    }

    // TODO: need to think about realization FCM tokens

    public function getFcmTokens()
    {
        $token = Tokens::findOne(['user_id' => $this->id, 'type' => Tokens::TYPE_FCM_TOKEN]);
        return $token ? $token->value : null;
    }

    public function setFcm_token($value)
    {
        Tokens::create($this->id, Tokens::TYPE_FCM_TOKEN, $value);
    }
}
