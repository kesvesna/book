<?php

use common\models\Status;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model common\models\Book */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="book-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'isbn')->textInput() ?>

    <?= $form->field($model, 'page_count')->textInput() ?>

    <?= $form->field($model, 'published_date')->textInput() ?>

    <?= $form->field($model, 'thumbnail_url')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'short_description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'long_description')->textarea(['rows' => 6]) ?>

    <?php echo $form->field($model, 'status_id')->dropdownlist(ArrayHelper::map
    (Status::find()->asArray()->all(), 'id', 'name'),
    ['class' => 'form-control', 'prompt' => 'Выберите статус книги',
    ])->label(false); ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success btn-lg btn-block']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
