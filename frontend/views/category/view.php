<?php

use common\helpers\PriceHelper;
use common\helpers\SvgHelper;
use frontend\widgets\HeaderCategoriesWidget;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\helpers\Url;

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
                                <?= SvgHelper::getIcon('favorite'); ?>
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
                                <div class="product-quantity <?= $product->quantity > 0 ? 'product-quantity--yes' : '' ?>">
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

                                <!--
                                <button class="btn btn-secondary">Подробнее</button>
                                -->
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <!-- Пагинация -->
                    <div class="pagination-container" style="width: 100%;">
                        <?= LinkPager::widget([
                            'pagination' => new Pagination([
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
