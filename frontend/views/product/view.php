<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\helpers\PriceHelper;

/* @var $this yii\web\View */
/* @var $product repositories\Product\models\Product */

$this->title = $product->name;
?>

<div class="product-view">
    <div class="container">
        <!-- Хлебные крошки -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <?= Html::a('Главная', ['site/index']) ?>
                </li>
                <li class="breadcrumb-item">
                    <?= Html::a('Каталог', ['catalog/index']) ?>
                </li>
                <?php if ($product->category): ?>
                    <li class="breadcrumb-item">
                        <?= Html::a($product->category->name, ['category/view', 'slug' => $product->category->slug]) ?>
                    </li>
                <?php endif; ?>
                <li class="breadcrumb-item active" aria-current="page">
                    <?= Html::encode($product->name) ?>
                </li>
            </ol>
        </nav>

        <div class="row">
            <!-- Изображение товара -->
            <div class="col-md-6">
                <div class="product-image">
                    <?php if ($product->image): ?>
                        <?= Html::img($product->getCroppedImageUrl(500, 400, 'fit'), [
                            'alt' => Html::encode($product->name),
                            'class' => 'img-fluid rounded shadow-sm'
                        ]) ?>
                    <?php else: ?>
                        <div class="no-image d-flex align-items-center justify-content-center bg-light rounded" style="height: 400px;">
                            <span class="text-muted">Нет изображения</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Информация о товаре -->
            <div class="col-md-6">
                <div class="product-info">
                    <h1 class="product-title mb-3"><?= Html::encode($product->name) ?></h1>
                    
                    <!-- Цена -->
                    <div class="product-price mb-3">
                        <?php if ($product->discount_price && $product->discount_price < $product->price): ?>
                            <span class="price-old text-muted text-decoration-line-through me-2">
                                <?= PriceHelper::format($product->price) ?>
                            </span>
                            <span class="price-current h3 text-danger fw-bold">
                                <?= PriceHelper::format($product->discount_price) ?>
                            </span>
                            <span class="badge bg-danger ms-2">
                                Скидка <?= round((($product->price - $product->discount_price) / $product->price) * 100) ?>%
                            </span>
                        <?php else: ?>
                            <span class="price-current h3 text-primary fw-bold">
                                <?= PriceHelper::format($product->price) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Артикул -->
                    <?php if ($product->sku): ?>
                        <div class="product-sku mb-2">
                            <small class="text-muted">Артикул: <?= Html::encode($product->sku) ?></small>
                        </div>
                    <?php endif; ?>

                    <!-- Наличие -->
                    <div class="product-availability mb-3">
                        <?php if ($product->quantity > 0): ?>
                            <span class="badge bg-success">В наличии (<?= $product->quantity ?> шт.)</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Нет в наличии</span>
                        <?php endif; ?>
                    </div>

                    <!-- Описание -->
                    <?php if ($product->description): ?>
                        <div class="product-description mb-4">
                            <h5>Описание</h5>
                            <p><?= Html::encode($product->description) ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Кнопка добавления в корзину -->
                    <div class="product-actions">
                        <?php if ($product->quantity > 0): ?>
                            <button type="button" 
                                    class="btn btn-primary btn-lg add-to-cart-btn me-3"
                                    data-product-id="<?= $product->id ?>"
                                    data-product-name="<?= Html::encode($product->name) ?>">
                                <i class="fas fa-shopping-cart me-2"></i>
                                В корзину
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-secondary btn-lg" disabled>
                                <i class="fas fa-times me-2"></i>
                                Нет в наличии
                            </button>
                        <?php endif; ?>
                        
                        <!-- Кнопка избранного (если есть функциональность) -->
                        <button type="button" class="btn btn-outline-danger">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Дополнительная информация -->
        <?php if ($product->description): ?>
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Подробное описание</h5>
                        </div>
                        <div class="card-body">
                            <?= nl2br(Html::encode($product->description)) ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Подключаем JavaScript для корзины -->
<?php
$this->registerJsFile('@web/js/add-to-cart.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>

<style>
.product-image img {
    width: 100%;
    height: auto;
    object-fit: cover;
}

.product-title {
    color: #333;
    font-weight: 600;
}

.price-old {
    font-size: 1.1rem;
}

.price-current {
    font-size: 1.8rem;
}

.product-actions {
    margin-top: 2rem;
}

.breadcrumb {
    background-color: transparent;
    padding: 0;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: #6c757d;
}

@media (max-width: 768px) {
    .product-actions {
        text-align: center;
    }
    
    .product-actions .btn {
        width: 100%;
        margin-bottom: 10px;
    }
}
</style>
