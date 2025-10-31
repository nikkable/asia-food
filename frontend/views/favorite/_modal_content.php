<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\helpers\PriceHelper;

/** @var \repositories\Favorite\models\FavoriteList $favorites */
?>

<?php if ($favorites->getCount() > 0): ?>
    <!-- Список товаров в избранном -->
    <div class="favorite-items mb-4">
        <?php foreach ($favorites->getItems() as $item): ?>
            <?php $product = $item->getProduct(); ?>
            <div class="favorite-item js-favorite-item" data-product-id="<?= $product->id ?>">
                <div class="favorite-item-image">
                    <img src="<?= $product->getCroppedImageUrl(200, 260, 'fit') ?>"
                         alt="<?= Html::encode($product->name) ?>"
                         style="width: 60px; height: 60px; object-fit: cover;">
                </div>

                <div class="favorite-item-info">
                    <a class="favorite-item-name" href="<?= Url::to(['/catalog/product', 'slug' => $product->slug]) ?>">
                        <?= Html::encode($product->name) ?>
                    </a>
                    <div class="favorite-item-price">
                        <?= PriceHelper::formatRub($product->price) ?>
                    </div>
                    <div class="favorite-item-quantity <?= $product->quantity > 0 ? 'color-green' : 'color-red' ?>">
                        <?= $product->quantity > 0 ? 'В наличии' : 'Нет в наличии' ?>
                    </div>
                </div>

                <div class="favorite-item-actions">
                    <button type="button" class="btn btn-three js-add-to-cart-btn mb-2"
                            data-product-id="<?= $product->id ?>"
                            data-product-name="<?= Html::encode($product->name) ?>">
                        В корзину
                    </button>
                    <button type="button" class="btn btn-secondary js-favorite-remove-btn" data-product-id="<?= $product->id ?>">
                        Удалить
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Кнопки действий -->
    <div class="d-flex justify-content-between">
        <button type="button" class="btn btn-secondary js-favorite-clear-btn">
            Очистить избранное
        </button>
    </div>

<?php else: ?>
    <!-- Пустое избранное -->
    <div class="text-center py-5">
        <div class="mb-4">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-muted">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h5 class="text-muted">В избранном пока нет товаров</h5>
        <p class="text-muted m-b-4">Добавьте товары в избранное, чтобы вернуться к ним позже</p>
        <button type="button" class="btn btn-three" data-bs-dismiss="modal">Продолжить покупки</button>
    </div>
<?php endif; ?>
