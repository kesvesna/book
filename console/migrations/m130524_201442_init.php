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

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'admin' => $this->boolean()->defaultValue(0),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'status' => $this->smallInteger()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->insert('user', [
            'username' => 'admin',
            'admin' => '1',
            'auth_key' => 'iYplI50-6uxaQxENiGsZyOLuJLfGlG4X',
            'password_hash' => '$2y$13$lBVcrTTBfet6BgAgKnv9/eM5vH0X9/TZHqKJdRpFFzI0TnB7nkJgW',
            'password_reset_token' => 'qCUxD7mFHa6ubGfFURDClbOtRUJwuXsH_1613072427',
            'email' => 'kesvesna@rambler.ru',
            'status' => 10,
            'created_at' => 1613072427,
            'updated_at' => 1613072427,
        ]);

        $this->insert('user', [
            'username' => 'user',
            'admin' => '0',
            'auth_key' => 'iYplI50-6uxaQxENiGsZyOLuJLfGlG4X',
            'password_hash' => '$2y$13$lBVcrTTBfet6BgAgKnv9/eM5vH0X9/TZHqKJdRpFFzI0TnB7nkJgW',
            'password_reset_token' => 'qCUxD7mFHa6ubGfFURDClbOtRUJwuXsH_1613072428',
            'email' => 'kesvesna@gmail.com',
            'status' => 10,
            'created_at' => 1613072428,
            'updated_at' => 1613072428,
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
