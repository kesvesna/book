<?php

use yii\db\Migration;

/**
 * Class m210210_115014_book
 */
class m210210_115014_book extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('book', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull()->unique(),
            'isbn' => $this->integer()->notNull()->unique(),
            'page_count' => $this->integer(2),
            'category_id' => $this->integer()->defaultValue(1),
            'shot_description' => $this->text(),
            'long_description' => $this->text(),
        ]);


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('book');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210210_115014_book cannot be reverted.\n";

        return false;
    }
    */
}
