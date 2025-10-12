<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\helpers\PriceHelper;

/** @var yii\web\View $this */
/** @var array $products */
/** @var string $query */

?>

<div class="catalog">
    <div class="container">
        <div class="catalog-info">
            <h1 class="catalog-title">Результаты поиска: "<?= Html::encode($query) ?>"</h1>
            <p class="catalog-desc">
                Найдено товаров: <?= count($products) ?>
            </p>
        </div>

        <?php if (empty($products)): ?>
            <div class="empty-results">
                <h3>По вашему запросу ничего не найдено</h3>
                <p>Попробуйте изменить поисковый запрос или <a href="<?= Url::to(['/catalog/index']) ?>">просмотрите весь каталог</a></p>
            </div>
        <?php else: ?>
            <div class="catalog-products">
                <?php foreach ($products as $product): ?>
                    <div class="catalog-products-item">
                        <a href="<?= Url::to(['/catalog/product', 'slug' => $product->slug]) ?>" class="catalog-products-item-link">
                            <div class="catalog-products-item-image">
                                <img src="<?= $product->image ? $product->getImageUrl() : '/images/products/default.png' ?>" alt="<?= Html::encode($product->name) ?>">
                            </div>
                            <div class="catalog-products-item-info">
                                <div class="catalog-products-item-name"><?= Html::encode($product->name) ?></div>
                                <div class="catalog-products-item-price"><?= PriceHelper::formatRub($product->price) ?></div>
                                <?php if ($product->quantity > 0): ?>
                                    <div class="catalog-products-item-stock in-stock">В наличии</div>
                                <?php else: ?>
                                    <div class="catalog-products-item-stock out-of-stock">Нет в наличии</div>
                                <?php endif; ?>
                            </div>
                        </a>
                        <?php if ($product->quantity > 0): ?>
                            <button class="btn btn-primary add-to-cart-btn" 
                                    data-product-id="<?= $product->id ?>" 
                                    data-product-name="<?= Html::encode($product->name) ?>">
                                В корзину
                            </button>
                        <?php else: ?>
                            <button class="btn btn-secondary" disabled>Нет в наличии</button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
