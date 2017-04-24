<?php
namespace common\models\extended;

use yii;

class RequestsLogs extends \common\models\RequestsLogs {
    use AppModel;

    public static function setError($data){
        $requestLogId = Yii::$app->session->get('request_log_id', false);
        if ($requestLogId) {
            $requestsLogs = RequestsLogs::findOne($requestLogId);
            $requestsLogs->error = \yii\helpers\Json::encode($data);
            $requestsLogs->save();
        }
    }
}
