<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "book_picture".
 *
 * @property int $id
 * @property string|null $picture_file_path
 * @property int $book_id
 *
 * @property Book $book
 */
class BookPicture extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'book_picture';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['book_id'], 'required'],
            [['book_id'], 'integer'],
            [['picture_file_path'], 'string', 'max' => 255],
            [['book_id'], 'exist', 'skipOnError' => true, 'targetClass' => Book::className(), 'targetAttribute' => ['book_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'picture_file_path' => 'Picture File Path',
            'book_id' => 'Book ID',
        ];
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

    public function fill($book_id, $picture_path){
        $this->book_id = $book_id;
        $this->picture_file_path = $picture_path;
    }
}
