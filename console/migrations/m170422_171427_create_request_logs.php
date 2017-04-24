<?php

use yii\db\Migration;

class m170422_171427_create_request_logs extends Migration
{
    public function up()
    {
        $this->createTable('requests_logs', [
            'id' => $this->primaryKey(),
            'controller' => $this->string()->notNull(),
            'action' => $this->string()->notNull(),
            'request' => $this->string(),
            'response' => $this->string(),
            'error' => $this->string(),
            'device' => $this->string(),
            'user_id' => $this->integer(),
            'timestamp' => $this->string(),
            'method' => $this->string(10),
        ]);
    }

    public function down()
    {
        $this->dropTable('requests_logs');
    }
}
