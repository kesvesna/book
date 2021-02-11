<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;


$this->title = 'Парсер';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-parser">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>Скопируйте и вставьте этот адрес для проверки парсера</p>
    <p>https://gitlab.com/prog-positron/test-app-vacancy/-/raw/master/books.json</p>
    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'parser-form']); ?>

            <?= $form->field($model, 'parserSourceAddress')->textInput(['autofocus' => true,
                'placeholder'=>'https://source.com/file.json']) ?>

            <div class="form-group">
                <?= Html::submitButton('Распарсить', ['class' => 'btn btn-primary btn-block btn-lg',
                    'name' => 'parser-button', 'id' => 'parser-button', 'value'=>1]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>


    </div>


</div>

