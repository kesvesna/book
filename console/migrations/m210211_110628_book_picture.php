<?php

use yii\db\Migration;

/**
 * Class m210211_110628_book_picture
 */
class m210211_110628_book_picture extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('book_picture', [
            'id' => $this->primaryKey(),
            'picture_file_path' => $this->string(255)->null()->defaultValue('0'),
            'book_id' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-book_picture-book_id',
            'book_picture',
            'book_id',
            'book',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('book_picture');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210211_110628_book_picture cannot be reverted.\n";

        return false;
    }
    */
}
