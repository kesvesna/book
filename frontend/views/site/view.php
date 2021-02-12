<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Book */
Yii::$app->language = 'ru-RU';
$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Books', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="book-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?//= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?//= Html::a('Delete', ['delete', 'id' => $model->id], [
          //  'class' => 'btn btn-danger',
          //  'data' => [
           //     'confirm' => 'Are you sure you want to delete this item?',
          //      'method' => 'post',
           // ],
        //]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'isbn',
            'page_count',
            'published_date',

            [
                'attribute' => 'authors',
                'label' => 'Автор',
                'value' => function($model){
                    $authors_name = \common\models\BookAuthor::find()
                        ->select(['authors.name'])
                        ->andWhere(['book_id'=> $model->id])
                        ->join('inner join', 'authors',
                            'authors.id = author_id', [])
                        ->asArray()
                        ->all();
                    $authors_string = '';
                    foreach ($authors_name as $value) {
                        $authors_string .= $value['name'].', ';
                    }
                    $authors_string = rtrim($authors_string,', ');
                    return $authors_string;
                },
            ],

           [
                'attribute' => 'categories',
                'label' => 'Категория',
                'value' => function($model){
                    $categories_name = \common\models\BookCategory::find()
                        ->select(['category.name'])
                        ->andWhere(['book_id'=> $model->id])
                        ->join('inner join', 'category',
                            'category.id = category_id', [])
                        ->asArray()
                        ->all();
                    $categories_string = '';
                    foreach ($categories_name as $value) {
                        $categories_string .= $value['name'].', ';
                    }
                    $categories_string = rtrim($categories_string,', ');
                    return $categories_string;
                },
            ],

            [
                'attribute' => 'thumbnail_url',
                'label' => 'Обложка книги',
                'format' => 'raw',
                'visible' => (!empty($model->thumbnail_url) ? true : false),
                'value'=> Html::img($model->thumbnail_url, ['alt' => 'Обложка книги']),
            ],

            'short_description:ntext',
            'long_description:ntext',

            [
                'attribute' => 'status.name',
                'label' => 'Статус',
                'headerOptions' => ['width' => '100'],
            ],

        ],
    ]) ?>

</div>
