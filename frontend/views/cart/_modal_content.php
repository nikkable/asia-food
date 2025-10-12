<?php

use common\helpers\PriceHelper;
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
            <div class="cart-item js-cart-item" data-product-id="<?= $item->getProduct()->id ?>">
                <div class="cart-item-image">
                    <?php if ($item->getProduct()->image): ?>
                        <img src="<?= $item->getProduct()->getImageUrl() ?>"
                             alt="<?= Html::encode($item->getProduct()->name) ?>"
                             style="width: 60px; height: 60px; object-fit: cover;">
                    <?php else: ?>
                        <div style="width: 60px; height: 60px; background: #f8f9fa; border-radius: 10px; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-size: 12px;">
                            Фото
                        </div>
                    <?php endif; ?>
                </div>

                <div class="cart-item-info flex-grow-1">
                    <div class="cart-item-name">
                        <a href="<?= Url::to(['/catalog/product', 'slug' => $item->getProduct()->slug]) ?>">
                            <?= Html::encode($item->getProduct()->name) ?>
                        </a>
                    </div>
                    <div class="cart-item-info-price">
                        <div class="cart-item-price">
                            <?= PriceHelper::formatRub($item->getProduct()->price) ?> × <?= $item->getQuantity() ?>
                        </div>
                        <div class="cart-item-total js-cart-item-total">
                            = <?= PriceHelper::formatRub($item->getCost()) ?>
                        </div>
                    </div>
                </div>

                <div class="cart-item-actions">
                    <div class="cart-item-quantity">
                        <button type="button" class="btn js-cart-quantity-btn" data-action="decrease" data-product-id="<?= $item->getProduct()->id ?>">
                            <svg width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve">
                                <g><line fill="none" stroke="#000000" stroke-width="2" stroke-miterlimit="10" x1="14" y1="31" x2="50" y2="31"/></g>
                                <g><circle fill="none" stroke="#000000" stroke-width="2" stroke-miterlimit="10" cx="32" cy="32" r="30.999"/></g>
                            </svg>
                        </button>
                        <span class="btn disabled">
                            <?= $item->getQuantity() ?>
                        </span>
                        <button type="button" class="btn js-cart-quantity-btn" data-action="increase" data-product-id="<?= $item->getProduct()->id ?>">
                            <svg width="16px" height="16px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve">
                                <g><line fill="none" stroke="#000000" stroke-width="2" stroke-miterlimit="10" x1="32" y1="50" x2="32" y2="14"/><line fill="none" stroke="#000000" stroke-width="2" stroke-miterlimit="10" x1="14" y1="32" x2="50" y2="32"/></g>
                                <g><circle fill="none" stroke="#000000" stroke-width="2" stroke-miterlimit="10" cx="32" cy="32" r="30.999"/></g>
                            </svg>
                        </button>
                    </div>

                    <div class="cart-item-remove">
                        <button type="button" class="btn js-cart-remove-btn" data-product-id="<?= $item->getProduct()->id ?>">
                            <svg width="20px" height="20px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve">
                                <g><line fill="none" stroke="#000000" stroke-width="2" stroke-miterlimit="10" x1="18.947" y1="17.153" x2="45.045" y2="43.056"/></g>
                                <g><line fill="none" stroke="#000000" stroke-width="2" stroke-miterlimit="10" x1="19.045" y1="43.153" x2="44.947" y2="17.056"/></g>
                                <g><circle fill="none" stroke="#000000" stroke-width="2" stroke-miterlimit="10" cx="32" cy="32" r="30.999"/></g>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Итого -->
    <div class="cart-total js-cart-total m-b-4">
        <div>Сумма: <strong><?= PriceHelper::formatRub($cart->getTotalCost()) ?></strong></div>
    </div>

    <!-- Форма быстрого заказа -->
    <div class="quick-order-form">
        <?php $form = ActiveForm::begin([
            'id' => 'quick-order-form',
            'action' => Url::to(['/order/create']),
            'options' => ['class' => 'row g-3']
        ]); ?>

        <div class="col-md-6">
            <?= $form->field($model, 'customerName', [
                'inputOptions' => [
                    'class' => 'field-text',
                    'placeholder' => 'Ваше имя'
                ],
                'options' => ['class' => 'form-group'],
            ])->label('Имя *') ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'customerPhone', [
                'inputOptions' => [
                    'class' => 'field-text',
                    'placeholder' => '+7 (999) 123-45-67'
                ],
                'options' => ['class' => 'form-group'],
            ])->label('Телефон *') ?>
        </div>

        <div class="col-md-6">
            <div class="form-group mb-3">
                <?= $form->field($model, 'customerEmail', [
                    'inputOptions' => [
                        'class' => 'field-text',
                        'placeholder' => 'email@example.com'
                    ],
                    'options' => ['class' => 'form-group'],
                ])->label('Email') ?>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group mb-3">
                <?= $form->field($model, 'deliveryAddress', [
                    'inputOptions' => [
                        'class' => 'field-text',
                        'placeholder' => 'г. Оренбург, ул. Харьковская, 127'
                    ],
                    'options' => ['class' => 'form-group'],
                ])->label('Адрес доставки') ?>
            </div>
        </div>
        
        <div class="col-12">
            <div class="form-group">
                <?= $form->field($model, 'paymentMethod', [
                    'options' => ['class' => 'payment-method-group'],
                ])->radioList(
                    $model->getPaymentMethodOptions(),
                    [
                        'item' => function($index, $label, $name, $checked, $value) {
                            $checked = $checked ? 'checked' : '';
                            $return = '<div class="field-radio">';
                            $return .= '<input class="form-check-input" type="radio" name="' . $name . '" value="' . $value . '" id="' . $value . '" ' . $checked . '>';
                            $return .= '<label class="form-check-label" for="' . $value . '">' . $label . '</label>';
                            $return .= '</div>';
                            return $return;
                        },
                        'unselect' => null,
                    ]
                ) ?>
            </div>
        </div>

        <div class="col-12">
            <div class="form-group mb-3">
                <?= $form->field($model, 'orderComment', [
                    'inputOptions' => [
                        'class' => 'field-textarea',
                        'rows' => 3,
                        'placeholder' => 'Комментарий к заказу'
                    ],
                    'options' => ['class' => 'form-group'],
                ])->textarea()->label('Комментарий') ?>
            </div>
        </div>

        <div class="col-12 text-center">
            <button type="submit" class="btn btn-three btn-big">
                Оформить заказ
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
        <p class="text-muted m-b-4">Добавьте товары в корзину, чтобы оформить заказ</p>
        <button type="button" class="btn btn-three" data-bs-dismiss="modal">Продолжить покупки</button>
    </div>
<?php endif; ?>
