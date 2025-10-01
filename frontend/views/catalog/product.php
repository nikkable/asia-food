<?php
/** @var yii\web\View $this */
/** @var \repositories\Product\models\Product $product */

use yii\helpers\Html;

$this->title = $product->name;
$this->params['breadcrumbs'][] = ['label' => 'Каталог', 'url' => ['/catalog/index']];
$this->params['breadcrumbs'][] = ['label' => $product->category->name, 'url' => ['/catalog/category', 'slug' => $product->category->slug]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-catalog-product">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-8">
            <p><?= Html::encode($product->description) ?></p>
            <p><strong>Цена:</strong> <?= Html::encode($product->price) ?> руб.</p>
            <p><strong>Артикул:</strong> <?= Html::encode($product->article) ?></p>
        </div>
    </div>
</div>
