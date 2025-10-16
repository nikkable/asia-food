<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use repositories\Category\models\Category;

/** @var yii\web\View $this */
/** @var repositories\Product\models\Product $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="product-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="row mb-3">
        <?= $form->field($model, 'category_id')->dropDownList(
            ArrayHelper::map(Category::find()->where(['status' => 1])->all(), 'id', 'name'),
            ['prompt' => 'Выберите категорию']
        ) ?>
    </div>
    <div class="row mb-3">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="row mb-3">
        <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
    </div>
    <div class="row mb-3">
        <?= $form->field($model, 'imageFile')->fileInput(['accept' => 'image/*']) ?>

        <?php if ($model->image): ?>
            <div class="form-group">
                <label class="control-label">Текущее изображение:</label><br>
                <img src="<?= $model->getImageUrl() ?>" alt="<?= Html::encode($model->name) ?>" style="max-width: 200px; max-height: 200px;">
            </div>
        <?php endif; ?>
    </div>
    <div class="row mb-3">
        <div class="col-4">
            <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'price_discount')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'quantity')->textInput() ?>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-4">
            <?= $form->field($model, 'article')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'status')->dropDownList([
                1 => 'Активный',
                0 => 'Неактивный'
            ], ['prompt' => 'Выберите статус']) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
