<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var repositories\Order\models\Order $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row mb-3">
        <?= $form->field($model, 'user_id')->textInput() ?>
    </div>
    <div class="row mb-3">
        <?= $form->field($model, 'customer_name')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="row mb-3">
        <?= $form->field($model, 'customer_email')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="row mb-3">
        <?= $form->field($model, 'customer_phone')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="row mb-3">
        <?= $form->field($model, 'total_cost')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="row mb-3">
        <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>
    </div>
    <div class="row mb-3">
        <?= $form->field($model, 'status')->textInput() ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
