<?php
/** @var yii\web\View $this */
/** @var \context\Cart\models\Cart $cart */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Корзина';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container">
    <div class="cart-index">
        <h1><?= Html::encode($this->title) ?></h1>

        <?php if (!empty($cart->getItems())): ?>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Товар</th>
                    <th>Цена</th>
                    <th>Количество</th>
                    <th>Сумма</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($cart->getItems() as $item): ?>
                    <tr>
                        <td><?= Html::a(Html::encode($item->getProduct()->name), ['/catalog/product', 'slug' => $item->getProduct()->slug]) ?></td>
                        <td><?= $item->getPrice() ?> руб.</td>
                        <td><?= $item->getQuantity() ?></td>
                        <td><?= $item->getCost() ?> руб.</td>
                        <td><?= Html::a('×', ['/cart/remove', 'id' => $item->getId()], ['class' => 'btn btn-danger', 'data-method' => 'post']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" class="text-right"><strong>Итого:</strong></td>
                    <td colspan="2"><strong><?= $cart->getTotalCost() ?> руб.</strong></td>
                </tr>
                </tbody>
            </table>
            <p>
                <?= Html::a('Очистить корзину', ['/cart/clear'], ['class' => 'btn btn-primary', 'data-method' => 'post']) ?>
                <?= Html::a('Оформить заказ', ['/checkout/index'], ['class' => 'btn btn-secondary']) ?>
            </p>
        <?php else: ?>
            <p>Ваша корзина пуста.</p>
        <?php endif; ?>
    </div>
</div>

