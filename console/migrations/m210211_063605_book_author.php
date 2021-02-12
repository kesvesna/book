<?php

use yii\db\Migration;

/**
 * Class m210211_063605_book_author
 */
class m210211_063605_book_author extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('book_author', [
            'id' => $this->primaryKey(),
            'book_id' => $this->integer()->notNull(),
            'author_id' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
        'fk-book_author-book_id',
        'book_author',
        'book_id',
        'book',
        'id',
        'CASCADE'
    );

        $this->addForeignKey(
            'fk-book_author-author_id',
            'book_author',
            'author_id',
            'authors',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('book_author');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210211_063605_book_author cannot be reverted.\n";

        return false;
    }
    */
}
