<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "book".
 *
 * @property int $id
 * @property string $title
 * @property int $isbn
 * @property int|null $page_count
 * @property string|null $published_date
 * @property string|null $thumbnail_url
 * @property string|null $short_description
 * @property string|null $long_description
 * @property int $status_id
 *
 * @property Status $status
 * @property BookAuthor[] $bookAuthors
 * @property BookCategory[] $bookCategories
 */
class Book extends \yii\db\ActiveRecord
{

    public $picture = "";
    public $authors = [];
    public $categories = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'book';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'status_id'], 'required'],
            [['page_count', 'status_id'], 'integer'],
            [['published_date'], 'safe'],
            [['thumbnail_url', 'short_description', 'long_description', 'isbn'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => Status::className(), 'targetAttribute' => ['status_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'isbn' => 'Артикул',
            'page_count' => 'Страниц',
            'published_date' => 'Дата опубликования',
            'thumbnail_url' => 'Изображение',
            'short_description' => 'Краткое описание',
            'long_description' => 'Полное описание',
            'status_id' => 'Статус',
        ];
    }

    /**
     * Gets query for [[Status]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(Status::className(), ['id' => 'status_id']);
    }

    /**
     * Gets query for [[BookAuthors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBookAuthors()
    {
        return $this->hasMany(BookAuthor::className(), ['book_id' => 'id']);
    }

    /**
     * Gets query for [[BookCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBookCategories()
    {
        return $this->hasMany(BookCategory::className(), ['book_id' => 'id']);
    }

    public function getAuthors()
    {
        return $this->hasMany(Authors::className(), ['id' => 'author_id'])
            ->viaTable('book_author', ['book_id' => 'id']);
    }

    public static function notExist ($params){

        $existBook = Book::find()
            ->andWhere([
                'isbn' => $params['isbn'],
                'title' => $params['title'],
                'published_date' => $params['publishedDate']['$date']
            ])->one();

        if($existBook){
            return true;
        } else {
            return false;
        }
    }


    public function fill($params = []){

        if(!empty($params)){
            $this->title = $params['title'];
            $this->isbn = $params['isbn'];
            $this->published_date = $params['publishedDate']['$date'];
            $this->short_description = $params['shortDescription'];
            $this->long_description = $params['longDescription'];
            $this->page_count = $params['pageCount'];
            $this->thumbnail_url = $params['thumbnailUrl'];
        } else {
            $this->title = 'Нет данных';
            $this->isbn = 'Нет данных';
            $this->published_date = '0000-00-00 00:00:00';
            $this->short_description = 'Нет данных';
            $this->long_description = 'Нет данных';
            $this->page_count = 0;
            $this->thumbnail_url = 'Нет данных';
        }

    }
}
