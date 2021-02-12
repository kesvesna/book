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
                'label' => 'Статус',
                'value' => 'status.name' ,
                'filter' => Html::activeDropDownList($searchModel, 'status_id', ArrayHelper::map
                (\common\models\Status::find()->asArray()->all(), 'id', 'name'),
                    ['class'=>'form-control','prompt' => 'Все']),
            ],

             /*[
                     'attribute' => 'authors',
                    'label' => 'Автор',
                    'value' => function($searchModel){
                        $authors_name = \common\models\BookAuthor::find()
                                    ->select(['authors.name'])
                                    ->andWhere(['book_id'=> $searchModel->id])
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
                     ],*/


             ['class' => 'yii\grid\ActionColumn',
            'template' => '{view}',
            //'visible' => (bool)$admin,
            ],
        ],
    ]); ?>

    </div>
</div>
