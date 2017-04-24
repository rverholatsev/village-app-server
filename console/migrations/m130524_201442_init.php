<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('users', [
            'id' => $this->primaryKey(),
            'name' => $this->string(31)->notNull(),
            'phone' => $this->integer()->unique()->notNull(),
            'email' => $this->string(31)->notNull()->unique(),
            'company_name' => $this->string(31),
            'password_hash' => $this->string(),
            'status' => $this->smallInteger(1),
            'role' => $this->smallInteger(1)->notNull(), // 1 - external_user, 2 - admin, 3 - super_admin
            'is_push_available' => $this->boolean(),
            'ar_number' => $this->integer(),
        ], $tableOptions);

        /** -- -- --- -- -- -- -- */

        $this->createTable('tokens', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'type' => $this->smallInteger(1),
            'value' => $this->string(63)->notNull(),
        ], $tableOptions);

        $this->addForeignKey('FK_users_tokens', 'tokens', 'user_id', 'users', 'id', 'CASCADE', 'CASCADE');

        /** -- -- --- -- -- -- -- */

        $this->createTable('hotels', [
            'id' => $this->primaryKey(),

            'address' => $this->string(255)->notNull(),
            'city_or_region' => $this->string(31)->notNull(),
            'g_place_id' => $this->string(47)->notNull(),
            'coord_latitude' => $this->string(47)->notNull(),
            'coord_longitude' => $this->string(47)->notNull(),

            'is_visible' => $this->boolean()->notNull(),
            'photos' => $this->text(), // json array, photos[1,5]
        ], $tableOptions);

        /** -- -- --- -- -- -- -- */

        $this->createTable('rooms', [
            'id' => $this->primaryKey(),
            'hotel_id' => $this->integer()->notNull(),

            'name' => $this->string(31)->notNull(),
            'number_attendees' => $this->integer()->notNull(),
            'price' => $this->integer()->notNull(), // json array, price for 1 hour each day of week
            'min_booking_period' => $this->integer()->notNull(),
            'max_booking_period' => $this->integer()->notNull(),
            'start_time' => $this->integer()->notNull(),
            'end_time' => $this->integer()->notNull(),

            'is_visible' => $this->boolean()->notNull(),
            'photos' => $this->text(), // json array, photos[1,5]
        ], $tableOptions);

        $this->addForeignKey('FK_hotels_rooms', 'rooms', 'hotel_id', 'hotels', 'id', 'CASCADE', 'CASCADE');

        /** -- -- --- -- -- -- -- */

        $this->createTable('booked_rooms', [
            'id' => $this->primaryKey(),
            'room_id' => $this->integer()->notNull(),

            'name' => $this->string(30)->notNull(),
            'company_name' => $this->string(30)->notNull(),
            'email' => $this->string(31),
            'phone' => $this->string(11)->notNull(),

            'date' => $this->integer()->notNull(),
            'start_time' => $this->integer()->notNull(),
            'end_time' => $this->integer()->notNull(),
            'total_price' => $this->integer()->notNull(),

            'status' => $this->integer()->notNull() // 1 - waiting_charge, 2 - charge_success, 3 - order_success
        ], $tableOptions);

        $this->addForeignKey('FK_rooms_booked_rooms', 'booked_rooms', 'room_id', 'rooms', 'id', 'CASCADE', 'CASCADE');

        /** -- -- --- -- -- -- -- */

        $this->createTable('faqs', [
            'id' => $this->primaryKey(),
            'question' => $this->text()->notNull(),
            'answer' => $this->text()->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('faqs');
        $this->dropTable('booked_rooms');
        $this->dropTable('rooms');
        $this->dropTable('hotels');
        $this->dropTable('tokens');
        $this->dropTable('users');
    }
}
