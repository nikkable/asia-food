<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Контакты';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container">
    <div class="site-contacts">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h1 class="text-center mb-4"><?= Html::encode($this->title) ?></h1>
                
                <div class="contacts-content">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h3>Магазин</h3>
                                    <p><strong>Адрес:</strong><br>г. Оренбург, Харьковская ул., 127</p>
                                    <p><strong>Режим работы:</strong><br>
                                    Пн-Сб: 09:00-18:00<br>
                                    Вс: 09:00-15:00</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h3>Связь</h3>
                                    <p><strong>Телефон:</strong><br>+7 (3532) 123-456</p>
                                    <p><strong>WhatsApp:</strong><br>+7 (987) 654-32-10</p>
                                    <p><strong>Email:</strong><br>info@asia-food.ru</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h3>Социальные сети</h3>
                            <div class="social-links">
                                <a href="#" class="btn btn-secondary me-2 mb-2">
                                    Telegram
                                </a>
                                <a href="#" class="btn btn-secondary me-2 mb-2">
                                    WhatsApp
                                </a>
                                <a href="#" class="btn btn-secondary me-2 mb-2">
                                    ВКонтакте
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h3>Как нас найти</h3>
                            <p>Наш магазин находится в центре города Оренбурга, недалеко от основных транспортных узлов.</p>
                            <p><strong>Ближайшие остановки общественного транспорта:</strong></p>
                            <ul>
                                <li>Остановка "Харьковская" - 50 метров</li>
                                <li>Остановка "Центральная" - 200 метров</li>
                            </ul>
                            <p><strong>Парковка:</strong> Бесплатная парковка возле магазина на 10 мест.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
