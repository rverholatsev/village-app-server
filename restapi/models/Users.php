<?php

namespace restapi\models;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;

//use common\components\Notifier;

class Users extends \common\models\extended\Users implements \yii\web\IdentityInterface
{
    public $newRecord = null;

    public function beforeSave($insert)
    {
        ($this->isNewRecord) ? $this->newRecord = true : $this->newRecord = false;

        return parent::beforeSave($insert);
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return self::findByBearerToken($token);
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function getBearerToken()
    {
        return $this->bearer_token;
    }

    public function generateBearerToken()
    {
        $this->bearer_token = Yii::$app->security->generateRandomString();
    }

    public static function findByBearerToken($token)
    {
        $token = Tokens::findOne(['type' => Tokens::TYPE_BEARER_TOKEN, 'value' => $token]);
        if (empty($token)) {
            return null;
        }

        return self::findOne(['id' => $token->user_id, 'status' => self::STATUS_ACTIVE]);
    }

    public function generateEmailVerifyToken()
    {
        $this->email_verify_token = Yii::$app->security->generateRandomString();
    }

    public static function findByEmailVerifyToken($token)
    {
        $token = Tokens::findOne(['type' => Tokens::TYPE_EMAIL_VERIFY_TOKEN, 'value' => $token]);
        return $token !== null ? self::findOne($token->user_id) : null;
    }

    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString();
    }

    public static function findByResetPasswordToken($token)
    {
        $token = Tokens::findOne(['type' => Tokens::TYPE_PASSWORD_RESET_TOKEN, 'value' => $token]);
        return $token !== null ? $token->user : null;
    }

    public static function findByUnverifiedEmail($email)
    {
        return self::findOne(['email' => $email, 'status' => self::STATUS_EMAIL_UNVERIFIED]);
    }

    public static function findByVerifiedEmail($email)
    {
        return self::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findByPhone($phone)
    {
        return self::findOne(['phone' => $phone]);
    }

    public static function signUp($name, $phone, $company_name, $email, $password)
    {
        $user = self::findByEmail($email);

        if (empty($user)) {
            $user = new self();

            $user->name = $name;
            $user->phone = $phone;
            $user->company_name = $company_name;
            $user->email = $email;
            $user->password_hash = Yii::$app->security->generatePasswordHash($password);

            $user->role = self::ROLE_USER;
            $user->status = self::STATUS_EMAIL_UNVERIFIED;
            $user->is_push_available = true;

            if (!$user->save()) {
                throw new ServerErrorHttpException('Can\'t create new user.');
            }

            $user->generateEmailVerifyToken();

            return $user;
        } else {
            $user = self::findByUnverifiedEmail($email);

            if (!empty($user)) {

                $user->generateEmailVerifyToken();

                return $user;
            } else {
                throw new BadRequestHttpException('User with current email is already exist.');
            }
        }
    } // TODO: email link : done!

    public function confirmAndLogin()
    {
        $this->status = self::STATUS_ACTIVE;

        if (!$this->save()) {
            throw new ServerErrorHttpException('Can\'t update status of user.');
        }

        $this->email_verify_token = null;
        $this->generateAuthKey();
        $this->generateBearerToken();

        Yii::$app->user->login($this);
    }

    public function login()
    {
        $this->generateAuthKey();
        $this->generateBearerToken();
        $this->save();

        Yii::$app->user->login($this);
    }

    public function logout()
    {
        Yii::$app->user->logout();
    }

    public function resetPassword()
    {
        $this->password_hash = null;
        $this->status = self::STATUS_PASSWORD_RESETED;
        $this->save();

        $this->generatePasswordResetToken();
    } // TODO: email link : done!

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
        $this->save();

        $this->password_reset_token = null;
    }

    public function edit($name, $phone, $email, $company_name)
    {
        $this->name = $name;
        $this->phone = $phone;
        $this->company_name = $company_name;

        if ($this->email !== $email) {
            $this->email = $email;
            $this->status = $this::STATUS_EMAIL_UNVERIFIED;

            $this->generateEmailVerifyToken();
        }

        if(!$this->save()){
            throw new ServerErrorHttpException('Can\'t edit mail.');
        }
    } // TODO: email link : done!

    public function userResponse()
    {
        $result = new \stdClass();
        $result->id = (string)$this->id;
        $result->name = $this->name;
        $result->phone = $this->phone;
        $result->email = $this->email;
        $result->company_name = $this->company_name;
        $result->status = $this->status;
        $result->is_push_available = $this->is_push_available;
        $result->role = $this->role;
        $result->ar_number = $this->ar_number;
        $result->bearer_token = $this->bearer_token;
        return $result;
    }

    public static function searchCompanies($text)
    {
        return self::find()
            ->select('company_name')->distinct()
            ->where(['like', 'company_name', $text])
            ->orderBy('company_name')
            ->column();
    }

    public function setFcmToken($token)
    {
//        if (!FcmTokens::findOne(['user_id' => $this->id, 'token' => $token])) {
//            $fcmToken = new FcmTokens();
//            $fcmToken->user_id = $this->id;
//            $fcmToken->token = $token;
//            if ($fcmToken->save()) {
//                $response = Yii::$app->getResponse();
//                $response->setStatusCode(200);
//                return new \stdClass();
//            } else {
//                throw new InternalErrorException('Can\'t save FCM token.');
//            }
//        } else {
//            throw new BadRequestHttpException('FCM token is already exist');
//        }
    } // TODO: maybe will use later
}
