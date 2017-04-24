<?php

namespace restapi\models;

use Yii;

class Tokens extends \common\models\extended\Tokens
{
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id'])->inverseOf('tokens');
    }
}
