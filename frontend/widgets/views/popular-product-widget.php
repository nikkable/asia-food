<?php

use yii\helpers\Html;
use common\helpers\PriceHelper;

/** @var repositories\Product\models\Product $product */
/** @var string $title */
?>

<div class="popular">
    <div class="container">
        <div class="popular-main">
            <div class="popular-info">
                <div class="popular-title title"><?= Html::encode($title) ?></div>
                <div class="popular-name">
                    <?= Html::a(Html::encode($product->name), ['product/view', 'slug' => $product->slug], [
                        'class' => 'text-decoration-none text-dark'
                    ]) ?>
                </div>
                <div class="popular-price hidden">
                    <?php if ($product->price_discount): ?>
                        <span class="price-old"><?= PriceHelper::formatRub($product->price) ?></span>
                        <span class="price-new"><?= PriceHelper::formatRub($product->price_discount) ?></span>
                    <?php else: ?>
                        <span class="price"><?= PriceHelper::formatRub($product->price) ?></span>
                    <?php endif; ?>
                </div>
                <div class="popular-buttons">
                    <button class="but but-secondary but-big js-add-to-cart-btn"
                            data-product-id="<?= $product->id ?>"
                            data-product-name="<?= Html::encode($product->name) ?>">
                        Купить
                    </button>
                </div>
            </div>
            <div class="popular-image">
                <img src="<?= $product->getCroppedImageUrl(580, 360, 'fit') ?>" alt="<?= Html::encode($product->name) ?>">
            </div>
        </div>
    </div>
</div>
