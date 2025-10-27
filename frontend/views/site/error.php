<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\helpers\Html;

$this->title = $name;

// Подключаем FontAwesome для иконок
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

// Подключаем CSS для страницы ошибки
$this->registerCssFile('@web/css/error-page.css', ['depends' => [\yii\web\YiiAsset::class]]);

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
            <div class="title"><?= $errorTitle ?></div>
            <div class="subtitle"><?= $errorSubtitle ?></div>
        </div>
        
        <div class="error-page-main">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Детали ошибки -->
                    <div class="error-details">
                        <div class="error-message">
                            <h4>Подробности:</h4>
                            <div class="message-content">
                                <?= nl2br(Html::encode($message)) ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Что можно сделать -->
                    <div class="error-suggestions">
                        <h4>Что можно сделать?</h4>
                        <div class="suggestions-list">
                            <?php if (strpos($name, '404') !== false): ?>
                                <div class="suggestion-item">
                                    <div class="suggestion-icon">
                                        <i class="fas fa-home"></i>
                                    </div>
                                    <div class="suggestion-text">
                                        <strong>Вернуться на главную</strong>
                                        <p>Перейдите на главную страницу нашего сайта</p>
                                    </div>
                                </div>
                                
                                <div class="suggestion-item">
                                    <div class="suggestion-icon">
                                        <i class="fas fa-search"></i>
                                    </div>
                                    <div class="suggestion-text">
                                        <strong>Воспользоваться поиском</strong>
                                        <p>Найдите нужную информацию через поиск по сайту</p>
                                    </div>
                                </div>
                                
                                <div class="suggestion-item">
                                    <div class="suggestion-icon">
                                        <i class="fas fa-utensils"></i>
                                    </div>
                                    <div class="suggestion-text">
                                        <strong>Посмотреть каталог</strong>
                                        <p>Ознакомьтесь с нашим ассортиментом блюд</p>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="suggestion-item">
                                    <div class="suggestion-icon">
                                        <i class="fas fa-redo"></i>
                                    </div>
                                    <div class="suggestion-text">
                                        <strong>Обновить страницу</strong>
                                        <p>Попробуйте перезагрузить страницу</p>
                                    </div>
                                </div>
                                
                                <div class="suggestion-item">
                                    <div class="suggestion-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="suggestion-text">
                                        <strong>Попробовать позже</strong>
                                        <p>Возможно, проблема временная</p>
                                    </div>
                                </div>
                                
                                <div class="suggestion-item">
                                    <div class="suggestion-icon">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                    <div class="suggestion-text">
                                        <strong>Связаться с нами</strong>
                                        <p>Сообщите нам о проблеме</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Действия -->
                    <div class="error-actions">
                        <div class="actions-buttons">
                            <?= Html::a('<i class="fas fa-home"></i> На главную', ['/'], [
                                'class' => 'btn btn-primary btn-lg'
                            ]) ?>
                            
                            <?= Html::a('<i class="fas fa-utensils"></i> Каталог', ['/catalog'], [
                                'class' => 'btn btn-outline-primary btn-lg'
                            ]) ?>
                            
                            <?= Html::a('<i class="fas fa-phone"></i> Связаться с нами', ['/contact'], [
                                'class' => 'btn btn-outline-secondary btn-lg'
                            ]) ?>
                        </div>
                    </div>
                    
                    <!-- Помощь -->
                    <div class="help-section">
                        <div class="help-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div class="help-content">
                            <h5>Нужна помощь?</h5>
                            <p>Если проблема повторяется, свяжитесь с нашей службой поддержки</p>
                            <div class="help-contacts">
                                <a href="tel:+79228111503" class="help-contact">
                                    <i class="fas fa-phone"></i>
                                    +7 922 811 15 03
                                </a>
                                <a href="mailto:albekovn@bk.ru" class="help-contact">
                                    <i class="fas fa-envelope"></i>
                                    albekovn@bk.ru
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

