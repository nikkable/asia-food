<?php

use common\helpers\PriceHelper;
use frontend\widgets\HeaderCategoriesWidget;
use yii\helpers\Html;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var repositories\Category\models\Category $category */
/** @var repositories\Product\models\Product[] $products */
/** @var int $currentPage */
/** @var int $totalPages */
/** @var int $pageSize */
/** @var int $totalProducts */

$this->title = $category->name;
$this->params['breadcrumbs'][] = ['label' => 'Каталог', 'url' => ['/catalog/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="category-page">
    <div class="container">
        <div class="category-page-head">
            <h1><?= Html::encode($category->name) ?></h1>
            <?php if ($category->description): ?>
                <div class="category-description">
                    <?= Html::encode($category->description) ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="category-page-main">
            <div class="category-page-categories">
                <ul>
                    <?= HeaderCategoriesWidget::widget(); ?>
                </ul>
            </div>
            <div class="category-page-products <?= empty($products) ? 'category-page-products--empty' : '' ?>">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product">
                            <?php $isInFavorites = Yii::$container->get('context\\Favorite\\interfaces\\FavoriteServiceInterface')->isInFavorites($product->id); ?>
                            <button class="product-favorite add-to-favorite <?= $isInFavorites ? 'active' : '' ?>"
                                    data-product-id="<?= $product->id ?>"
                                    data-product-name="<?= Html::encode($product->name) ?>">
                                <span><?= $isInFavorites ? 'В избранном' : 'В избранное' ?></span>
                            </button>
                            <div class="product-image">
                                <?php if ($product->image): ?>
                                    <img src="<?= $product->getImageUrl() ?>" alt="<?= Html::encode($product->name) ?>">
                                <?php else: ?>
                                    <img src="/images/products/default.png" alt="<?= Html::encode($product->name) ?>">
                                <?php endif; ?>
                            </div>
                            <div class="product-info">
                                <div class="product-price"><?= PriceHelper::formatRub($product->price) ?></div>
                                <div class="product-quantity">
                                    <?= $product->quantity > 0 ? 'В наличии' : 'Нет в наличии' ?>
                                </div>
                                <div class="product-name"><?= Html::encode($product->name) ?></div>
                                <?php if ($product->description): ?>
                                    <div class="product-desc"><?= Html::encode($product->description) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="product-buttons">
                                <?php if ($product->quantity > 0): ?>
                                    <button class="btn btn-three add-to-cart-btn"
                                            data-product-id="<?= $product->id ?>"
                                            data-product-name="<?= Html::encode($product->name) ?>">
                                        В корзину
                                    </button>
                                <?php endif; ?>
                                <button class="btn btn-secondary">Подробнее</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <!-- Пагинация -->
                    <div class="pagination-container">
                        <?= LinkPager::widget([
                            'pagination' => new \yii\data\Pagination([
                                'totalCount' => $totalProducts,
                                'pageSize' => $pageSize,
                                'page' => $currentPage - 1,
                                'pageSizeParam' => false,
                            ]),
                            'options' => ['class' => 'pagination'],
                            'linkOptions' => ['class' => 'page-link'],
                            'activePageCssClass' => 'active',
                            'disabledPageCssClass' => 'disabled',
                            'prevPageLabel' => '&laquo;',
                            'nextPageLabel' => '&raquo;',
                        ]) ?>
                    </div>
                <?php else: ?>
                    <p>В данной категории пока нет товаров.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
use yii\helpers\Url;

$this->registerJs("
$(document).ready(function() {
    $('.add-to-cart-btn').on('click', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var productId = button.data('product-id');
        var productName = button.data('product-name');
        var originalText = button.text();
        
        // Блокируем кнопку на время запроса
        button.prop('disabled', true).text('Добавляем...');
        
        $.ajax({
            url: '" . Url::to(['/cart/add']) . "' + '?id=' + productId,
            type: 'POST',
            data: {
                quantity: 1,
                '_csrf-frontend': $('meta[name=\"csrf-token\"]').attr('content')
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Показываем успешное сообщение
                    button.text('Добавлено!').removeClass('btn-three').addClass('btn-success');
                    
                    // Обновляем счетчик корзины если есть
                    if ($('.js-cart-counter').length) {
                        $('.js-cart-counter').text(response.cartAmount);
                    }
                    
                    // Показываем уведомление
                    showNotification('Товар \"' + productName + '\" добавлен в корзину', 'success');
                    
                    // Возвращаем кнопку в исходное состояние через 2 секунды
                    setTimeout(function() {
                        button.text(originalText).removeClass('btn-success').addClass('btn-three').prop('disabled', false);
                    }, 2000);
                } else {
                    button.text(originalText).prop('disabled', false);
                    showNotification(response.message || 'Ошибка при добавлении товара', 'error');
                }
            },
            error: function() {
                button.text(originalText).prop('disabled', false);
                showNotification('Произошла ошибка. Попробуйте еще раз.', 'error');
            }
        });
    });
});

// Функция для показа уведомлений
function showNotification(message, type) {
    var notification = $('<div class=\"notification notification-' + type + '\">' + message + '</div>');
    
    // Добавляем стили если их нет
    if (!$('#notification-styles').length) {
        $('head').append('<style id=\"notification-styles\">' +
            '.notification { position: fixed; top: 20px; right: 20px; padding: 15px 20px; border-radius: 5px; color: white; z-index: 9999; max-width: 300px; }' +
            '.notification-success { background-color: #28a745; }' +
            '.notification-error { background-color: #dc3545; }' +
            '.notification-fade-in { animation: fadeIn 0.3s ease-in; }' +
            '.notification-fade-out { animation: fadeOut 0.3s ease-out; }' +
            '@keyframes fadeIn { from { opacity: 0; transform: translateX(100%); } to { opacity: 1; transform: translateX(0); } }' +
            '@keyframes fadeOut { from { opacity: 1; transform: translateX(0); } to { opacity: 0; transform: translateX(100%); } }' +
            '</style>');
    }
    
    notification.addClass('notification-fade-in');
    $('body').append(notification);
    
    // Автоматически скрываем через 3 секунды
    setTimeout(function() {
        notification.addClass('notification-fade-out');
        setTimeout(function() {
            notification.remove();
        }, 300);
    }, 3000);
}
");
?>
