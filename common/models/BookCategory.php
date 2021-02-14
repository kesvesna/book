<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "book_category".
 *
 * @property int $book_id
 * @property int $category_id
 *
 * @property Category $category
 * @property Book $book
 */
class BookCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'book_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['book_id', 'category_id'], 'required'],
            [['book_id', 'category_id'], 'integer'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
            [['book_id'], 'exist', 'skipOnError' => true, 'targetClass' => Book::className(), 'targetAttribute' => ['book_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'book_id' => 'Book ID',
            'category_id' => 'Category ID',
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * Gets query for [[Book]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBook()
    {
        return $this->hasOne(Book::className(), ['id' => 'book_id']);
    }


    public static function notExist($book_id, $category_id){

        $existBookCategory = BookCategory::find()
            ->andWhere([
                'book_id' => $book_id,
                'category_id' => $category_id
            ])->one();

        if(empty($existBookCategory)){
            return true;
        } else {
            return false;
        }
    }

    public function fill($book_id, $category_id) {
        $this->book_id = $book_id;
        $this->category_id = $category_id;
    }
}
