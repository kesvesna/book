<?php

use yii\db\Migration;
use Faker\Factory;

/**
 * Class m210210_185827_book
 */
class m210210_185827_book extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('book', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'isbn' => $this->string(255)->null()->defaultValue('0'),
            'page_count' => $this->integer(5),
            'published_date' => $this->dateTime(),
            'thumbnail_url' => $this->text(),
            'short_description' => $this->text(),
            'long_description' => $this->text(),
            'status_id' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-book-status_id',
            'book',
            'status_id',
            'status',
            'id',
            'CASCADE'
        );


        /*$faker = Factory::create();

        for($i = 0; $i < 10; $i++)
        {
            $counter_rates = [];
            for ($j = 0; $j < 10; $j++)
            {
                $counter_rates[] = [

                    $faker->text(15),   // book name
                    $faker->numberBetween (1, 250),   // isbn
                    $faker->numberBetween (1, 250), // page_count
                    $date = date('Y-m-d H:i:s', strtotime($faker->iso8601($max = 'now'))),
                    $faker->text = "https://".$faker->text(20).'com', // thumbnailUrl
                    $faker->text(20), // short_description
                    $faker->text(55),   // long_description
                    $faker->numberBetween(1, 1), // status_id
                ];


            }

            // Для заполнения таблицы уберите комментарии
            Yii::$app->db->createCommand()->batchInsert('book', ['title', 'isbn', 'page_count',
                'published_date', 'thumbnail_url', 'short_description', 'long_description', 'status_id'
                //'inn',
                //'dg', 'address'
            ], $counter_rates)->execute();
            unset($counter_rates);
        }*/
        //die('Data generation is complete!');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('book');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210210_185827_book cannot be reverted.\n";

        return false;
    }
    */
}
