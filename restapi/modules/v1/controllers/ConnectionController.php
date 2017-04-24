<?php
namespace restapi\modules\v1\controllers;

use restapi\controllers\AppController;
use restapi\models\Users;
use restapi\models\Connections;
use restapi\models\forms\ConnectionForm;
use yii;
use restapi\filters\RequestLogFilter;

class ConnectionController extends AppController
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['logs'] = ['class' => RequestLogFilter::className(), 'except' => ['log']];
        $behaviors['authenticator'] = [
            'class' => yii\filters\auth\HttpBearerAuth::className(),
            'except' => [],
        ];
        return $behaviors;
    }

    protected function verbs()
    {
        return [
            'connect' => ['post'],
            'approve' => ['post'],
            'decline' => ['post'],
            'disconnect' => ['post'],
            'view-connections' => ['get'],
            'view-invites' => ['get'],
        ];
    }

    public function actionConnect()
    {
        /** @var Users $user */
        $user = Yii::$app->user->identity;

        $model = new ConnectionForm();
        $model->load(Yii::$app->request->post(), '');
        if ($model->validate()) {
            return Connections::connect($user->id, $model->id);
        }
        return $model;
    }

    public function actionApprove()
    {
        /** @var Users $user */
        $user = Yii::$app->user->identity;

        $model = new ConnectionForm();
        $model->load(Yii::$app->request->post(), '');
        if ($model->validate()) {
            return Connections::approve($model->id, $user->id);
        }
        return $model;
    }

    public static function actionDecline()
    {
        /** @var Users $user */
        $user = Yii::$app->user->identity;

        $model = new ConnectionForm();
        $model->load(Yii::$app->request->post(), '');
        if ($model->validate()) {
            return Connections::decline($model->id, $user->id);
        }
        return $model;
    }

    public function actionDisconnect()
    {
        /** @var Users $user */
        $user = Yii::$app->user->identity;

        $model = new ConnectionForm();
        $model->load(Yii::$app->request->post(), '');
        if ($model->validate()) {
            return Connections::disconnect($model->id, $user->id);
        }
        return $model;
    }

    public function actionViewConnections(){
        /** @var Users $user */
        $user = Yii::$app->user->identity;
        return $user->getConnections(true);
    }

    public function actionViewInvites()
    {
        /** @var Users $user */
        $user = Yii::$app->user->identity;
        return $user->getInvites();
    }
}
