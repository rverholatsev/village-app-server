<?php
namespace restapi\models;

use common\components\Notifier;
use common\models\extended\RequestsLogs;
use common\models\FcmTokens;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;

class Connections extends \common\models\extended\Connections
{
    public static function connect($host_id, $object_id)
    {
        if ($host_id == $object_id) {
            throw new BadRequestHttpException('Host ID can\'t be equal to Object ID.');
        } elseif (empty(Users::findOne(['id' => $object_id]))) {
            throw new BadRequestHttpException('Object with current ID is not exist.');
        }

        if (!self::findConnectionByUsersId($host_id, $object_id)) {
            $connectionModel = new self;
            $connectionModel->host_id = $host_id;
            $connectionModel->object_id = $object_id;
            $connectionModel->is_approved = false;

            if ($connectionModel->save()) {
                $tokens = FcmTokens::find()
                    ->select(['token'])
                    ->where(['user_id' => $object_id])
                    ->column();

                if (count($tokens) > 0) {
                    $host = Users::findOne($host_id);
                    Notifier::sendPush($tokens,
                        [
                            'title' => 'You have an invites.',
                            'body' => $host->first_name . ' ' . $host->last_name . ' invite you to my connections',
                            'click_action' => Connections::INVITE_REQUEST,
                        ],
                        [
                            'id' => $host->id,
                        ]);
                } else {
                    RequestsLogs::setError(['Push Notification not sended. Object haven\'t FCM tokens.']);
                }

                $response = Yii::$app->getResponse();
                $response->setStatusCode(200);
                return new \stdClass();
            } else {
                throw new ServerErrorHttpException('Can\'t create new connection');
            }
        } else {
            throw new BadRequestHttpException('Invite is already send');
        }
    }

    public static function approve($host_id, $object_id)
    {
        if ($host_id == $object_id) {
            throw new BadRequestHttpException('Host ID can\'t be equal to Object ID.');
        }

        $connectionModel = self::find()
            ->where(['host_id' => $host_id, 'object_id' => $object_id, 'is_approved' => false])
            ->one();

        if ($connectionModel) {
            $connectionModel->is_approved = true;

            if ($connectionModel->update()) {
                $tokens = FcmTokens::find()
                    ->select(['token'])
                    ->where(['user_id' => $host_id])
                    ->column();

                if (count($tokens) > 0) {
                    $object = Users::findOne($object_id);
                    Notifier::sendPush($tokens,
                        [
                            'title' => 'Your invite is approved.',
                            'body' => $object->first_name . ' ' . $object->last_name . ' is approved your invite and added to My Networks.',
                            'click_action' => Connections::APPROVE_REQUEST,
                        ],
                        [
                            'id' => $object->id,
                        ]);
                } else {
                    RequestsLogs::setError(['Push Notification not sended. Host haven\'t FCM tokens.']);
                }

                $response = Yii::$app->getResponse();
                $response->setStatusCode(200);
                return new \stdClass();
            } else {
                throw new ServerErrorHttpException('Can\'t update connection');
            }

        } else {
            throw new BadRequestHttpException('Can\'t find invite.');
        }
    }

    public static function decline($host_id, $object_id)
    {
        if ($host_id == $object_id) {
            throw new BadRequestHttpException('Host ID can\'t be equal to Object ID.');
        }

        $connectionModel = self::find()
            ->where(['host_id' => $host_id, 'object_id' => $object_id, 'is_approved' => false])
            ->one();

        if ($connectionModel) {
            if ($connectionModel->delete()) {
                $response = Yii::$app->getResponse();
                $response->setStatusCode(200);
                return new \stdClass();
            } else {
                throw new BadRequestHttpException('Can\'t delete invite');
            }
        } else {
            throw new BadRequestHttpException('Can\'t find invite.');
        }
    }

    public static function disconnect($user_id_1, $user_id_2)
    {
        if ($user_id_1 == $user_id_2) {
            throw new BadRequestHttpException('Host ID can\'t be equal to Object ID.');
        }

        if ($connectionModel = Connections::findConnectionByUsersId($user_id_1, $user_id_2)) {
            if ($connectionModel->delete()) {
                $response = Yii::$app->getResponse();
                $response->setStatusCode(200);
                return new \stdClass();
            } else {
                throw new BadRequestHttpException('Can\'t delete connection');
            }
        } else {
            throw new BadRequestHttpException('Can\'t find connection');
        }

    }

    public static function findConnectionByUsersId($user_id_1, $user_id_2)
    {
        $result = self::find()
            ->where(['host_id' => $user_id_1, 'object_id' => $user_id_2])
            ->orWhere(['host_id' => $user_id_2, 'object_id' => $user_id_1])
            ->one();
        return $result;
    }
}