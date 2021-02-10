<?php

use yii\db\Migration;
use Faker\Factory;

/**
 * Class m210210_185350_status
 */
class m210210_185350_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('status', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()->unique(),
        ]);

        $faker = Factory::create();
        $status = ['PUBLISH'];
        for($i = 0; $i < count($status); $i++)
        {
            //$counter_rates = [];
            // for ($j = 0; $j < 10; $j++)
            // {
            $counter_rates[] = [

                //$faker->company,   // name
                //$faker->bankAccountNumber,   // ИНН
                $faker->name = $status[$i],   // DG
                //$faker->address,   // address
            ];
            // }
            // Для заполнения таблицы уберите комментарии
            Yii::$app->db->createCommand()->batchInsert('status', ['name'
                //'inn',
                //'dg', 'address'
            ], $counter_rates)->execute();
            unset($counter_rates);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('status');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210210_185350_status cannot be reverted.\n";

        return false;
    }
    */
}
