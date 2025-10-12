<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Сотрудничество';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container">
    <div class="site-cooperation">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h1 class="text-center mb-4"><?= Html::encode($this->title) ?></h1>
                
                <div class="cooperation-content">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3>Предложения для партнеров</h3>
                            <p>Мы открыты для различных форм сотрудничества и всегда рады новым партнерам!</p>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h3>Поставщики продуктов</h3>
                            <p>Если вы производитель или поставщик качественных продуктов питания, мы готовы рассмотреть возможность сотрудничества.</p>
                            <ul>
                                <li>Качественная продукция с сертификатами</li>
                                <li>Стабильные поставки</li>
                                <li>Конкурентные цены</li>
                                <li>Гибкие условия оплаты</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h3>Корпоративные клиенты</h3>
                            <p>Предлагаем специальные условия для организаций:</p>
                            <ul>
                                <li>Корпоративные скидки</li>
                                <li>Безналичная оплата</li>
                                <li>Регулярные поставки</li>
                                <li>Индивидуальный подход</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h3>Франшиза</h3>
                            <p>Рассматриваем возможность развития сети под нашим брендом в других городах.</p>
                            <p>Условия франшизы обсуждаются индивидуально.</p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h3>Контакты для партнеров</h3>
                            <p><strong>Email:</strong> partners@asia-food.ru</p>
                            <p><strong>Телефон:</strong> +7 (3532) 123-456</p>
                            <p><strong>Адрес:</strong> г. Оренбург, Харьковская ул., 127</p>
                            <p class="mt-3">Отправьте нам свое предложение, и мы обязательно рассмотрим его!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
