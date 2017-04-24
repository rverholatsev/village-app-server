<?php
namespace restapi\modules\v1\controllers;

use common\components\Places;
use restapi\controllers\AppController;
use restapi\models\forms\PlacesSearch;
use yii;
use \restapi\filters\RequestLogFilter;

class PlacesController extends AppController
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
            ],
        ];
        return $behaviors;
    }

    protected function verbs()
    {
        return [
            'search' => ['get'],
        ];
    }

    public function actionSearch() {
        $model = new PlacesSearch();
        $model->load(Yii::$app->request->get(), '');
        if($model->validate()) {
            return Places::search($model->address, $model->query, $model->next_page_token);
        }
        return $model;
    }
}