<?php

use frontend\widgets\CategoryWidget;
use frontend\widgets\BestsellerWidget;
use frontend\widgets\PopularProductWidget;
use common\helpers\SvgHelper;
use yii\helpers\Url;

/** @var yii\web\View $this */

$this->title = 'Главная';
?>

<div class="screen">
    <div class="container">
        <div class="screen-main js-screen-main">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="screen-item">
                        <img src="/images/screen/1.png" alt="">
                        <div class="screen-item-buttons">
                            <a href="<?= Url::to(['/catalog/index']) ?>" class="btn btn-primary">Каталог</a>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="screen-item">
                        <img src="/images/screen/2.png" alt="">
                        <div class="screen-item-buttons">
                            <a href="<?= Url::to(['/catalog/index']) ?>" class="btn btn-primary">Каталог</a>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="screen-item">
                        <img src="/images/screen/3.png" alt="">
                        <div class="screen-item-buttons">
                            <a href="<?= Url::to(['/catalog/index']) ?>" class="btn btn-primary">Каталог</a>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="screen-item">
                        <img src="/images/screen/4.png" alt="">
                        <div class="screen-item-buttons">
                            <a href="<?= Url::to(['/catalog/index']) ?>" class="btn btn-primary">Каталог</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="slider-prev js-slider-prev"><?= SvgHelper::getIcon('arrow-slider'); ?></div>
            <div class="slider-next js-slider-next"><?= SvgHelper::getIcon('arrow-slider'); ?></div>
            <div class="slider-dots js-slider-dots"></div>
        </div>
    </div>
</div>

<?= CategoryWidget::widget() ?>

<?= PopularProductWidget::widget([
    'slug' => 'sous-poke-tamaki-047-ml6stup',
    'title' => 'Популярное',
    'imagePath' => '/images/popular/1.png'
]) ?>

<?= BestsellerWidget::widget([
    'title' => 'Хиты продаж',
    'subtitle' => 'Самые популярные товары нашего магазина',
    'limit' => 20
]) ?>

<!--
<div class="review">
    <div class="container">
        <div class="review-head">
            <div class="title"></div>
        </div>
        <div class="review-main">
            <div class="review-info">
                <div class="review-rating">
                    <div class="review-rating-value">4.3</div>
                    <div class="review-rating-star">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.52447 2.71365C9.67415 2.25299 10.3259 2.25299 10.4755 2.71365L12.0656 7.60737H17.2112C17.6955 7.60737 17.8969 8.22718 17.5051 8.51188L13.3422 11.5364L14.9323 16.4301C15.0819 16.8907 14.5547 17.2738 14.1628 16.9891L10 13.9646L5.83715 16.9891C5.4453 17.2738 4.91806 16.8907 5.06773 16.4301L6.6578 11.5364L2.49495 8.51188C2.10309 8.22718 2.30448 7.60737 2.78884 7.60737H7.93441L9.52447 2.71365Z" fill="currentColor"></path></svg>
                    </div>
                    <div class="review-rating-hr">|</div>
                    <div class="review-rating-text">3 отзыва</div>
                </div>
                <div class="review-yandex">Яндекс 4.3</div>
            </div>
            <div class="review-items">
                <div class="review-item">
                    <div class="review-item-user">
                        <div class="review-item-user-avatar"><img src="https://myreviews.dev/widget/dist/media/yandex-color-icon.svg" alt=""></div>
                        <div class="review-item-user-info">
                            <div class="review-item-user-name">Павел</div>
                            <div class="review-item-user-date">11 июня на Яндекс</div>
                        </div>
                    </div>
                    <div class="review-item-rating">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.52447 2.71365C9.67415 2.25299 10.3259 2.25299 10.4755 2.71365L12.0656 7.60737H17.2112C17.6955 7.60737 17.8969 8.22718 17.5051 8.51188L13.3422 11.5364L14.9323 16.4301C15.0819 16.8907 14.5547 17.2738 14.1628 16.9891L10 13.9646L5.83715 16.9891C5.4453 17.2738 4.91806 16.8907 5.06773 16.4301L6.6578 11.5364L2.49495 8.51188C2.10309 8.22718 2.30448 7.60737 2.78884 7.60737H7.93441L9.52447 2.71365Z" fill="currentColor"></path></svg>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.52447 2.71365C9.67415 2.25299 10.3259 2.25299 10.4755 2.71365L12.0656 7.60737H17.2112C17.6955 7.60737 17.8969 8.22718 17.5051 8.51188L13.3422 11.5364L14.9323 16.4301C15.0819 16.8907 14.5547 17.2738 14.1628 16.9891L10 13.9646L5.83715 16.9891C5.4453 17.2738 4.91806 16.8907 5.06773 16.4301L6.6578 11.5364L2.49495 8.51188C2.10309 8.22718 2.30448 7.60737 2.78884 7.60737H7.93441L9.52447 2.71365Z" fill="currentColor"></path></svg>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.52447 2.71365C9.67415 2.25299 10.3259 2.25299 10.4755 2.71365L12.0656 7.60737H17.2112C17.6955 7.60737 17.8969 8.22718 17.5051 8.51188L13.3422 11.5364L14.9323 16.4301C15.0819 16.8907 14.5547 17.2738 14.1628 16.9891L10 13.9646L5.83715 16.9891C5.4453 17.2738 4.91806 16.8907 5.06773 16.4301L6.6578 11.5364L2.49495 8.51188C2.10309 8.22718 2.30448 7.60737 2.78884 7.60737H7.93441L9.52447 2.71365Z" fill="currentColor"></path></svg>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.52447 2.71365C9.67415 2.25299 10.3259 2.25299 10.4755 2.71365L12.0656 7.60737H17.2112C17.6955 7.60737 17.8969 8.22718 17.5051 8.51188L13.3422 11.5364L14.9323 16.4301C15.0819 16.8907 14.5547 17.2738 14.1628 16.9891L10 13.9646L5.83715 16.9891C5.4453 17.2738 4.91806 16.8907 5.06773 16.4301L6.6578 11.5364L2.49495 8.51188C2.10309 8.22718 2.30448 7.60737 2.78884 7.60737H7.93441L9.52447 2.71365Z" fill="currentColor"></path></svg>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.52447 2.71365C9.67415 2.25299 10.3259 2.25299 10.4755 2.71365L12.0656 7.60737H17.2112C17.6955 7.60737 17.8969 8.22718 17.5051 8.51188L13.3422 11.5364L14.9323 16.4301C15.0819 16.8907 14.5547 17.2738 14.1628 16.9891L10 13.9646L5.83715 16.9891C5.4453 17.2738 4.91806 16.8907 5.06773 16.4301L6.6578 11.5364L2.49495 8.51188C2.10309 8.22718 2.30448 7.60737 2.78884 7.60737H7.93441L9.52447 2.71365Z" fill="currentColor"></path></svg>
                    </div>
                    <div class="review-item-desc">
                        Лучшее место
                    </div>
                </div>
                <div class="review-item">
                    <div class="review-item-user">
                        <div class="review-item-user-avatar"><img src="https://myreviews.dev/widget/dist/media/yandex-color-icon.svg" alt=""></div>
                        <div class="review-item-user-info">
                            <div class="review-item-user-name">Павел</div>
                            <div class="review-item-user-date">11 июня на Яндекс</div>
                        </div>
                    </div>
                    <div class="review-item-rating">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.52447 2.71365C9.67415 2.25299 10.3259 2.25299 10.4755 2.71365L12.0656 7.60737H17.2112C17.6955 7.60737 17.8969 8.22718 17.5051 8.51188L13.3422 11.5364L14.9323 16.4301C15.0819 16.8907 14.5547 17.2738 14.1628 16.9891L10 13.9646L5.83715 16.9891C5.4453 17.2738 4.91806 16.8907 5.06773 16.4301L6.6578 11.5364L2.49495 8.51188C2.10309 8.22718 2.30448 7.60737 2.78884 7.60737H7.93441L9.52447 2.71365Z" fill="currentColor"></path></svg>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.52447 2.71365C9.67415 2.25299 10.3259 2.25299 10.4755 2.71365L12.0656 7.60737H17.2112C17.6955 7.60737 17.8969 8.22718 17.5051 8.51188L13.3422 11.5364L14.9323 16.4301C15.0819 16.8907 14.5547 17.2738 14.1628 16.9891L10 13.9646L5.83715 16.9891C5.4453 17.2738 4.91806 16.8907 5.06773 16.4301L6.6578 11.5364L2.49495 8.51188C2.10309 8.22718 2.30448 7.60737 2.78884 7.60737H7.93441L9.52447 2.71365Z" fill="currentColor"></path></svg>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.52447 2.71365C9.67415 2.25299 10.3259 2.25299 10.4755 2.71365L12.0656 7.60737H17.2112C17.6955 7.60737 17.8969 8.22718 17.5051 8.51188L13.3422 11.5364L14.9323 16.4301C15.0819 16.8907 14.5547 17.2738 14.1628 16.9891L10 13.9646L5.83715 16.9891C5.4453 17.2738 4.91806 16.8907 5.06773 16.4301L6.6578 11.5364L2.49495 8.51188C2.10309 8.22718 2.30448 7.60737 2.78884 7.60737H7.93441L9.52447 2.71365Z" fill="currentColor"></path></svg>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.52447 2.71365C9.67415 2.25299 10.3259 2.25299 10.4755 2.71365L12.0656 7.60737H17.2112C17.6955 7.60737 17.8969 8.22718 17.5051 8.51188L13.3422 11.5364L14.9323 16.4301C15.0819 16.8907 14.5547 17.2738 14.1628 16.9891L10 13.9646L5.83715 16.9891C5.4453 17.2738 4.91806 16.8907 5.06773 16.4301L6.6578 11.5364L2.49495 8.51188C2.10309 8.22718 2.30448 7.60737 2.78884 7.60737H7.93441L9.52447 2.71365Z" fill="currentColor"></path></svg>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.52447 2.71365C9.67415 2.25299 10.3259 2.25299 10.4755 2.71365L12.0656 7.60737H17.2112C17.6955 7.60737 17.8969 8.22718 17.5051 8.51188L13.3422 11.5364L14.9323 16.4301C15.0819 16.8907 14.5547 17.2738 14.1628 16.9891L10 13.9646L5.83715 16.9891C5.4453 17.2738 4.91806 16.8907 5.06773 16.4301L6.6578 11.5364L2.49495 8.51188C2.10309 8.22718 2.30448 7.60737 2.78884 7.60737H7.93441L9.52447 2.71365Z" fill="currentColor"></path></svg>
                    </div>
                    <div class="review-item-desc">
                        Лучшее место
                    </div>
                </div>
                <div class="review-item">
                    <div class="review-item-user">
                        <div class="review-item-user-avatar"><img src="https://myreviews.dev/widget/dist/media/yandex-color-icon.svg" alt=""></div>
                        <div class="review-item-user-info">
                            <div class="review-item-user-name">Павел</div>
                            <div class="review-item-user-date">11 июня на Яндекс</div>
                        </div>
                    </div>
                    <div class="review-item-rating">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.52447 2.71365C9.67415 2.25299 10.3259 2.25299 10.4755 2.71365L12.0656 7.60737H17.2112C17.6955 7.60737 17.8969 8.22718 17.5051 8.51188L13.3422 11.5364L14.9323 16.4301C15.0819 16.8907 14.5547 17.2738 14.1628 16.9891L10 13.9646L5.83715 16.9891C5.4453 17.2738 4.91806 16.8907 5.06773 16.4301L6.6578 11.5364L2.49495 8.51188C2.10309 8.22718 2.30448 7.60737 2.78884 7.60737H7.93441L9.52447 2.71365Z" fill="currentColor"></path></svg>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.52447 2.71365C9.67415 2.25299 10.3259 2.25299 10.4755 2.71365L12.0656 7.60737H17.2112C17.6955 7.60737 17.8969 8.22718 17.5051 8.51188L13.3422 11.5364L14.9323 16.4301C15.0819 16.8907 14.5547 17.2738 14.1628 16.9891L10 13.9646L5.83715 16.9891C5.4453 17.2738 4.91806 16.8907 5.06773 16.4301L6.6578 11.5364L2.49495 8.51188C2.10309 8.22718 2.30448 7.60737 2.78884 7.60737H7.93441L9.52447 2.71365Z" fill="currentColor"></path></svg>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.52447 2.71365C9.67415 2.25299 10.3259 2.25299 10.4755 2.71365L12.0656 7.60737H17.2112C17.6955 7.60737 17.8969 8.22718 17.5051 8.51188L13.3422 11.5364L14.9323 16.4301C15.0819 16.8907 14.5547 17.2738 14.1628 16.9891L10 13.9646L5.83715 16.9891C5.4453 17.2738 4.91806 16.8907 5.06773 16.4301L6.6578 11.5364L2.49495 8.51188C2.10309 8.22718 2.30448 7.60737 2.78884 7.60737H7.93441L9.52447 2.71365Z" fill="currentColor"></path></svg>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.52447 2.71365C9.67415 2.25299 10.3259 2.25299 10.4755 2.71365L12.0656 7.60737H17.2112C17.6955 7.60737 17.8969 8.22718 17.5051 8.51188L13.3422 11.5364L14.9323 16.4301C15.0819 16.8907 14.5547 17.2738 14.1628 16.9891L10 13.9646L5.83715 16.9891C5.4453 17.2738 4.91806 16.8907 5.06773 16.4301L6.6578 11.5364L2.49495 8.51188C2.10309 8.22718 2.30448 7.60737 2.78884 7.60737H7.93441L9.52447 2.71365Z" fill="currentColor"></path></svg>
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.52447 2.71365C9.67415 2.25299 10.3259 2.25299 10.4755 2.71365L12.0656 7.60737H17.2112C17.6955 7.60737 17.8969 8.22718 17.5051 8.51188L13.3422 11.5364L14.9323 16.4301C15.0819 16.8907 14.5547 17.2738 14.1628 16.9891L10 13.9646L5.83715 16.9891C5.4453 17.2738 4.91806 16.8907 5.06773 16.4301L6.6578 11.5364L2.49495 8.51188C2.10309 8.22718 2.30448 7.60737 2.78884 7.60737H7.93441L9.52447 2.71365Z" fill="currentColor"></path></svg>
                    </div>
                    <div class="review-item-desc">
                        Лучшее место
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
-->

<div class="review">
    <div class="container">
        <div class="review-head">
            <div class="title">Отзывы</div>
        </div>
        <div class="review-main">
            <div style="overflow:hidden;position:relative;"><iframe style="width:100%;height:100%;border:1px solid #e6e6e6;border-radius:30px;box-sizing:border-box" src="https://yandex.ru/maps-reviews-widget/84393507382?comments"></iframe><a href="https://yandex.ru/maps/org/aziya_fud/84393507382/" target="_blank" style="box-sizing:border-box;text-decoration:none;color:#b3b3b3;font-size:10px;font-family:YS Text,sans-serif;padding:0 20px;position:absolute;bottom:8px;width:100%;text-align:center;left:0;overflow:hidden;text-overflow:ellipsis;display:block;max-height:14px;white-space:nowrap;padding:0 16px;box-sizing:border-box">Азия ФУД на карте Оренбурга — Яндекс Карты</a></div>
        </div>
    </div>
</div>

<div class="map">
    <div class="container">
        <div class="map-head">
            <div class="title">Адрес для самовывоза</div>
        </div>
        <div class="map-main">
            <div style="position:relative;overflow:hidden;border-radius: 30px;"><a href="https://yandex.ru/maps/org/aziya_fud/84393507382/?utm_medium=mapframe&utm_source=maps" style="color:#eee;font-size:12px;position:absolute;top:0px;">Азия ФУД</a><a href="https://yandex.ru/maps/48/orenburg/category/sushi_and_rolls_store/68190731095/?utm_medium=mapframe&utm_source=maps" style="color:#eee;font-size:12px;position:absolute;top:14px;">Магазин суши и роллов в Оренбурге</a><iframe src="https://yandex.ru/map-widget/v1/org/aziya_fud/84393507382/?indoorLevel=1&ll=55.140021%2C51.780471&z=17" width="100%" height="400" frameborder="1" allowfullscreen="true" style="position:relative;"></iframe></div>
        </div>
    </div>
</div>

