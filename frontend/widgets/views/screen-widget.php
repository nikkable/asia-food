<?php

use common\helpers\SvgHelper;
use yii\helpers\Url;

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