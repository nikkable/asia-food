<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use frontend\models\QuickOrderForm;

/** @var context\Cart\models\Cart $cart */

// Создаем модель для формы
$model = new QuickOrderForm();
?>

<?php if ($cart->getAmount() > 0): ?>
    <!-- Список товаров в корзине -->
    <div class="cart-items mb-4">
        <?php foreach ($cart->getItems() as $item): ?>
            <div class="cart-item d-flex align-items-center mb-3 p-3 border rounded" data-product-id="<?= $item->getProduct()->id ?>">
                <div class="cart-item-image me-3">
                    <?php if ($item->getProduct()->image): ?>
                        <img src="<?= $item->getProduct()->getImageUrl() ?>"
                             alt="<?= Html::encode($item->getProduct()->name) ?>"
                             style="width: 60px; height: 60px; object-fit: cover;">
                    <?php else: ?>
                        <div style="width: 60px; height: 60px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                            Нет фото
                        </div>
                    <?php endif; ?>
                </div>

                <div class="cart-item-info flex-grow-1">
                    <h6 class="mb-1">
                        <a href="<?= Url::to(['/catalog/product', 'slug' => $item->getProduct()->slug]) ?>">
                            <?= Html::encode($item->getProduct()->name) ?>
                        </a>
                    </h6>
                    <div class="cart-item-price text-muted">
                        <?= number_format($item->getProduct()->price, 0, ',', ' ') ?>р. × <?= $item->getQuantity() ?>
                    </div>
                    <div class="cart-item-total fw-bold">
                        = <?= number_format($item->getCost(), 0, ',', ' ') ?>р.
                    </div>
                </div>

                <div class="cart-item-actions">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary cart-quantity-btn"
                                data-action="decrease" data-product-id="<?= $item->getProduct()->id ?>">
                            −
                        </button>
                        <span class="btn disabled">
                            <?= $item->getQuantity() ?>
                        </span>
                        <button type="button" class="btn btn-outline-secondary cart-quantity-btn"
                                data-action="increase" data-product-id="<?= $item->getProduct()->id ?>">
                            +
                        </button>
                    </div>

                    <button type="button" class="btn btn-sm btn-outline-danger mt-2 cart-remove-btn"
                            data-product-id="<?= $item->getProduct()->id ?>">
                        Удалить
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Итого -->
    <div class="cart-total text-end mb-4">
        <h5>Итого: <strong><?= number_format($cart->getTotalCost(), 0, ',', ' ') ?>р.</strong></h5>
    </div>

    <!-- Форма быстрого заказа -->
    <div class="quick-order-form">
        <h6 class="mb-3">Быстрый заказ</h6>

        <?php $form = ActiveForm::begin([
            'id' => 'quick-order-form',
            'action' => Url::to(['/order/create']),
            'options' => ['class' => 'row g-3']
        ]); ?>

        <div class="col-md-6">
            <?= $form->field($model, 'customerName', [
                'inputOptions' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ваше имя'
                ]
            ])->label('Имя *') ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'customerPhone', [
                'inputOptions' => [
                    'class' => 'form-control',
                    'placeholder' => '+7 (999) 123-45-67'
                ]
            ])->label('Телефон *') ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'customerEmail', [
                'inputOptions' => [
                    'class' => 'form-control',
                    'placeholder' => 'email@example.com'
                ]
            ])->label('Email') ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'deliveryAddress', [
                'inputOptions' => [
                    'class' => 'form-control',
                    'placeholder' => 'г. Оренбург, ул. Харьковская, 127'
                ]
            ])->label('Адрес доставки') ?>
        </div>
        
        <div class="col-12">
            <?= $form->field($model, 'paymentMethod')->radioList(
                $model->getPaymentMethodOptions(),
                [
                    'item' => function($index, $label, $name, $checked, $value) {
                        $checked = $checked ? 'checked' : '';
                        $return = '<div class="form-check mb-2">';
                        $return .= '<input class="form-check-input" type="radio" name="' . $name . '" value="' . $value . '" id="' . $value . '" ' . $checked . '>';
                        $return .= '<label class="form-check-label" for="' . $value . '">' . $label . '</label>';
                        $return .= '</div>';
                        return $return;
                    },
                    'unselect' => null,
                ]
            ) ?>
        </div>

        <div class="col-12">
            <?= $form->field($model, 'orderComment', [
                'inputOptions' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Комментарий к заказу'
                ]
            ])->textarea()->label('Комментарий') ?>
        </div>

        <div class="col-12 text-center">
            <button type="submit" class="btn btn-success btn-lg">
                Оформить заказ на <?= number_format($cart->getTotalCost(), 0, ',', ' ') ?>р.
            </button>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

<?php else: ?>
    <!-- Пустая корзина -->
    <div class="text-center py-5">
        <div class="mb-4">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-muted">
                <path d="M3 3H5L5.4 5M7 13H17L21 5H5.4M7 13L5.4 5M7 13L4.7 15.3C4.3 15.7 4.6 16.4 5.1 16.4H17M17 13V17C17 18.1 16.1 19 15 19H9C7.9 19 7 18.1 7 17V13H17ZM9 21C9.6 21 10 21.4 10 22S9.6 23 9 23 8 22.6 8 22 8.4 21 9 21ZM20 21C20.6 21 21 21.4 21 22S20.6 23 20 23 19 22.6 19 22 19.4 21 20 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h5 class="text-muted">Корзина пуста</h5>
        <p class="text-muted">Добавьте товары в корзину, чтобы оформить заказ</p>
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Продолжить покупки</button>
    </div>
<?php endif; ?>
