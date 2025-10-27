<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Контакты';

// Подключаем FontAwesome для иконок
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

// Подключаем CSS для страницы контактов
$this->registerCssFile('@web/css/contact.css', ['depends' => [\yii\web\YiiAsset::class]]);
?>

<div class="contact">
    <div class="container">
        <div class="contact-head">
            <div class="title">Контакты</div>
            <div class="subtitle">Свяжитесь с нами любым удобным способом</div>
        </div>
        
        <div class="contact-main">
            <div class="row">
                <!-- Контактная информация -->
                <div class="col-lg-6 mb-4">
                    <div class="contact-info">
                        <div class="contact-info-item">
                            <div class="contact-info-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-info-content">
                                <h4>Адрес</h4>
                                <p>г. Оренбург, ул. Харьковская, д. 127</p>
                            </div>
                        </div>
                        
                        <div class="contact-info-item">
                            <div class="contact-info-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="contact-info-content">
                                <h4>Телефон</h4>
                                <p><a href="tel:+79228111503">+7 922 811 15 03</a></p>
                            </div>
                        </div>
                        
                        <div class="contact-info-item">
                            <div class="contact-info-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-info-content">
                                <h4>Email</h4>
                                <p><a href="mailto:albekovn@bk.ru">albekovn@bk.ru</a></p>
                            </div>
                        </div>
                        
                        <div class="contact-info-item">
                            <div class="contact-info-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="contact-info-content">
                                <h4>Режим работы</h4>
                                <p>Ежедневно с 10:00 до 22:00</p>
                            </div>
                        </div>
                        
                        <div class="contact-info-item">
                            <div class="contact-info-icon">
                                <i class="fas fa-share-alt"></i>
                            </div>
                            <div class="contact-info-content">
                                <h4>Мы в соцсетях</h4>
                                <div class="social-links">
                                    <a href="#" class="social-link" title="ВКонтакте">
                                        <i class="fab fa-vk"></i>
                                    </a>
                                    <a href="#" class="social-link" title="Telegram">
                                        <i class="fab fa-telegram"></i>
                                    </a>
                                    <a href="#" class="social-link" title="WhatsApp">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Реквизиты компании -->
                <div class="col-lg-6 mb-4">
                    <div class="contact-company">
                        <h3 class="mb-4">ООО «Азия Фуд»</h3>
                        
                        <div class="company-details">
                            <div class="company-detail-item">
                                <span class="label">ИНН:</span>
                                <span class="value">5610243853</span>
                            </div>
                            
                            <div class="company-detail-item">
                                <span class="label">ОГРН:</span>
                                <span class="value">1215600031001</span>
                            </div>
                            
                            <div class="company-detail-item">
                                <span class="label">КПП:</span>
                                <span class="value">561001001</span>
                            </div>
                            
                            <div class="company-detail-item">
                                <span class="label">Расчетный счет:</span>
                                <span class="value">40702810946000013167</span>
                            </div>
                            
                            <div class="company-detail-item">
                                <span class="label">Банк:</span>
                                <span class="value">ОРЕНБУРГСКОЕ ОТДЕЛЕНИЕ N8623 ПАО СБЕРБАНК</span>
                            </div>
                            
                            <div class="company-detail-item">
                                <span class="label">БИК:</span>
                                <span class="value">045354601</span>
                            </div>
                            
                            <div class="company-detail-item">
                                <span class="label">Корр. счет:</span>
                                <span class="value">30101810600000000601</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Преимущества -->
        <div class="contact-features">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <h4>Быстрая доставка</h4>
                        <p>Доставляем свежие суши и роллы в течение 60 минут по всему городу</p>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <h4>Свежие продукты</h4>
                        <p>Используем только свежие ингредиенты и готовим каждый заказ индивидуально</p>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-award"></i>
                        </div>
                        <h4>Качество гарантировано</h4>
                        <p>Более 5 лет опыта в приготовлении азиатской кухни. Довольные клиенты - наша гордость</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Карта -->
        <div class="contact-map">
            <div class="contact-map-head">
                <div class="title">Как нас найти</div>
            </div>
            <div class="contact-map-main">
                <div style="position:relative;overflow:hidden;border-radius: 30px;">
                    <a href="https://yandex.ru/maps/org/aziya_fud/84393507382/?utm_medium=mapframe&utm_source=maps" 
                       style="color:#eee;font-size:12px;position:absolute;top:0px;">Азия ФУД</a>
                    <a href="https://yandex.ru/maps/48/orenburg/category/sushi_and_rolls_store/68190731095/?utm_medium=mapframe&utm_source=maps" 
                       style="color:#eee;font-size:12px;position:absolute;top:14px;">Магазин суши и роллов в Оренбурге</a>
                    <iframe src="https://yandex.ru/map-widget/v1/org/aziya_fud/84393507382/?indoorLevel=1&ll=55.140021%2C51.780471&z=17" 
                            width="100%" height="400" frameborder="1" allowfullscreen="true" style="position:relative;"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
