<?php

namespace restapi\modules\v1\controllers;

use restapi\controllers\AppController;
use restapi\models\Users;
use restapi\models\validation\companies\Search;
use yii;
use \restapi\filters\RequestLogFilter;

class CompaniesController extends AppController
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
            'search' => ['get'],
        ];
    }

    public function actionSearch()
    {
        $model = new Search();
        $model->load(Yii::$app->request->get(), '');
        if (!$model->validate()) {
            return $model;
        }

        return Users::searchCompanies($model->text);
    }
}