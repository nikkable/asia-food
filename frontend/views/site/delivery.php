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
                            <p>Наша компания осуществляет регулярную доставку продуктов питания для кафе, ресторанов, магазинов и частных покупателей. Мы работаем с оптовыми и розничными заказами, обеспечивая свежесть и высокое качество товаров.</p>
                            <h4>Наши преимущества:</h4>
                            <ul>
                                <li>Бесплатная доставка по городу при заказе от 5 000 рублей.</li>
                                <li>Заказ до 11:00 — доставка в тот же день.</li>
                                <li>Воскресенье — только самовывоз по адресу: г. Оренбург, улица Харьковская, 127.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h3>График поставок</h3>
                            <ul>
                                <li><strong>Понедельник:</strong> Соль-Илецк</li>
                                <li><strong>Вторник:</strong> Октябрьское, Шарлык, Бузулук</li>
                                <li><strong>Среда:</strong> Саракташ</li>
                                <li><strong>Четверг:</strong> Соль-Илецк</li>
                                <li><strong>Пятница:</strong> Орск, Ясный</li>
                                <li><strong>Суббота:</strong> Бузулук, Первомайский</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <p>Заказывайте специи, соусы, морепродукты и другие продукты для HoReCa с доставкой в Оренбурге и области. Мы гарантируем удобные условия сотрудничества, выгодные цены и точное соблюдение графика.</p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h3>Самовывоз</h3>
                            <p><strong>Адрес:</strong> г. Оренбург, улица Харьковская, 127</p>
                            <p><strong>Воскресенье — только самовывоз.</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
