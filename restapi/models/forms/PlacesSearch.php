<?php
namespace restapi\models\forms;

use yii\base\Model;

class PlacesSearch extends Model
{
    public $address;
    public $query;
    public $next_page_token;

    public function rules()
    {
        return [
            [['address', 'query', 'next_page_token'], 'filter', 'filter' => 'trim'],
            [['address', 'query'], 'required'],
            [['address', 'query'], 'safe'],

            [['next_page_token'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'address' => 'Address',
            'query' => 'Query string',
            'token' => 'Token of next result search'
        ];
    }
}
