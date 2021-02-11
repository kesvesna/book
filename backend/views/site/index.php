<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
Yii::$app->language = 'ru-RU';
$this->title = 'Книги';
?>
<div class="site-index">

    <div class="body-content">

        <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

             [
             'attribute' => 'id',
             'headerOptions' => ['width' => '60'],
             'value' => 'id' ,
            ],

            [

             'attribute' => 'title',
             'headerOptions' => ['width' => '100'],
             'value' => 'title' ,
            ],


            [

                'attribute' => 'isbn',
                'headerOptions' => ['width' => '100'],
                'value' => 'isbn' ,
            ],

            [

                'attribute' => 'status_id',
                'value' => 'status.name' ,
                'filter' => Html::activeDropDownList($searchModel, 'status_id', ArrayHelper::map
                (\common\models\Status::find()->asArray()->all(), 'id', 'name'),
                    ['class'=>'form-control','prompt' => 'Все']),
            ],


             ['class' => 'yii\grid\ActionColumn',
            'template' => '{view}',
            //'visible' => (bool)$admin,
            ],
        ],
    ]); ?>

    </div>
</div>
