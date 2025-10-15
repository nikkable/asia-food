<?php

use frontend\widgets\CategoryWidget;
use frontend\widgets\BestsellerWidget;
use frontend\widgets\MapWidget;
use frontend\widgets\PopularProductWidget;
use frontend\widgets\ReviewWidget;
use frontend\widgets\ScreenWidget;

/** @var yii\web\View $this */

$this->title = 'Главная';
?>

<?= ScreenWidget::widget() ?>

<?= CategoryWidget::widget() ?>

<?= PopularProductWidget::widget() ?>

<?= BestsellerWidget::widget() ?>

<?= ReviewWidget::widget() ?>

<?= MapWidget::widget() ?>

