<?php

use common\helpers\PriceHelper;
use frontend\widgets\HeaderCategoriesWidget;
use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var repositories\Category\models\Category[] $categories */
/** @var repositories\Product\models\Product[] $products */
/** @var int $currentPage */
/** @var int $totalPages */
/** @var int $pageSize */
/** @var int $totalProducts */

$this->title = 'Каталог';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="category-page">
    <div class="container">
        <div class="category-page-head">
            <h1><?= Html::encode($this->title) ?></h1>
            <div class="category-description">
                Все товары нашего магазина
            </div>
        </div>

        <div class="category-page-main">
            <div class="category-page-categories">
                <ul>
                    <li><a href="<?= Url::to(['/catalog/index']) ?>">Все</a></li>
                    <?= HeaderCategoriesWidget::widget(); ?>
                </ul>
            </div>
            <div class="category-page-products <?= empty($products) ? 'category-page-products--empty' : '' ?>">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
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
                    <p>В каталоге пока нет товаров.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
