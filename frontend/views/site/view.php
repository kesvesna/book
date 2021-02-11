<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Book */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Books', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="book-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
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
