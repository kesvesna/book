<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property string $name
 *
 * @property BookCategory[] $bookCategories
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * Gets query for [[BookCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBookCategories()
    {
        return $this->hasMany(BookCategory::className(), ['category_id' => 'id']);
    }


    public static function getCategoryId ($category_name){

        $existCategory = Category::find()->andWhere(['name' => $category_name])->one();

        if (empty($existCategory)) {
            $newCategory = new Category();
            $newCategory->name = $category_name;
            $newCategory->save();
            return $newCategory->id;
        } else {
            return $existCategory->id;
        }
    }
}
