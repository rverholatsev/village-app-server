<?php

namespace common\models\extended;

use Yii;
use yii\web\ServerErrorHttpException;

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
class Tokens extends \common\models\Tokens
{
    const TYPE_EMAIL_VERIFY_TOKEN = 0;
    const TYPE_AUTH_KEY = 1;
    const TYPE_BEARER_TOKEN = 2;
    const TYPE_PASSWORD_RESET_TOKEN = 3;
    const TYPE_FCM_TOKEN = 4;

    public static function updateOrCreateOrDelete($user_id, $type, $value)
    {
        $token = self::findOne(['user_id' => $user_id, 'type' => $type]);

        if (empty($token)) {
            if ($value === null) {
                throw new ServerErrorHttpException('Can\'t create new token with a null value.');
            }
            $token = new self();
            $token->user_id = $user_id;
            $token->type = $type;
            $token->value = $value;
        } else {
            if ($value === null) {
                $token->delete();
                return;
            } else {
                $token->value = $value;
            }
        }

        if (!$token->save()) {
            throw new ServerErrorHttpException('Can\'t save token.');
        }
    }

    public static function create($user_id, $type, $value)
    {
        $token = new self();
        $token->user_id = $user_id;
        $token->type = $type;
        $token->value = $value;
        $token->save();
    }
}
