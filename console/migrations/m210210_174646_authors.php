<?php

use yii\db\Migration;
use Faker\Factory;

/**
 * Class m210210_174646_authors
 */
class m210210_174646_authors extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('authors', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()->unique()
        ]);

        $faker = Factory::create();

        for($i = 0; $i < 10; $i++)
        {
            $counter_rates = [];
            for ($j = 0; $j < 10; $j++)
            {
                $counter_rates[] = [

                    //$faker->company,   // name
                    //$faker->bankAccountNumber,   // ИНН
                    $faker->name($gender = 'male'|'female') ,   // DG
                    //$faker->address,   // address
                ];


            }

            // Для заполнения таблицы уберите комментарии
            Yii::$app->db->createCommand()->batchInsert('authors', ['name'
                //'inn',
                //'dg', 'address'
            ], $counter_rates)->execute();
            unset($counter_rates);
        }
        //die('Data generation is complete!');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('authors');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210210_174646_authors cannot be reverted.\n";

        return false;
    }
    */
}
