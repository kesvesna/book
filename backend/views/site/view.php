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
    <br>
    <div class="row">
        <div class="col-xs-12">
            <?php if($admin){
            echo Html::a('Добавить книгу', ['create', ], ['class' => 'btn btn-success btn-block btn-lg']);}?>
        </div>
    </div>
    <br>
    <div class="row">
    <div class="col-xs-6">
        <?php if($admin){
            echo Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-block btn-lg']);?>
    </div>
    <div class="col-xs-6">
            <?php echo Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger btn-block btn-lg',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]);
        }  ?>
    </div>
    </div>
    <br>


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
                'headerOptions' => ['width' => '100'],
            ],

        ],
    ]) ?>

</div>
