<?php
/** @var yii\web\View $this */
/** @var \frontend\models\OrderForm $model */
/** @var \context\Cart\models\Cart $cart */

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Оформление заказа';
$this->params['breadcrumbs'][] = ['label' => 'Корзина', 'url' => ['/cart/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="checkout-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-6">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'customer_name')->textInput() ?>
            <?= $form->field($model, 'customer_email')->textInput() ?>
            <?= $form->field($model, 'customer_phone')->textInput() ?>
            <?= $form->field($model, 'note')->textarea(['rows' => 3]) ?>

            <div class="form-group">
                <?= Html::submitButton('Подтвердить заказ', ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-md-6">
            <h3>Ваш заказ</h3>
            <table class="table">
                <?php foreach ($cart->getItems() as $item): ?>
                    <tr>
                        <td><?= Html::encode($item->getProduct()->name) ?> x <?= $item->getQuantity() ?></td>
                        <td class="text-right"><?= $item->getCost() ?> руб.</td>
                    </tr>
                <?php endforeach; ?>
                <tfoot>
                    <tr>
                        <td class="text-right"><strong>Итого:</strong></td>
                        <td class="text-right"><strong><?= $cart->getTotalCost() ?> руб.</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
