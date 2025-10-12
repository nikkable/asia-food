<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'О доставке';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container">
    <div class="site-delivery">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h1 class="text-center mb-4"><?= Html::encode($this->title) ?></h1>
                
                <div class="delivery-content">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3>Условия доставки</h3>
                            <ul>
                                <li>Бесплатная доставка при заказе от 1000 рублей</li>
                                <li>При заказе менее 1000 рублей - стоимость доставки 200 рублей</li>
                                <li>Доставка осуществляется в пределах города Оренбурга</li>
                                <li>Время доставки: от 30 минут до 2 часов</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h3>Зоны доставки</h3>
                            <p>Мы доставляем по всему городу Оренбургу, включая:</p>
                            <ul>
                                <li>Центральный район</li>
                                <li>Дзержинский район</li>
                                <li>Ленинский район</li>
                                <li>Промышленный район</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h3>Способы оплаты</h3>
                            <ul>
                                <li>Наличными курьеру</li>
                                <li>Банковской картой курьеру</li>
                                <li>Онлайн-оплата на сайте</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h3>Самовывоз</h3>
                            <p><strong>Адрес:</strong> г. Оренбург, Харьковская ул., 127</p>
                            <p><strong>Режим работы:</strong></p>
                            <ul>
                                <li>Пн-Сб: 09:00-18:00</li>
                                <li>Вс: 09:00-15:00</li>
                            </ul>
                            <p>При самовывозе скидка 5% на весь заказ!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
