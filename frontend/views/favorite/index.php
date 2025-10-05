<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var \repositories\Favorite\models\FavoriteList $favorites */

$this->title = 'Избранное';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container py-5">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($favorites->getCount() > 0): ?>
        <div class="row">
            <?php foreach ($favorites->getItems() as $item): ?>
                <?php $product = $item->getProduct(); ?>
                <div class="col-md-4 mb-4 favorite-item" data-product-id="<?= $product->id ?>">
                    <div class="card h-100">
                        <?php if ($product->image): ?>
                            <img src="<?= $product->getImageUrl() ?>" class="card-img-top" alt="<?= Html::encode($product->name) ?>" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <span class="text-muted">Нет фото</span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h5 class="card-title"><?= Html::encode($product->name) ?></h5>
                            <p class="card-text text-truncate"><?= Html::encode($product->description) ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fs-5 fw-bold"><?= number_format($product->price, 0, ',', ' ') ?> руб.</span>
                                <span class="badge bg-<?= $product->quantity > 0 ? 'success' : 'danger' ?>">
                                    <?= $product->quantity > 0 ? 'В наличии' : 'Нет в наличии' ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="card-footer d-flex justify-content-between">
                            <a href="<?= Url::to(['/catalog/product', 'slug' => $product->slug]) ?>" class="btn btn-outline-primary">
                                Подробнее
                            </a>
                            <button class="btn btn-outline-danger remove-from-favorite" data-product-id="<?= $product->id ?>">
                                Удалить
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="mt-4">
            <button id="clear-favorites" class="btn btn-danger">Очистить избранное</button>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <p>В избранном пока нет товаров.</p>
            <a href="<?= Url::to(['/catalog']) ?>" class="btn btn-primary">Перейти в каталог</a>
        </div>
    <?php endif; ?>
</div>

<?php
$js = <<<JS
$(document).ready(function() {
    // Удаление товара из избранного
    $('.remove-from-favorite').on('click', function() {
        var productId = $(this).data('product-id');
        var item = $(this).closest('.favorite-item');
        
        $.ajax({
            url: '/favorite/remove',
            type: 'POST',
            data: {
                id: productId,
                '_csrf-frontend': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    item.fadeOut(300, function() {
                        $(this).remove();
                        
                        // Обновляем счетчик в шапке
                        $('.favorite-counter').text(response.favoritesCount);
                        
                        // Если больше нет товаров, показываем сообщение
                        if (response.favoritesCount === 0) {
                            $('.row').html('');
                            $('#clear-favorites').hide();
                            $('.container').append(
                                '<div class="alert alert-info">' +
                                '<p>В избранном пока нет товаров.</p>' +
                                '<a href="/catalog" class="btn btn-primary">Перейти в каталог</a>' +
                                '</div>'
                            );
                        }
                    });
                } else {
                    alert('Ошибка: ' + response.message);
                }
            },
            error: function() {
                alert('Произошла ошибка при удалении товара из избранного');
            }
        });
    });
    
    // Очистка всего избранного
    $('#clear-favorites').on('click', function() {
        if (confirm('Вы уверены, что хотите очистить список избранного?')) {
            $.ajax({
                url: '/favorite/clear',
                type: 'POST',
                data: {
                    '_csrf-frontend': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Обновляем счетчик в шапке
                        $('.favorite-counter').text('0');
                        
                        // Очищаем содержимое и показываем сообщение
                        $('.row').html('');
                        $('#clear-favorites').hide();
                        $('.container').append(
                            '<div class="alert alert-info">' +
                            '<p>В избранном пока нет товаров.</p>' +
                            '<a href="/catalog" class="btn btn-primary">Перейти в каталог</a>' +
                            '</div>'
                        );
                    } else {
                        alert('Ошибка: ' + response.message);
                    }
                },
                error: function() {
                    alert('Произошла ошибка при очистке избранного');
                }
            });
        }
    });
});
JS;

$this->registerJs($js);
?>
