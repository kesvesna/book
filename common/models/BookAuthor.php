<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "book_author".
 *
 * @property int $book_id
 * @property int $author_id
 *
 * @property Authors $author
 * @property Book $book
 */
class BookAuthor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'book_author';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['book_id', 'author_id'], 'required'],
            [['book_id', 'author_id'], 'integer'],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Authors::className(), 'targetAttribute' => ['author_id' => 'id']],
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
            'author_id' => 'Author ID',
        ];
    }

    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(Authors::className(), ['id' => 'author_id']);
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


    public function fill($book_id, $author_id) {
        $this->book_id = $book_id;
        $this->author_id = $author_id;
    }

    public static function notExist($book_id, $author_id){

        $existBookAuthor = BookAuthor::find()
            ->andWhere([
                'book_id' => $book_id,
                'author_id' => $author_id
            ])->one();

        if(empty($existBookAuthor)){
            return true;
        } else {
            return false;
        }
    }
}
