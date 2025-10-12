<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Согласие на рекламную рассылку';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container">
    <div class="site-advertising-consent">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h1 class="text-center mb-4"><?= Html::encode($this->title) ?></h1>
                
                <div class="advertising-content">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3>Согласие на получение рекламных материалов</h3>
                            <p>Настоящим я даю свое согласие ООО "АЗИЯ ФУД" на получение рекламных и информационных материалов о товарах и услугах компании.</p>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h3>Способы получения рекламной информации:</h3>
                            <ul>
                                <li>Электронная почта (email-рассылка)</li>
                                <li>SMS-сообщения</li>
                                <li>Мессенджеры (WhatsApp, Telegram)</li>
                                <li>Телефонные звонки</li>
                                <li>Push-уведомления в мобильном приложении</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h3>Виды рекламной информации:</h3>
                            <ul>
                                <li>Информация о новых товарах и услугах</li>
                                <li>Специальные предложения и акции</li>
                                <li>Персональные скидки и бонусы</li>
                                <li>Уведомления о распродажах</li>
                                <li>Приглашения на мероприятия</li>
                                <li>Новости компании</li>
                                <li>Сезонные предложения</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h3>Преимущества подписки на рассылку:</h3>
                            <ul>
                                <li>Первыми узнавайте о новых товарах</li>
                                <li>Получайте эксклюзивные предложения</li>
                                <li>Участвуйте в закрытых акциях</li>
                                <li>Получайте персональные скидки</li>
                                <li>Будьте в курсе всех событий</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h3>Отказ от рассылки</h3>
                            <p>Вы можете отказаться от получения рекламных материалов в любое время:</p>
                            <ul>
                                <li>Нажав ссылку "Отписаться" в любом рекламном письме</li>
                                <li>Отправив SMS с текстом "СТОП" на номер рассылки</li>
                                <li>Обратившись в службу поддержки по телефону или email</li>
                                <li>Изменив настройки в личном кабинете на сайте</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h3>Контакты для отказа от рассылки</h3>
                            <p><strong>Телефон:</strong> +7 (3532) 123-456</p>
                            <p><strong>Email:</strong> unsubscribe@asia-food.ru</p>
                            <p><strong>Время работы службы поддержки:</strong> Пн-Пт 09:00-18:00</p>
                            <p class="text-muted mt-3">Согласие на рекламную рассылку не является обязательным для совершения покупок. Отказ от рассылки не влияет на обработку заказов и предоставление услуг.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
