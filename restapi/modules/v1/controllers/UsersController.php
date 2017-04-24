<?php

namespace restapi\modules\v1\controllers;

use restapi\controllers\AppController;
use restapi\models\validation\users\Confirm;
use restapi\models\validation\users\Edit;
use restapi\models\validation\users\Login;
use restapi\models\validation\users\ResetPassword;
use restapi\models\validation\users\SetPassword;
use restapi\models\validation\users\Signup;
use restapi\models\Users;
use yii;
use \restapi\filters\RequestLogFilter;

class UsersController extends AppController
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
            'except' => [
                'signup',
                'confirm',
                'login',
                'reset-password',
                'set-password',
            ],
        ];
        return $behaviors;
    }

    protected function verbs()
    {
        return [
            'index' => ['get'],
            'signup' => ['post'],
            'confirm' => ['get'],
            'login' => ['post'],
            'logout' => ['post'],
            'reset-password' => ['post'],
            'set-password' => ['post'],
            'edit' => ['post'],
        ];
    }

    public function actionIndex()
    {
        /** @var Users $user */
        $user = Yii::$app->user->identity;
        return $user->userResponse();
    }

    public function actionSignup()
    {
        $model = new Signup();
        $model->load(Yii::$app->request->post(), '');
        if ($model->validate()) {
            return Users::signUp($model->name, $model->phone, $model->company_name, $model->email, $model->password);
        }
        return $model;
    }

    public function actionConfirm()
    {
        $model = new Confirm();
        $model->load(Yii::$app->request->get(), '');

        if (!$model->validate()) {
            return $model;
        }

        $user = Users::findByEmailVerifyToken($model->email_verify_token);
        return $user->confirmAndLogin();
    }

    public function actionLogin()
    {
        $model = new Login();
        $model->load(Yii::$app->request->post(), '');

        if (!$model->validate()) {
            return $model;
        }

        $user = Users::findByEmail($model->email);

        return $user->login();
    }

    public function actionLogout()
    {
        /** @var Users $user */
        $user = Yii::$app->user->identity;

        return $user->logout();
    }

    public function actionResetPassword()
    {
        $model = new ResetPassword();

        $model->load(Yii::$app->request->post(), '');

        if (!$model->validate()) {
            return $model;
        }

        $user = Users::findByVerifiedEmail($model->email);

        return $user->resetPassword();
    }

    public function actionSetPassword()
    {
        $model = new SetPassword();

        $model->load(Yii::$app->request->post(), '');

        if (!$model->validate()) {
            return $model;
        }

        $user = Users::findByResetPasswordToken($model->password_reset_token);

        return $user->setPassword($model->password);
    } // TODO: fix error

    public function actionEdit()
    {
        $model = new Edit();
        $model->load(Yii::$app->request->post(), '');
        if(!$model->validate()){
            return $model;
        }

        /** @var Users $user */
        $user = Yii::$app->user->identity;
        return $user->edit($model->name, $model->phone, $model->email, $model->company_name);
    }
}