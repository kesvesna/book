<?php

use yii\db\Migration;
use Faker\Factory;

/**
 * Class m210210_175229_category
 */
class m210210_175229_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('category', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()->unique()
        ]);

        $faker = Factory::create();
        $categories = ['Новинки'];
        for($i = 0; $i < count($categories); $i++)
        {
            //$counter_rates = [];
           // for ($j = 0; $j < 10; $j++)
           // {
                $counter_rates[] = [

                    //$faker->company,   // name
                    //$faker->bankAccountNumber,   // ИНН
                    $faker->name = $categories[$i],   // DG
                    //$faker->address,   // address
                ];
           // }
            // Для заполнения таблицы уберите комментарии
            Yii::$app->db->createCommand()->batchInsert('category', ['name'
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
        $this->dropTable('category');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210210_175229_category cannot be reverted.\n";

        return false;
    }
    */
}
