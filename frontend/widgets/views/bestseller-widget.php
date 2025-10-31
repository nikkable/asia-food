<?php

use common\helpers\SvgHelper;
use context\Favorite\interfaces\FavoriteServiceInterface;
use yii\helpers\Html;
use yii\helpers\Url;
use common\helpers\PriceHelper;

/** @var string $title */
/** @var string $subtitle */
/** @var FavoriteServiceInterface $favoriteService */
/** @var repositories\Product\models\Product[] $bestsellers */
?>

<section class="bestsellers">
    <div class="container">
        <div class="bestsellers-head">
            <div class="bestsellers-title title"><?= Html::encode($title) ?></div>
            <?php if (!empty($subtitle)): ?>
                <div class="undertitle"><?= Html::encode($subtitle) ?></div>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($bestsellers)): ?>
            <div class="bestsellers-main">
                <?php foreach ($bestsellers as $product): ?>
                    <div class="product">
                        <?php $isInFavorites = $favoriteService->isInFavorites($product->id); ?>
                        <button class="product-favorite js-product-favorite <?= $isInFavorites ? 'active' : '' ?>"
                                data-product-id="<?= $product->id ?>"
                                data-product-name="<?= Html::encode($product->name) ?>">
                            <?= SvgHelper::getIcon('favorite'); ?>
                        </button>
                        <div class="product-image">
                            <img src="<?= $product->getImageUrl() ?>" alt="<?= Html::encode($product->name) ?>">
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
                                <button class="but but-three js-add-to-cart-btn"
                                        data-product-id="<?= $product->id ?>"
                                        data-product-name="<?= Html::encode($product->name) ?>">
                                    В корзину
                                </button>
                            <?php endif; ?>
                            <!--
                            <a href="<?= Url::to(['/catalog/product', 'slug' => $product->slug]) ?>" class="but but-secondary">Подробнее</a>
                            -->
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
