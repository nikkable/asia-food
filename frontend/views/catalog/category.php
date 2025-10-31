<?php
/** @var yii\web\View $this */
/** @var \repositories\Category\models\Category $category */
/** @var array $products */

use yii\helpers\Html;
use common\helpers\PriceHelper;

$this->title = $category->name;
$this->params['breadcrumbs'][] = ['label' => 'Каталог', 'url' => ['/catalog/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container">
    <div class="site-catalog-category">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-lg-4">
                    <h2><?= Html::encode($product->name) ?></h2>
                    <p><?= PriceHelper::formatRub($product->price) ?></p>
                    <p><?= Html::a('Подробнее &raquo;', ['/catalog/product', 'slug' => $product->slug], ['class' => 'but btn-outline-secondary']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

