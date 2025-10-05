<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var string $title */
/** @var string $subtitle */
/** @var repositories\Product\models\Product[] $bestsellers */
?>

<section class="bestsellers-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><?= Html::encode($title) ?></h2>
            <?php if (!empty($subtitle)): ?>
                <p class="section-subtitle"><?= Html::encode($subtitle) ?></p>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($bestsellers)): ?>
            <div class="bestsellers-grid">
                <?php foreach ($bestsellers as $product): ?>
                    <div class="product">
                        <?php $isInFavorites = Yii::$container->get('context\\Favorite\\interfaces\\FavoriteServiceInterface')->isInFavorites($product->id); ?>
                        <button class="product-favorite js-product-favorite <?= $isInFavorites ? 'active' : '' ?>"
                                data-product-id="<?= $product->id ?>"
                                data-product-name="<?= Html::encode($product->name) ?>">
                            <svg width="21" height="18" viewBox="0 0 21 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 6.32647C20 11.4974 10.5 17 10.5 17C10.5 17 1 11.4974 1 6.32647C1 -0.694364 10.5 -0.599555 10.5 5.57947C10.5 -0.599555 20 -0.507124 20 6.32647Z" stroke="black" stroke-linejoin="round"></path>
                            </svg>
                        </button>
                        <div class="product-image">
                            <?php if ($product->image): ?>
                                <img src="<?= $product->getImageUrl() ?>" alt="<?= Html::encode($product->name) ?>">
                            <?php else: ?>
                                <img src="/images/products/default.png" alt="<?= Html::encode($product->name) ?>">
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <div class="product-price"><?= number_format($product->price, 0, ',', ' ') ?>р.</div>
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
                            <a href="<?= Url::to(['/catalog/product', 'slug' => $product->slug]) ?>" class="btn btn-secondary">Подробнее</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="bestsellers-empty">
                <p>В данный момент нет хитов продаж.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
