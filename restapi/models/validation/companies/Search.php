<?php

namespace restapi\models\validation\companies;

use restapi\models\Users;
use yii;
use yii\base\Model;

class Search extends Model
{
    public $text;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['text', 'required'],
            ['text', 'string', 'min' => 3],
        ];
    }
}
