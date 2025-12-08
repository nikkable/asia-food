<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\helpers\PriceHelper;

/* @var $this yii\web\View */
/* @var $product repositories\Product\models\Product */
/* @var $isInFavorites bool */

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
                            'class' => 'img-fluid'
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
                        <?php if ($product->price_discount && $product->price_discount < $product->price): ?>
                            <span class="price-old text-muted text-decoration-line-through me-2">
                                <?= PriceHelper::format($product->price) ?>
                            </span>
                            <span class="price-current h3 text-danger fw-bold">
                                <?= PriceHelper::format($product->price_discount) ?>
                            </span>
                            <span class="badge bg-danger ms-2">
                                Скидка <?= round((($product->price - $product->price_discount) / $product->price) * 100) ?>%
                            </span>
                        <?php else: ?>
                            <span class="price-current h3 text-primary fw-bold">
                                <?= PriceHelper::format($product->price) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Артикул -->
                    <?php if ($product->article): ?>
                        <div class="product-sku mb-2">
                            <small class="text-muted">Артикул: <?= Html::encode($product->article) ?></small>
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

                    <!-- Кнопка добавления в корзину и избранное -->
                    <div class="product-actions">
                        <?php if ($product->quantity > 0): ?>
                            <button type="button" 
                                    class="btn btn-primary btn-lg js-add-to-cart-btn me-3"
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
                        
                        <?php
                        $favoriteButtonClasses = 'btn btn-outline-danger js-product-favorite';
                        if ($isInFavorites) {
                            $favoriteButtonClasses .= ' active';
                        }
                        ?>
                        <!-- Кнопка избранного -->
                        <button type="button"
                                class="<?= $favoriteButtonClasses ?>"
                                data-product-id="<?= $product->id ?>">
                            <i class="<?= $isInFavorites ? 'fas' : 'far' ?> fa-heart"></i>
                            <span><?= $isInFavorites ? 'В избранном' : 'В избранное' ?></span>
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
.product-view {
    padding: 40px 0 60px;
}

.product-view .container {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 18px 45px rgba(0, 0, 0, 0.08);
    padding: 30px 25px 35px;
}

@media (min-width: 992px) {
    .product-view .container {
        padding: 40px 45px 45px;
    }
}

.product-image {
    position: relative;
}

.product-image img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.product-title {
    color: #333;
    font-weight: 700;
    font-size: 1.9rem;
}

.product-price {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px 12px;
}

.price-old {
    font-size: 1.1rem;
}

.price-current {
    font-size: 1.9rem;
}

.product-sku small {
    letter-spacing: 0.03em;
}

.product-description h5 {
    font-weight: 600;
    margin-bottom: 0.75rem;
}

.product-description p {
    line-height: 1.6;
}

.product-actions {
    margin-top: 2rem;
}

.product-actions .btn-primary {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    border: none;
    padding: 0.7rem 1.8rem;
    border-radius: 999px;
    box-shadow: 0 10px 22px rgba(238, 90, 36, 0.35);
}

.product-actions .btn-primary:hover {
    background: linear-gradient(135deg, #ff7f7f 0%, #ff6a33 100%);
    box-shadow: 0 12px 26px rgba(238, 90, 36, 0.45);
}

.product-actions .btn-outline-danger {
    border-radius: 999px;
}

.product-actions .btn-secondary {
    border-radius: 999px;
}

.breadcrumb {
    background-color: transparent;
    padding: 0;
    margin-bottom: 1.5rem;
}

.breadcrumb a {
    color: #ff6b6b;
    text-decoration: none;
}

.breadcrumb a:hover {
    text-decoration: underline;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: #6c757d;
}

.card {
    border-radius: 18px;
    border: none;
    box-shadow: 0 10px 28px rgba(0, 0, 0, 0.06);
}

.card-header {
    border-bottom: none;
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    color: #fff;
    border-radius: 18px 18px 0 0;
}

.card-body {
    padding-top: 1.5rem;
}

@media (max-width: 768px) {
    .product-view {
        padding: 20px 0 40px;
    }

    .product-view .container {
        padding: 20px 18px 25px;
        border-radius: 16px;
    }

    .product-title {
        font-size: 1.5rem;
        text-align: center;
    }

    .product-actions {
        text-align: center;
    }
    
    .product-actions .btn {
        width: 100%;
        margin-bottom: 10px;
    }
}
</style>
