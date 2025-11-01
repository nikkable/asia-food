<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\helpers\Html;

$this->title = $name;

// Подключаем FontAwesome для иконок
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

// Определяем тип ошибки для иконки
$errorCode = $name;
$errorIcon = 'fas fa-exclamation-triangle';
$errorColor = '#ff6b6b';

if (strpos($name, '404') !== false) {
    $errorIcon = 'fas fa-search';
    $errorTitle = 'Страница не найдена';
    $errorSubtitle = 'К сожалению, запрашиваемая страница не существует';
} elseif (strpos($name, '403') !== false) {
    $errorIcon = 'fas fa-ban';
    $errorTitle = 'Доступ запрещен';
    $errorSubtitle = 'У вас нет прав для просмотра этой страницы';
} elseif (strpos($name, '500') !== false) {
    $errorIcon = 'fas fa-server';
    $errorTitle = 'Ошибка сервера';
    $errorSubtitle = 'Произошла внутренняя ошибка сервера';
} else {
    $errorTitle = Html::encode($this->title);
    $errorSubtitle = 'Произошла непредвиденная ошибка';
}
?>

<div class="error-page">
    <div class="container">
        <div class="error-page-head">
            <div class="error-icon">
                <i class="<?= $errorIcon ?>"></i>
            </div>
            <div class="error-code"><?= Html::encode($name) ?></div>
            <div class="title" style="color: white;"><?= $errorTitle ?></div>
            <div class="subtitle"><?= $errorSubtitle ?></div>
        </div>
    </div>
</div>

