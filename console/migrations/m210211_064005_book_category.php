<?php

use yii\db\Migration;

/**
 * Class m210211_064005_book_category
 */
class m210211_064005_book_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('book_category', [
            'id' => $this->primaryKey(),
            'book_id' => $this->integer()->notNull(),
            'category_id' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-book_category-book_id',
            'book_category',
            'book_id',
            'book',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-book_category-author_id',
            'book_category',
            'category_id',
            'category',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-book_category-book_id', 'book_category');
        $this->dropForeignKey('fk-book_category-author_id', 'book_category');
        $this->dropTable('book_category');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210211_064005_book_category cannot be reverted.\n";

        return false;
    }
    */
}
