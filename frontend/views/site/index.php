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
                'attribute' => 'category_id',
                'headerOptions' => ['width' => '100'],
                'value' => 'category.name',
            ],

            [

                'attribute' => 'isbn',
                'headerOptions' => ['width' => '100'],
                'value' => 'isbn' ,
            ],

            [
                'attribute' => 'status_id',
                'headerOptions' => ['width' => '100'],
                'value' => 'status.name',
            ],






           /* [
                // 'attribute' => 'town_id',
                'attribute' => 'system_id',
                // 'headerOptions' => ['width' => '180'],
                // $trk = Trk::findOne('trk_id'),
               // 'visible'=>(bool)$user->sadmin,
                'value' => 'system.name' ,
                'filter' => Html::activeDropDownList($searchModel, 'system_id', ArrayHelper::map
                (\common\models\System::find()->asArray()->all(), 'id', 'name'),
                    ['class'=>'form-control','prompt' => 'Все']),
            ],*/

            /* [
                // 'attribute' => 'town_id',
                'attribute' => 'equipment_id',
                // 'headerOptions' => ['width' => '180'],
                // $trk = Trk::findOne('trk_id'),
               // 'visible'=>(bool)$user->sadmin,
               'filter'=>false,
                'value' => 'equipment.name' ,
               // 'filter' => Html::activeDropDownList($searchModel, 'system_id', ArrayHelper::map
               // (\common\models\System::find()->asArray()->all(), 'id', 'name'),
                 //   ['class'=>'form-control','prompt' => 'Все']),
            ],*/

             ['class' => 'yii\grid\ActionColumn',
            'template' => '{view}',
            //'visible' => (bool)$admin,
            ],
        ],
    ]); ?>

    </div>
</div>
