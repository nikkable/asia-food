<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use frontend\models\QuickOrderForm;

/** @var context\Cart\models\Cart $cart */

// Создаем модель для формы
$model = new QuickOrderForm();
?>

<!-- Кнопка корзины -->
<button type="button" class="cart-button" data-bs-toggle="modal" data-bs-target="#cartModal">
    <svg role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
        <path fill="none" stroke-width="2" stroke-miterlimit="10" d="M44 18h10v45H10V18h10z"></path>
        <path fill="none" stroke-width="2" stroke-miterlimit="10" d="M22 24V11c0-5.523 4.477-10 10-10s10 4.477 10 10v13"></path>
    </svg>
    <span class="cart-button-counter js-cart-counter">
        <?= $cart->getAmount() ?>
    </span>
</button>

<!-- Модалка корзины -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title" id="cartModalLabel">Мой заказ</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>

            <div class="modal-body">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Загрузка...</span>
                    </div>
                    <p class="mt-3">Загрузка корзины...</p>
                </div>
            </div>
        </div>
    </div>
</div>
