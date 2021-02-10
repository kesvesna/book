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
            'page_count' => $this->integer(5),
            'published_date' => $this->dateTime(),
            'thumbnail_url' => $this->string(255),
            'shot_description' => $this->text(),
            'long_description' => $this->text(),
            'status' => $this->string(255)
        ]);


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('book');
<<<<<<< HEAD
        return true;
=======
        return true; // comment for git test
>>>>>>> d3cc671bf2d4ec332d9e4ada7f47b777098078f3
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
