<?php

namespace restapi\modules\v1\controllers;

use yii\rest\Controller;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        return new \stdClass();
    }
}
